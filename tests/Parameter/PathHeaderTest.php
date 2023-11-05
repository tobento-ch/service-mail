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
use Tobento\Service\Mail\Parameter\PathHeader;
use Tobento\Service\Mail\Parameter\Headerable;
use Tobento\Service\Mail\AddressInterface;
use Tobento\Service\Mail\Address;

/**
 * PathHeaderTest
 */
class PathHeaderTest extends TestCase
{
    public function testPathHeader()
    {
        $param = new PathHeader(name: 'foo', address: 'foo@example.com');
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertInstanceof(Headerable::class, $param);
        $this->assertSame('foo', $param->name());
        $this->assertInstanceof(AddressInterface::class, $param->address());
        $this->assertSame('foo@example.com', $param->address()?->email());
    }
    
    public function testPathHeaderWithAddress()
    {
        $address = new Address('foo@example.com');
        $param = new PathHeader(name: 'foo', address: $address);

        $this->assertTrue($address === $param->address());
    }
    
    public function testPathHeaderWithArrayAddress()
    {
        $param = new PathHeader(name: 'foo', address: ['email' => 'foo@example.com', 'name' => 'Foo']);

        $this->assertSame('foo@example.com', $param->address()?->email());
        $this->assertSame('Foo', $param->address()?->name());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new PathHeader(name: 'foo', address: 'foo@example.com');
        
        $this->assertSame(
            ['name' => 'foo', 'address' => ['email' => 'foo@example.com', 'name' => null]],
            $param->jsonSerialize()
        );
    }
}