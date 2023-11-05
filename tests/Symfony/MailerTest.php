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

namespace Tobento\Service\Mail\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\Symfony\Mailer;
use Tobento\Service\Mail\Symfony\EmailFactory;
use Tobento\Service\Mail\Symfony\EmailFactoryInterface;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MailerException;
use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\Event;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Tobento\Service\Event\Events;
use Tobento\Service\Collection\Collection;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Exception\TransportException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * MailerTest
 */
class MailerTest extends TestCase
{
    protected function createMailer(
        string $name,
        null|TransportInterface $transport = null,
        null|QueueHandlerInterface $queueHandler = null,
        null|EventDispatcherInterface $eventDispatcher = null,
        array $config = [],
    ): Mailer {
        // create the renderer:
        $renderer = new ViewRenderer(
            new View\View(
                new View\PhpRenderer(
                    new Dir\Dirs(
                        new Dir\Dir(realpath(__DIR__.'/../views/')),
                    )
                ),
                new View\Data(),
                new View\Assets(__DIR__.'/../src/', 'https://example.com/src/')
            )
        );
        
        // create email factory:
        $emailFactory = new EmailFactory(
            renderer: $renderer,
            config: $config,
        );

        // create the mailer:
        return new Mailer(
            name: 'default',
            emailFactory: $emailFactory,
            transport: $transport ?: new NullTransport(),
            queueHandler: $queueHandler,
            eventDispatcher: $eventDispatcher,
        );
    }
    
    public function testMailer()
    {
        $mailer = $this->createMailer(name: 'default');

        $this->assertInstanceof(MailerInterface::class, $mailer);
        $this->assertSame('default', $mailer->name());
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $mailer->send($message);
        
        $this->assertInstanceof(TransportInterface::class, $mailer->transport());
        $this->assertInstanceof(EmailFactoryInterface::class, $mailer->emailFactory());
        $this->assertSame(null, $mailer->queueHandler());
        $this->assertSame(null, $mailer->eventDispatcher());
    }
    
    public function testSendMultiple()
    {
        $mailer = $this->createMailer(name: 'default');
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $anotherMessage = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');        
        
        $mailer->send($message, $anotherMessage);
        
        $this->assertTrue(true);
    }
    
    public function testGetsHandleWithQueueHandler()
    {
        $queueHandler = new class() implements QueueHandlerInterface
        {
            public function __construct(
                private array $handled = []
            ) {}

            public function handle(MessageInterface $message): void
            {
                $this->handled[] = $message;
            }
            
            public function handled(): array
            {
                return $this->handled;
            }
        };
        
        $mailer = $this->createMailer(
            name: 'default',
            queueHandler: $queueHandler,
        );
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>')
            ->parameter(new Parameter\Queue());
        
        $mailer->send($message);
        
        $this->assertTrue($message === ($queueHandler->handled()[0] ?? null));
        
        $this->assertInstanceof(QueueHandlerInterface::class, $mailer->queueHandler());
    }
    
    public function testThrowsMailerExceptionOnTransportException()
    {
        $this->expectException(MailerException::class);
        
        $transport = new class() extends AbstractTransport
        {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('failed');
            }

            public function __toString(): string
            {
                return 'null://';
            }
        };

        $mailer = $this->createMailer(
            name: 'default',
            transport: $transport,
        );
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $mailer->send($message);
    }
    
    public function testMessageSentEventShouldBeDispatched()
    {
        $events = new Events();
        $collection = new Collection();
        
        $events->listen(function(Event\MessageSent $event) use ($collection) {
            $collection->add('message', $event->message());
        });
        
        $mailer = $this->createMailer(name: 'default', eventDispatcher: $events);
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $mailer->send($message);
        
        $this->assertTrue($message === $collection->get('message'));
        $this->assertInstanceof(EventDispatcherInterface::class, $mailer->eventDispatcher());
    }
    
    public function testMessageNotSentEventShouldBeDispatched()
    {
        $transport = new class() extends AbstractTransport
        {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('failed');
            }

            public function __toString(): string
            {
                return 'null://';
            }
        };
        
        $events = new Events();
        $collection = new Collection();
        
        $events->listen(function(Event\MessageNotSent $event) use ($collection) {
            $collection->add('message', $event->message());
            $collection->add('exception', $event->exception());
        });
        
        $mailer = $this->createMailer(
            name: 'default',
            eventDispatcher: $events,
            transport: $transport,
        );
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        try {
            $mailer->send($message);
        } catch (MailerException $e) {
            // ignore
        }
        
        $this->assertTrue($message === $collection->get('message'));
        $this->assertSame('failed', $collection->get('exception')?->getMessage());
    }
    
    public function testMessageQueuedEventShouldBeDispatched()
    {
        $queueHandler = new class() implements QueueHandlerInterface
        {
            public function handle(MessageInterface $message): void
            {
                //
            }
        };
        
        $events = new Events();
        $collection = new Collection();
        
        $events->listen(function(Event\MessageQueued $event) use ($collection) {
            $collection->add('message', $event->message());
        });
        
        $mailer = $this->createMailer(
            name: 'default',
            eventDispatcher: $events,
            queueHandler: $queueHandler,
        );
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>')
            ->parameter(new Parameter\Queue());
        
        $mailer->send($message);
        
        $this->assertTrue($message === $collection->get('message'));
    }
}