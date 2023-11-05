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
use Tobento\Service\Mail\Queue\MailJobHandler;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\MessageFactory;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Tobento\Service\Queue\Job;
use Symfony\Component\Mailer\Transport\NullTransport;

class MailJobHandlerTest extends TestCase
{
    public function testHandleJob()
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

        $mailer = new Symfony\Mailer(
            name: 'default',
            emailFactory: new Symfony\EmailFactory($renderer),
            transport: new NullTransport(),
        );
        
        $handler = new MailJobHandler(
            mailer: $mailer,
            messageFactory: new MessageFactory(),
        );
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $job = new Job(
            name: MailJobHandler::class,
            payload: $message->jsonSerialize(),
        );
        
        $handler->handleJob($job);
        
        $this->assertTrue(true);
    }
}