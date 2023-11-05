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
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\AddressInterface;
use Tobento\Service\Mail\AddressesInterface;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\ParametersInterface;
use Tobento\Service\Mail\Template;
use Tobento\Service\Mail\TemplateInterface;

/**
 * MessageTest
 */
class MessageTest extends TestCase
{
    public function testEmptyMessage()
    {
        $message = new Message();
        
        $this->assertInstanceof(MessageInterface::class, $message);
        $this->assertSame(null, $message->getFrom());
        $this->assertInstanceof(AddressesInterface::class, $message->getTo());
        $this->assertInstanceof(AddressesInterface::class, $message->getCc());
        $this->assertInstanceof(AddressesInterface::class, $message->getBcc());
        $this->assertSame(null, $message->getReplyTo());
        $this->assertSame('', $message->getSubject());
        $this->assertSame(null, $message->getText());
        $this->assertSame(null, $message->getHtml());
        $this->assertInstanceof(ParametersInterface::class, $message->parameters());
    }
    
    public function testFromMethod()
    {
        $message = (new Message())->from('foo@example.com');
        
        $this->assertInstanceof(AddressInterface::class, $message->getFrom());
        $this->assertSame('foo@example.com', $message->getFrom()->email());
        $this->assertSame(null, $message->getFrom()->name());
        
        $message = (new Message())->from(new Address('foo@example.com'));
        
        $this->assertInstanceof(AddressInterface::class, $message->getFrom());
        $this->assertSame('foo@example.com', $message->getFrom()->email());
        $this->assertSame(null, $message->getFrom()->name());
    }
    
    public function testToMethod()
    {
        $message = (new Message())->to('foo@example.com', new Address('bar@example.com'));
        
        $this->assertInstanceof(AddressesInterface::class, $message->getTo());
        
        $emails = $message->getTo()->map(function(AddressInterface $address): string {
            return $address->email();
        })->all();
        
        $this->assertSame(['foo@example.com', 'bar@example.com'], $emails);
    }
    
    public function testCcMethod()
    {
        $message = (new Message())->cc('foo@example.com', new Address('bar@example.com'));
        
        $this->assertInstanceof(AddressesInterface::class, $message->getTo());
        
        $emails = $message->getCc()->map(function(AddressInterface $address): string {
            return $address->email();
        })->all();
        
        $this->assertSame(['foo@example.com', 'bar@example.com'], $emails);
    }
    
    public function testBccMethod()
    {
        $message = (new Message())->bcc('foo@example.com', new Address('bar@example.com'));
        
        $this->assertInstanceof(AddressesInterface::class, $message->getTo());
        
        $emails = $message->getBcc()->map(function(AddressInterface $address): string {
            return $address->email();
        })->all();
        
        $this->assertSame(['foo@example.com', 'bar@example.com'], $emails);
    }
    
    public function testReplyToMethod()
    {
        $message = (new Message())->replyTo('foo@example.com');
        
        $this->assertInstanceof(AddressInterface::class, $message->getReplyTo());
        $this->assertSame('foo@example.com', $message->getReplyTo()->email());
        $this->assertSame(null, $message->getReplyTo()->name());
        
        $message = (new Message())->replyTo(new Address('foo@example.com'));
        
        $this->assertInstanceof(AddressInterface::class, $message->getReplyTo());
        $this->assertSame('foo@example.com', $message->getReplyTo()->email());
        $this->assertSame(null, $message->getReplyTo()->name());
    }
    
    public function testSubjectMethod()
    {
        $message = (new Message())->subject('lorem');

        $this->assertSame('lorem', $message->getSubject());
    }
    
    public function testTextMethod()
    {
        $message = (new Message())->text('lorem');
        $this->assertSame('lorem', $message->getText());
        
        $message = (new Message())->text(new Template('name', []));
        $this->assertInstanceof(TemplateInterface::class, $message->getText());
    }
    
    public function testTextTemplateMethod()
    {
        $message = (new Message())->textTemplate(name: 'name', data: ['key' => 'value']);

        $this->assertInstanceof(TemplateInterface::class, $message->getText());
        $this->assertSame('name', $message->getText()->name());
        $this->assertSame(['key' => 'value'], $message->getText()->data());
    }
    
    public function testHtmlMethod()
    {
        $message = (new Message())->html('<p>lorem</p>');
        $this->assertSame('<p>lorem</p>', $message->getHtml());
        
        $message = (new Message())->html(new Template('name', []));
        $this->assertInstanceof(TemplateInterface::class, $message->getHtml());
    }
    
    public function testHtmlTemplateMethod()
    {
        $message = (new Message())->htmlTemplate(name: 'name', data: ['key' => 'value']);

        $this->assertInstanceof(TemplateInterface::class, $message->getHtml());
        $this->assertSame('name', $message->getHtml()->name());
        $this->assertSame(['key' => 'value'], $message->getHtml()->data());
    }
    
    public function testParameterMethod()
    {
        $foo = new Parameter\TextHeader(name: 'foo', value: 'foo value');
        $bar = new Parameter\TextHeader(name: 'bar', value: 'bar value');
        
        $message = (new Message())->parameter($foo)->parameter($bar);
        
        $this->assertTrue($foo === ($message->parameters()->all()[0] ?? null));
        $this->assertTrue($bar === ($message->parameters()->all()[1] ?? null));
    }
    
    public function testJsonSerializeMethod()
    {
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $this->assertSame(
            [
                'from' => null,
                'to' => [
                    ['email' => 'to@example.com', 'name' => null],
                ],
                'cc' => [],
                'bcc' => [],
                'replyTo' => null,
                'subject' => 'Subject',
                'text' => null,
                'html' => '<p>Lorem Ipsum</p>',
                'parameters' => [],
            ],
            $message->jsonSerialize()
        );
    }
    
    public function testToStringMethod()
    {
        $message = (new Message())
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');

        $this->assertSame(
            '{"from":null,"to":[{"email":"to@example.com","name":null}],"cc":[],"bcc":[],"replyTo":null,"subject":"Subject","text":null,"html":"<p>Lorem Ipsum<\/p>","parameters":[]}',
            $message->__toString()
        );
    }
}