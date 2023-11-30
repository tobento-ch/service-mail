<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Mail\Test\Queue;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\Queue\QueueHandler;
use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\Mail\Queue\MailJobHandler;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Queue\Queues;
use Tobento\Service\Queue\InMemoryQueue;
use Tobento\Service\Queue\JobProcessor;
use Tobento\Service\Queue\Parameter as Param;
use Tobento\Service\Container\Container;
use Tobento\Service\View;
use Tobento\Service\Dir;

class QueueHandlerTest extends TestCase
{
    protected function createRenderer(): RendererInterface
    {
        return new ViewRenderer(
            new View\View(
                new View\PhpRenderer(
                    new Dir\Dirs(
                        new Dir\Dir(realpath(__DIR__.'/../views/')),
                    )
                ),
                new View\Data(),
                new View\Assets(__DIR__.'/src/', 'https://example.com/src/')
            )
        );
    }
    
    public function testHandleMethod()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $this->createRenderer(),
        );
        
        $this->assertInstanceof(QueueHandlerInterface::class, $handler);
        
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>')
            ->parameter(new Parameter\Queue());
        
        $handler->handle($message);
        
        $job = $queue->pop();
        
        $this->assertSame(MailJobHandler::class, $job->getName());
        $this->assertSame('Subject', $job->getPayload()['subject'] ?? '');
        $this->assertSame('inmemory', $job->parameters()->get(Param\Queue::class)?->name());
        $this->assertSame(null, $job->parameters()->get(Param\Delay::class)?->seconds());
        $this->assertSame(3, $job->parameters()->get(Param\Retry::class)?->max());
        $this->assertSame(0, $job->parameters()->get(Param\Priority::class)?->priority());
        $this->assertSame(null, $job->parameters()->get(Param\Encrypt::class));
    }
    
    public function testHandleMethodWithSpecificQueueValues()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'foo',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $this->createRenderer(),
        );
        
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->parameter(new Parameter\Queue(
                name: 'foo',
                delay: 30,
                retry: 5,
                priority: 100,
                encrypt: false,
                renderTemplates: false,
            ));
        
        $handler->handle($message);
        
        $job = $queue->pop();

        $this->assertSame('foo', $job->parameters()->get(Param\Queue::class)?->name());
        $this->assertSame(30, $job->parameters()->get(Param\Delay::class)?->seconds());
        $this->assertSame(5, $job->parameters()->get(Param\Retry::class)?->max());
        $this->assertSame(100, $job->parameters()->get(Param\Priority::class)?->priority());
        $this->assertSame(null, $job->parameters()->get(Param\Encrypt::class));
    }
    
    public function testHandleMethodUsesQueueNameIfNoSpecificSet()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'foo',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'bar',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $this->createRenderer(),
            queueName: 'bar',
        );
        
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->parameter(new Parameter\Queue());
        
        $handler->handle($message);
        
        $job = $queue->pop();

        $this->assertSame('bar', $job->parameters()->get(Param\Queue::class)?->name());
        
        // should use specific:
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->parameter(new Parameter\Queue(name: 'foo'));
        
        $handler->handle($message);
        
        $job = $queue->pop();

        $this->assertSame('foo', $job->parameters()->get(Param\Queue::class)?->name());
    }
    
    public function testHandleMethodRenderTemplatesByDefault()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $this->createRenderer(),
        );
        
        $message = (new Message())
            ->to('to@example.com')
            ->textTemplate('welcome-text', ['name' => 'John'])
            ->htmlTemplate('welcome', ['name' => 'John'])
            ->parameter(new Parameter\Queue());
        
        $handler->handle($message);
        
        $job = $queue->pop();
        
        $this->assertSame(
            'Welcome, John',
            $job->getPayload()['text'] ?? ''
        );
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>Welcome</title></head><body>Welcome, John</body></html>',
            $job->getPayload()['html'] ?? ''
        );
    }
    
    public function testHandleMethodWithRenderTemplatesFalse()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $this->createRenderer(),
        );
        
        $message = (new Message())
            ->to('to@example.com')
            ->textTemplate('welcome-text', ['name' => 'John'])
            ->htmlTemplate('welcome', ['name' => 'John'])
            ->parameter(new Parameter\Queue(
                renderTemplates: false,
            ));
        
        $handler->handle($message);
        
        $job = $queue->pop();

        $this->assertSame(
            ['name' => 'welcome-text', 'data' => ['name' => 'John']],
            $job->getPayload()['text'] ?? ''
        );
        
        $this->assertSame(
            ['name' => 'welcome', 'data' => ['name' => 'John']],
            $job->getPayload()['html'] ?? ''
        );
    }    
}