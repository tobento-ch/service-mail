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
use Tobento\Service\Mail\Symfony\TemplateMessage;
use Tobento\Service\Mail\TemplateMessageInterface;
use Tobento\Service\Filesystem\File;
use Symfony\Component\Mime\Email;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * TemplateMessageTest
 */
class TemplateMessageTest extends TestCase
{
    public function testImplementsTemplateMessageInterface()
    {
        $tm = new TemplateMessage(email: new Email(), subject: 'Subject');

        $this->assertInstanceof(TemplateMessageInterface::class, $tm);
    }
    
    public function testSubjectMethod()
    {
        $tm = new TemplateMessage(email: new Email(), subject: 'Subject');

        $this->assertSame('Subject', $tm->subject());
    }
    
    public function testEmbedMethodWithString()
    {
        $tm = new TemplateMessage(email: new Email(), subject: 'Subject');

        $this->assertTrue(
            str_starts_with($tm->embed(file: __DIR__.'/../src/image.jpg'), 'cid:')
        );
    }
    
    public function testEmbedMethodWithFile()
    {
        $tm = new TemplateMessage(email: new Email(), subject: 'Subject');

        $this->assertTrue(
            str_starts_with($tm->embed(file: new File(__DIR__.'/../src/image.jpg')), 'cid:')
        );
    }
    
    public function testEmbedMethodWithStream()
    {
        $tm = new TemplateMessage(email: new Email(), subject: 'Subject');

        $this->assertTrue(
            str_starts_with(
                $tm->embed(file: (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/image.jpg')),
                'cid:'
            )
        );
    }
}