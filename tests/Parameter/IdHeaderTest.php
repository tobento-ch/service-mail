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
use Tobento\Service\Mail\Parameter\IdHeader;
use Tobento\Service\Mail\Parameter\Headerable;

/**
 * IdHeaderTest
 */
class IdHeaderTest extends TestCase
{
    public function testIdHeader()
    {
        $param = new IdHeader(name: 'foo', ids: ['id']);
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertInstanceof(Headerable::class, $param);
        $this->assertSame('foo', $param->name());
        $this->assertSame(['id'], $param->ids());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new IdHeader(name: 'foo', ids: ['id']);
        
        $this->assertSame(
            ['name' => 'foo', 'ids' => ['id']],
            $param->jsonSerialize()
        );
    }
}