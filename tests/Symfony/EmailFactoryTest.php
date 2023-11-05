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
use Tobento\Service\Mail\Symfony\EmailFactory;
use Tobento\Service\Mail\Symfony\EmailFactoryInterface;
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\Parameters;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\Template;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Symfony\Component\Mime;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * EmailFactoryTest
 */
class EmailFactoryTest extends TestCase
{
    protected function createEmailFactory(
        null|RendererInterface $renderer = null,
        array $config = [],
    ): EmailFactory {

        if (is_null($renderer)) {
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
        }

        return new EmailFactory(
            renderer: $renderer,
            config: $config,
        );
    }
    
    public function testImplementsEmailFactoryInterface()
    {
        $emailFactory = $this->createEmailFactory();

        $this->assertInstanceof(EmailFactoryInterface::class, $emailFactory);
    }
    
    public function testWithConfigMethodReturnsNewInstance()
    {
        $emailFactory = $this->createEmailFactory();
        
        $emailFactoryNew = $emailFactory->withConfig(['key' => 'value']);

        $this->assertFalse($emailFactory === $emailFactoryNew);
    }
    
    public function testCreateEmailFromMessageReturnsEmail()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertInstanceof(Mime\Email::class, $email);
    }
    
    public function testCreateEmailFromMessageFromAddress()
    {
        $emailFactory = $this->createEmailFactory();

        $email = $emailFactory->createEmailFromMessage(
            (new Message())->from('foo@example.com')
        );
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        
        $email = $emailFactory->createEmailFromMessage(
            (new Message())->from(new Address('foo@example.com', 'Foo'))
        );
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        $this->assertSame('Foo', $adr?->getName());
    }
    
    public function testCreateEmailFromMessageReplyToAddress()
    {
        $emailFactory = $this->createEmailFactory();

        $email = $emailFactory->createEmailFromMessage(
            (new Message())->replyTo('foo@example.com')
        );
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        
        $email = $emailFactory->createEmailFromMessage(
            (new Message())->replyTo(new Address('foo@example.com', 'Foo'))
        );
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        $this->assertSame('Foo', $adr?->getName());
    }    
    
    public function testCreateEmailFromMessageToAddress()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())
            ->to('foo@example.com', new Address('bar@example.com', 'Bar'));
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getTo()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        
        $adr1 = $email->getTo()[1] ?? null;
        $this->assertSame('bar@example.com', $adr1?->getAddress());
        $this->assertSame('Bar', $adr1?->getName());
    }
    
    public function testCreateEmailFromMessageCcAddress()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())
            ->cc('foo@example.com', new Address('bar@example.com', 'Bar'));
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getCc()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        
        $adr1 = $email->getCc()[1] ?? null;
        $this->assertSame('bar@example.com', $adr1?->getAddress());
        $this->assertSame('Bar', $adr1?->getName());
    }
    
    public function testCreateEmailFromMessageBccAddress()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())
            ->bcc('foo@example.com', new Address('bar@example.com', 'Bar'));
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getBcc()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
        
        $adr1 = $email->getBcc()[1] ?? null;
        $this->assertSame('bar@example.com', $adr1?->getAddress());
        $this->assertSame('Bar', $adr1?->getName());
    }
    
    public function testCreateEmailFromSubject()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->subject('Subject');
        
        $email = $emailFactory->createEmailFromMessage(message: $message);
        
        $this->assertSame('Subject', $email->getSubject());
    }
    
    public function testCreateEmailFromMessageText()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->text('lorem');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame('lorem', $email->getTextBody());
    }
    
    public function testCreateEmailFromMessageTextWithTemplate()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->text(new Template('welcome-text', ['name' => 'John']));
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame('Welcome, John', $email->getTextBody());
    }    
    
    public function testCreateEmailFromMessageHtml()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->html('<p>lorem</p>');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame('<p>lorem</p>', $email->getHtmlBody());
    }    

    public function testCreateEmailFromMessageHtmlWithTemplate()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->html(new Template('welcome', ['name' => 'John']));
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>Welcome</title></head><body>Welcome, John</body></html>',
            $email->getHtmlBody()
        );
    }
    
    public function testCreateEmailFromMessageTextTemplate()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->textTemplate('welcome-text', ['name' => 'John']);
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame('Welcome, John', $email->getTextBody());
    }
    
    public function testCreateEmailFromMessageHtmlTemplate()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->htmlTemplate('welcome', ['name' => 'John']);
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>Welcome</title></head><body>Welcome, John</body></html>',
            $email->getHtmlBody()
        );
    }
    
    public function testCreateEmailFromMessageTextGetsCreatedFromHtmlIfNotSpecified()
    {
        $emailFactory = $this->createEmailFactory();
        
        $message = (new Message())->html('<p>Lorem</p>');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertSame('Lorem', $email->getTextBody());
    }
    
    public function testCreateEmailFromMessageFiles()
    {
        $emailFactory = $this->createEmailFactory();
            
        $message = (new Message())
            ->parameter(new Parameter\File(__DIR__.'/../src/image.jpg'))
            ->parameter(new Parameter\File(__DIR__.'/../src/app.css'))
            ->parameter(new Parameter\StreamFile(
                (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/image.jpg'),
                'Image.jpg',
            ))
            ->parameter(new Parameter\StreamFile(
                (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/app.css'),
                'App.css',
            ))
            ->parameter(new Parameter\ResourceFile(
                fopen(__DIR__.'/../src/image.jpg', 'r+'),
                'Image.jpg',
            ))
            ->parameter(new Parameter\ResourceFile(
                fopen(__DIR__.'/../src/app.css', 'r+'),
                'App.css',
            ));            
        
        $email = $emailFactory->createEmailFromMessage($message);

        $this->assertSame(6, count($email->getAttachments()));
    }
    
    public function testCreateEmailFromMessageHeaders()
    {
        $emailFactory = $this->createEmailFactory();
            
        $message = (new Message())
            ->parameter(new Parameter\TextHeader(
                name: 'X-Custom-Header',
                value: 'value',
            ))
            ->parameter(new Parameter\IdHeader(
                name: 'References',
                ids: ['a@example.com', 'b@example.com'],
            ))
            ->parameter(new Parameter\IdHeader(
                name: 'Ids',
                ids: ['a@example.com', 'b@example.com'],
            ))            
            ->parameter(new Parameter\PathHeader(
                name: 'Return-Path',
                address: 'return@example.com',
            ));
        
        $email = $emailFactory->createEmailFromMessage($message);

        $this->assertTrue($email->getHeaders()->has('x-custom-header'));
        $this->assertTrue($email->getHeaders()->has('references'));
        $this->assertTrue($email->getHeaders()->has('ids'));
        $this->assertTrue($email->getHeaders()->has('return-path'));
    }
    
    public function testCreateEmailFromMessageTagsAndMetadata()
    {
        $emailFactory = $this->createEmailFactory();
            
        $message = (new Message())
            ->parameter(new Parameter\Tags(['tagname']))
            ->parameter(new Parameter\Tags(['tagname-foo']))
            ->parameter(new Parameter\Metadata([
                'foo' => 'value',
            ]))
            ->parameter(new Parameter\Metadata([
                'bar' => 'value',
            ]));            
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertTrue($email->getHeaders()->has('x-metadata-foo'));
        $this->assertTrue($email->getHeaders()->has('x-metadata-bar'));
        $this->assertTrue($email->getHeaders()->has('x-tag'));
    }
    
    public function testCreateEmailFromMessageConfigAlwaysTo()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['alwaysTo' => 'alwaysTo@example.com'],
        );
        
        $message = (new Message())->to('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getTo()[0] ?? null;
        $this->assertSame('alwaysTo@example.com', $adr?->getAddress());
        $this->assertSame(1, count($email->getTo()));
    }
    
    public function testCreateEmailFromMessageConfigAlwaysToWithAddress()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['alwaysTo' => new Address('alwaysTo@example.com', 'AlwaysTo')],
        );
        
        $message = (new Message())->to('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getTo()[0] ?? null;
        $this->assertSame('alwaysTo@example.com', $adr?->getAddress());
        $this->assertSame('AlwaysTo', $adr?->getName());
        $this->assertSame(1, count($email->getTo()));
    }
    
    public function testCreateEmailFromMessageConfigFrom()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['from' => 'from.config@example.com'],
        );
        
        $message = new Message();
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('from.config@example.com', $adr?->getAddress());
        
        // specified is used:
        $message = (new Message())->from('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
    }
    
    public function testCreateEmailFromMessageConfigFromWithAddress()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['from' => new Address('from.config@example.com', 'From Config')],
        );
        
        $message = new Message();
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('from.config@example.com', $adr?->getAddress());
        $this->assertSame('From Config', $adr?->getName());
        
        // specified is used:
        $message = (new Message())->from('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getFrom()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
    }
    
    public function testCreateEmailFromMessageConfigReplyTo()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['replyTo' => 'replyto.config@example.com'],
        );
        
        $message = new Message();
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('replyto.config@example.com', $adr?->getAddress());
        
        // specified is used:
        $message = (new Message())->replyTo('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
    }
    
    public function testCreateEmailFromMessageConfigReplyToWithAddress()
    {
        $emailFactory = $this->createEmailFactory(
            config: ['replyTo' => new Address('replyto.config@example.com', 'ReplyTo Config')],
        );
        
        $message = new Message();
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('replyto.config@example.com', $adr?->getAddress());
        $this->assertSame('ReplyTo Config', $adr?->getName());
        
        // specified is used:
        $message = (new Message())->replyTo('foo@example.com');
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $adr = $email->getReplyTo()[0] ?? null;
        $this->assertSame('foo@example.com', $adr?->getAddress());
    }
    
    public function testCreateEmailFromMessageConfigParameters()
    {
        $emailFactory = $this->createEmailFactory(
            config: [
                'parameters' => new Parameters(
                    new Parameter\PathHeader('Return-Path', 'return@example.com'),
                ),
            ],
        );
        
        $message = new Message();
        
        $email = $emailFactory->createEmailFromMessage($message);
        
        $this->assertTrue($email->getHeaders()->has('return-path'));
    }    
}