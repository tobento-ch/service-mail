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

namespace Tobento\Service\Mail\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\Mailers;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MailersInterface;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\Message;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Tobento\Service\Container\Container;
use Symfony\Component\Mailer\Transport\NullTransport;
use Psr\Container\ContainerInterface;

/**
 * MailersTest
 */
class MailersTest extends TestCase
{
    protected function createMailer(string $name): Symfony\Mailer
    {
        // create the renderer:
        $renderer = new ViewRenderer(
            new View\View(
                new View\PhpRenderer(
                    new Dir\Dirs(
                        new Dir\Dir(realpath(__DIR__.'/views/')),
                    )
                ),
                new View\Data(),
                new View\Assets(__DIR__.'/src/', 'https://example.com/src/')
            )
        );

        return new Symfony\Mailer(
            name: $name,
            emailFactory: new Symfony\EmailFactory($renderer),
            transport: new NullTransport(),
        );
    }
    
    public function testMailersInterfaceMethods()
    {
        $mailers = new Mailers(
            $this->createMailer('default'),
            $this->createMailer('foo'),
        );
        
        $this->assertInstanceof(MailersInterface::class, $mailers);
        $this->assertInstanceof(MailerInterface::class, $mailers->mailer(name: 'default'));
        $this->assertInstanceof(MailerInterface::class, $mailers->mailer(name: 'foo'));
        $this->assertSame(null, $mailers->mailer(name: 'bar'));
        $this->assertSame(['default', 'foo'], $mailers->names());
    }
    
    public function testMailerInterfaceMethods()
    {
        $mailers = new Mailers(
            $this->createMailer('default'),
        );
        
        $this->assertInstanceof(MailerInterface::class, $mailers);
        $this->assertTrue(is_string($mailers->name()));
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $mailers->send($message);
        
        $this->assertTrue(true);
    }
}