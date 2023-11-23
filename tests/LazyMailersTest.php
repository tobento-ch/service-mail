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
use Tobento\Service\Mail\LazyMailers;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MailersInterface;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\Message;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Tobento\Service\Container\Container;
use Psr\Container\ContainerInterface;

/**
 * LazyMailersTest
 */
class LazyMailersTest extends TestCase
{
    protected function createLazyMailers(
        array $mailers,
        null|ContainerInterface $container = null,
    ): LazyMailers {

        $container = $container ?: new Container();
        
        $container->set(Symfony\EmailFactoryInterface::class, function() {
            
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

            return new Symfony\EmailFactory(
                renderer: $renderer
            );
        });
        
        return new LazyMailers(
            container: $container,
            mailers: $mailers,
        );
    }
    
    public function testMailersInterfaceMethods()
    {
        $mailers = $this->createLazyMailers(mailers: [
            'default' => [
                'factory' => Symfony\SmtpMailerFactory::class,
                'config' => [],
            ],
        ]);
        
        $this->assertInstanceof(MailersInterface::class, $mailers);
        $this->assertInstanceof(MailerInterface::class, $mailers->mailer(name: 'default'));
        $this->assertSame(null, $mailers->mailer(name: 'foo'));
    }
    
    public function testMailerInterfaceMethods()
    {
        $mailers = $this->createLazyMailers(mailers: [
            'default' => [
                'factory' => Symfony\SmtpMailerFactory::class,
                'config' => [],
            ],
        ]);
        
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
    
    public function testUsingMailerObject()
    {
        $mailers = new LazyMailers(container: new Container(), mailers: [
            'primary' => new class() implements MailerInterface {
                public function name(): string
                {
                    return 'primary';
                }

                public function send(MessageInterface ...$message): void
                {
                    //
                }
            },
        ]);
        
        $this->assertSame('primary', $mailers->mailer('primary')?->name());
        $this->assertSame($mailers->mailer('primary'), $mailers->mailer('primary'));
    }
    
    public function testUsingClosure()
    {
        $mailers = new LazyMailers(container: new Container(), mailers: [
            'primary' => static function (string $name, ContainerInterface $c): MailerInterface {
                return new class() implements MailerInterface {
                    public function name(): string
                    {
                        return 'primary';
                    }

                    public function send(MessageInterface ...$message): void
                    {
                        //
                    }
                };
            },
        ]);
        
        $this->assertSame('primary', $mailers->mailer('primary')?->name());
        $this->assertSame($mailers->mailer('primary'), $mailers->mailer('primary'));
    }    
}