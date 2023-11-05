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
use Tobento\Service\Mail\Symfony\SmtpMailerFactory;
use Tobento\Service\Mail\Symfony\EmailFactory;
use Tobento\Service\Mail\MailerFactoryInterface;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Tobento\Service\Event\Events;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\NullTransport;

/**
 * SmtpMailerFactoryTest
 */
class SmtpMailerFactoryTest extends TestCase
{
    protected function createSmtpMailerFactory(
        null|EmailFactoryInterface $emailFactory = null,
        null|QueueHandlerInterface $queueHandler = null,
        null|EventDispatcherInterface $eventDispatcher = null
    ): SmtpMailerFactory {
        if (is_null($emailFactory)) {
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

            $emailFactory = new EmailFactory(renderer: $renderer);
        }

        return new SmtpMailerFactory(
            emailFactory: $emailFactory,
            queueHandler: $queueHandler,
            eventDispatcher: $eventDispatcher,
        );
    }
    
    public function testImplementsMailerFactoryInterface()
    {
        $factory = $this->createSmtpMailerFactory();

        $this->assertInstanceof(MailerFactoryInterface::class, $factory);
    }
    
    public function testCreateMailer()
    {
        $mailer = $this->createSmtpMailerFactory()->createMailer(name: 'foo');
        
        $this->assertInstanceof(MailerInterface::class, $mailer);
        $this->assertSame('foo', $mailer->name());
    }

    public function testCreateMailerWithConfig()
    {
        $mailer = $this->createSmtpMailerFactory()->createMailer(
            name: 'foo',
            config: [
                'encryption' => '',
                'host' => 'host',
                'user' => 'user',
                'password' => '********',
                'port' => 465,
            ],
        );
        
        $this->assertInstanceof(EsmtpTransport::class, $mailer->transport());
    }
    
    public function testCreateMailerWithoutConfigDataCreatesNullTranport()
    {
        $mailer = $this->createSmtpMailerFactory()->createMailer(name: 'foo');
        
        $this->assertInstanceof(NullTransport::class, $mailer->transport());
    }
    
    public function testCreateMailerWithQueueHandler()
    {
        $queueHandler = new class() implements QueueHandlerInterface
        {
            public function handle(MessageInterface $message): void
            {
                //
            }
        };
        
        $mailer = $this->createSmtpMailerFactory(
            queueHandler: $queueHandler,
        )->createMailer(name: 'foo');
        
        $this->assertInstanceof(QueueHandlerInterface::class, $mailer->queueHandler());
    }
    
    public function testCreateMailerWithDispatcher()
    {
        $mailer = $this->createSmtpMailerFactory(
            eventDispatcher: new Events(),
        )->createMailer(name: 'foo');
        
        $this->assertInstanceof(EventDispatcherInterface::class, $mailer->eventDispatcher());
    }
    
    public function testCreateMailerWithDefaultsConfig()
    {
        $mailer = $this->createSmtpMailerFactory()->createMailer(
            name: 'foo',
            config: [
                'defaults' => [
                    'from' => 'from@example.com',
                ],
            ],
        );
        
        $email = $mailer->emailFactory()->createEmailFromMessage(new Message());
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('from@example.com', $adr?->getAddress());
    }
}