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
use Tobento\Service\Mail\MessageFactory;
use Tobento\Service\Mail\MessageFactoryInterface;
use Tobento\Service\Mail\MessageException;
use Tobento\Service\Mail\Parameter;

class MessageFactoryTest extends TestCase
{
    public function testThatImplementsMessageFactoryInterface()
    {
        $this->assertInstanceof(MessageFactoryInterface::class, new MessageFactory());
    }
    
    public function testCreateFromArrayMethod()
    {
        $message = (new MessageFactory())->createFromArray([
            'from' => ['email' => 'from@example.com', 'name' => 'John'],
            'to' => [
                ['email' => 'to@example.com', 'name' => 'Marc'],
            ],
            'cc' => [
                ['email' => 'cc@example.com', 'name' => null],
            ],
            'bcc' => [
                ['email' => 'bcc@example.com', 'name' => null],
            ],
            'replyTo' => ['email' => 'reply@example.com'],
            'subject' => 'Subject',
            'text' => 'Text',
            'html' => 'Html',
            'parameters' => [
                Parameter\TextHeader::class => ['name' => 'X-Custom-Header', 'value' => 'value'],
                Parameter\File::class => ['file' => '/path/document.pdf'],                
            ],
        ]);
        
        $this->assertSame('from@example.com', $message->getFrom()?->email());
        $this->assertSame('John', $message->getFrom()?->name());
        $this->assertSame(1, $message->getTo()->count());
        $this->assertSame(1, $message->getCc()->count());
        $this->assertSame(1, $message->getBcc()->count());
        $this->assertSame('reply@example.com', $message->getReplyTo()?->email());
        $this->assertSame('Subject', $message->getSubject());
        $this->assertSame('Text', $message->getText());
        $this->assertSame('Html', $message->getHtml());
        $this->assertSame(2, count($message->parameters()->all()));
    }
    
    public function testCreateFromArrayMethodWithTemplates()
    {
        $message = (new MessageFactory())->createFromArray([
            'text' => ['name' => 'view/text', 'data' => ['key' => 'value']],
            'html' => ['name' => 'view/html', 'data' => ['key' => 'value']],
        ]);

        $this->assertSame('view/text', $message->getText()->name());
        $this->assertSame(['key' => 'value'], $message->getText()->data());
        $this->assertSame('view/html', $message->getHtml()->name());
        $this->assertSame(['key' => 'value'], $message->getHtml()->data());
    }
    
    public function testCreateFromArrayMethodThrowsMessageExceptionOnFailure()
    {
        $this->expectException(MessageException::class);
        
        (new MessageFactory())->createFromArray([
            'parameters' => [Parameter\File::class => ['invalid' => 'foo']],
        ]);
    }
    
    public function testCreateFromJsonStringMethod()
    {
        $message = (new MessageFactory())->createFromJsonString(json_encode([
            'from' => ['email' => 'from@example.com', 'name' => 'John'],
            'to' => [
                ['email' => 'to@example.com', 'name' => 'Marc'],
            ],
            'cc' => [
                ['email' => 'cc@example.com', 'name' => null],
            ],
            'bcc' => [
                ['email' => 'bcc@example.com', 'name' => null],
            ],
            'replyTo' => ['email' => 'reply@example.com'],
            'subject' => 'Subject',
            'text' => 'Text',
            'html' => 'Html',
            'parameters' => [
                Parameter\TextHeader::class => ['name' => 'X-Custom-Header', 'value' => 'value'],
                Parameter\File::class => ['file' => '/path/document.pdf'],                
            ],
        ]));
        
        $this->assertSame('from@example.com', $message->getFrom()?->email());
        $this->assertSame('John', $message->getFrom()?->name());
        $this->assertSame(1, $message->getTo()->count());
        $this->assertSame(1, $message->getCc()->count());
        $this->assertSame(1, $message->getBcc()->count());
        $this->assertSame('reply@example.com', $message->getReplyTo()?->email());
        $this->assertSame('Subject', $message->getSubject());
        $this->assertSame('Text', $message->getText());
        $this->assertSame('Html', $message->getHtml());
        $this->assertSame(2, count($message->parameters()->all()));
    }
    
    public function testCreateFromStringMethodThrowsMessageExceptionOnFailure()
    {
        $this->expectException(MessageException::class);
        
        (new MessageFactory())->createFromJsonString(json_encode([
            'parameters' => [Parameter\File::class => ['invalid' => 'foo']],
        ]));
    }
}