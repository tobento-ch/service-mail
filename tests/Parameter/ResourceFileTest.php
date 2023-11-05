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
use Tobento\Service\Mail\Parameter\ResourceFile;

/**
 * ResourceFileTest
 */
class ResourceFileTest extends TestCase
{
    public function testResourceFile()
    {
        $param = new ResourceFile(
            resource: fopen(__DIR__.'/../src/image.jpg', 'r+'),
            filename: 'Name'
        );
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertTrue(is_resource($param->resource()));
        $this->assertSame('Name', $param->filename());
    }

    public function testMimeType()
    {
        $resource = fopen(__DIR__.'/../src/image.jpg', 'r+');
        
        $param = new ResourceFile(resource: $resource, filename: 'Name');
        
        $this->assertSame(null, $param->mimeType());
        
        $param = new ResourceFile(resource: $resource, filename: 'Name', mimeType: 'image/jpeg');

        $this->assertSame('image/jpeg', $param->mimeType());
    }
    
    public function testIsInline()
    {
        $resource = fopen(__DIR__.'/../src/image.jpg', 'r+');
        
        $param = new ResourceFile(resource: $resource, filename: 'Name');
        
        $this->assertSame(false, $param->isInline());
        
        $param = new ResourceFile(resource: $resource, filename: 'Name', inline: true);

        $this->assertSame(true, $param->isInline());
    }
}