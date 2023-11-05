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
use Tobento\Service\Mail\Parameter\Tags;

/**
 * TagsTest
 */
class TagsTest extends TestCase
{
    public function testTags()
    {
        $param = new Tags(tags: ['foo', 'bar']);
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame(['foo', 'bar'], $param->tags());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new Tags(tags: ['foo', 'bar']);
        
        $this->assertSame(
            ['tags' => ['foo', 'bar']],
            $param->jsonSerialize()
        );
    }
}