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

namespace Tobento\Service\Mail\Test\Parameter;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter\StreamFile;
use Psr\Http\Message\StreamInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * StreamFileTest
 */
class StreamFileTest extends TestCase
{
    public function testStreamFile()
    {
        $stream = (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/image.jpg');
        
        $param = new StreamFile(stream: $stream, filename: 'Name');
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertInstanceof(StreamInterface::class, $param->stream());
        $this->assertTrue($stream === $param->stream());
        $this->assertSame('Name', $param->filename());
    }

    public function testMimeType()
    {
        $stream = (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/image.jpg');
        
        $param = new StreamFile(stream: $stream, filename: 'Name');
        
        $this->assertSame(null, $param->mimeType());
        
        $param = new StreamFile(stream: $stream, filename: 'Name', mimeType: 'image/jpeg');

        $this->assertSame('image/jpeg', $param->mimeType());
    }
    
    public function testIsInline()
    {
        $stream = (new Psr17Factory())->createStreamFromFile(__DIR__.'/../src/image.jpg');
        
        $param = new StreamFile(stream: $stream, filename: 'Name');
        
        $this->assertSame(false, $param->isInline());
        
        $param = new StreamFile(stream: $stream, filename: 'Name', inline: true);

        $this->assertSame(true, $param->isInline());
    }
}