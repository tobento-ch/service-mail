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
use Tobento\Service\Mail\Parameter\TextHeader;
use Tobento\Service\Mail\Parameter\Headerable;

/**
 * TextHeaderTest
 */
class TextHeaderTest extends TestCase
{
    public function testTextHeader()
    {
        $param = new TextHeader(name: 'foo', value: 'bar');
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertInstanceof(Headerable::class, $param);
        $this->assertSame('foo', $param->name());
        $this->assertSame('bar', $param->value());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new TextHeader(name: 'foo', value: 'bar');
        
        $this->assertSame(
            ['name' => 'foo', 'value' => 'bar'],
            $param->jsonSerialize()
        );
    }
}