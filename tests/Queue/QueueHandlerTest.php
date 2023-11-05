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
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Queue\InMemoryQueue;
use Tobento\Service\Queue\JobProcessor;
use Tobento\Service\Container\Container;
use Tobento\Service\View;
use Tobento\Service\Dir;

class QueueHandlerTest extends TestCase
{
    public function testHandleMethod()
    {
        $renderer = new ViewRenderer(
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
        
        $queue = new InMemoryQueue(
            name: 'inmemeory',
            jobProcessor: new JobProcessor(new Container()),
            priority: 100,
        );
        
        $handler = new QueueHandler(
            queue: $queue,
            renderer: $renderer,
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
    }
}