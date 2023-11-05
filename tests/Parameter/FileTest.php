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
use Tobento\Service\Mail\Parameter\File;
use Tobento\Service\Filesystem\File as BaseFile;

/**
 * FileTest
 */
class FileTest extends TestCase
{
    public function testFile()
    {
        $param = new File(file: new BaseFile(__DIR__.'/../src/image.jpg'));
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertInstanceof(BaseFile::class, $param->file());
    }
    
    public function testFilename()
    {
        $param = new File(
            file: new BaseFile(__DIR__.'/../src/image.jpg'),
            filename: 'Name',
        );

        $this->assertSame('Name', $param->filename());
        
        $param = new File(file: new BaseFile(__DIR__.'/../src/image.jpg'));
        $this->assertSame('image.jpg', $param->filename());
    }
    
    public function testIsInline()
    {
        $param = new File(file: new BaseFile(__DIR__.'/../src/image.jpg'));
        $this->assertSame(false, $param->isInline());
        
        $param = new File(
            file: new BaseFile(__DIR__.'/../src/image.jpg'),
            inline: true,
        );

        $this->assertSame(true, $param->isInline());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new File(file: 'file', filename: 'filename');
        
        $this->assertSame(
            ['file' => 'file', 'filename' => 'filename', 'inline' => false],
            $param->jsonSerialize()
        );
    }
}