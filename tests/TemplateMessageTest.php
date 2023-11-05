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
use Tobento\Service\Mail\TemplateMessage;
use Tobento\Service\Mail\TemplateMessageInterface;
use Tobento\Service\Filesystem\File;
use Nyholm\Psr7\Factory\Psr17Factory;
use InvalidArgumentException;

/**
 * TemplateMessageTest
 */
class TemplateMessageTest extends TestCase
{
    public function testImplementsTemplateMessageInterface()
    {
        $tm = new TemplateMessage(subject: 'Lorem');
        
        $this->assertInstanceof(TemplateMessageInterface::class, $tm);
    }
    
    public function testSubjectMethod()
    {
        $tm = new TemplateMessage(subject: 'Lorem');
        
        $this->assertSame('Lorem', $tm->subject());
    }
    
    public function testEmbedMethodWithString()
    {
        $tm = new TemplateMessage(subject: 'Lorem');
        
        $this->assertSame( 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAAAAD/7gAOQWRvYmUAZMAAAAAB/9sAhAAbGhopHSlBJiZBQi8vL0JHPz4+P0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHAR0pKTQmND8oKD9HPzU/R0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0f/wAARCAABAAEDASIAAhEBAxEB/8QBGwAAAwEBAQEBAQEBAQAAAAAAAQACAwQFBgcICQoLAQEBAQEBAQEBAQEBAQAAAAAAAQIDBAUGBwgJCgsQAAICAQMCAwQHBgMDBgIBNQEAAhEDIRIxBEFRIhNhcTKBkbFCoQXRwRTwUiNyM2LhgvFDNJKishXSUyRzwmMGg5Pi8qNEVGQlNUUWJnQ2VWWzhMPTdePzRpSkhbSVxNTk9KW1xdXl9VZmdoaWprbG1ub2EQACAgAFAQYGAQMBAwUDBi8AARECIQMxQRJRYXGBkSITMvChsQTB0eHxQlIjYnIUkjOCQySisjRTRGNzwtKDk6NU4vIFFSUGFiY1ZEVVNnRls4TD03Xj80aUpIW0lcTU5PSltcXV5fVWZnaG/9oADAMBAAIRAxEAPwDgVVfGfoj/2Q==',
            $tm->embed(file: __DIR__.'/src/image.jpg')
        );
    }
    
    public function testEmbedMethodWithFile()
    {
        $tm = new TemplateMessage(subject: 'Lorem');
        
        $this->assertSame( 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAAAAD/7gAOQWRvYmUAZMAAAAAB/9sAhAAbGhopHSlBJiZBQi8vL0JHPz4+P0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHAR0pKTQmND8oKD9HPzU/R0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0f/wAARCAABAAEDASIAAhEBAxEB/8QBGwAAAwEBAQEBAQEBAQAAAAAAAQACAwQFBgcICQoLAQEBAQEBAQEBAQEBAQAAAAAAAQIDBAUGBwgJCgsQAAICAQMCAwQHBgMDBgIBNQEAAhEDIRIxBEFRIhNhcTKBkbFCoQXRwRTwUiNyM2LhgvFDNJKishXSUyRzwmMGg5Pi8qNEVGQlNUUWJnQ2VWWzhMPTdePzRpSkhbSVxNTk9KW1xdXl9VZmdoaWprbG1ub2EQACAgAFAQYGAQMBAwUDBi8AARECIQMxQRJRYXGBkSITMvChsQTB0eHxQlIjYnIUkjOCQySisjRTRGNzwtKDk6NU4vIFFSUGFiY1ZEVVNnRls4TD03Xj80aUpIW0lcTU5PSltcXV5fVWZnaG/9oADAMBAAIRAxEAPwDgVVfGfoj/2Q==',
            $tm->embed(file: new File(__DIR__.'/src/image.jpg'))
        );
    }
    
    public function testEmbedMethodWithFileThrowsInvalidArgumentExceptionIfNotImage()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $tm = new TemplateMessage(subject: 'Lorem');

        $tm->embed(file: (new Psr17Factory())->createStreamFromFile(__DIR__.'/src/app.css'));
    }
    
    public function testEmbedMethodWithStream()
    {
        $tm = new TemplateMessage(subject: 'Lorem');

        $this->assertSame( 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAAAAD/7gAOQWRvYmUAZMAAAAAB/9sAhAAbGhopHSlBJiZBQi8vL0JHPz4+P0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHAR0pKTQmND8oKD9HPzU/R0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0f/wAARCAABAAEDASIAAhEBAxEB/8QBGwAAAwEBAQEBAQEBAQAAAAAAAQACAwQFBgcICQoLAQEBAQEBAQEBAQEBAQAAAAAAAQIDBAUGBwgJCgsQAAICAQMCAwQHBgMDBgIBNQEAAhEDIRIxBEFRIhNhcTKBkbFCoQXRwRTwUiNyM2LhgvFDNJKishXSUyRzwmMGg5Pi8qNEVGQlNUUWJnQ2VWWzhMPTdePzRpSkhbSVxNTk9KW1xdXl9VZmdoaWprbG1ub2EQACAgAFAQYGAQMBAwUDBi8AARECIQMxQRJRYXGBkSITMvChsQTB0eHxQlIjYnIUkjOCQySisjRTRGNzwtKDk6NU4vIFFSUGFiY1ZEVVNnRls4TD03Xj80aUpIW0lcTU5PSltcXV5fVWZnaG/9oADAMBAAIRAxEAPwDgVVfGfoj/2Q==',
            $tm->embed(
                file: (new Psr17Factory())->createStreamFromFile(__DIR__.'/src/image.jpg'),
                mimeType: (new File(__DIR__.'/src/image.jpg'))->getMimeType()
            )
        );
    }
    
    public function testEmbedMethodWithStreamWithoutMimeTypeThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $tm = new TemplateMessage(subject: 'Lorem');

        $tm->embed(file: (new Psr17Factory())->createStreamFromFile(__DIR__.'/src/image.jpg'));
    }
}