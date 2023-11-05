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
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\AddressInterface;

/**
 * AddressTest
 */
class AddressTest extends TestCase
{
    public function testWithEmailOnly()
    {
        $address = new Address(email: 'from@example.com');
        
        $this->assertInstanceof(AddressInterface::class, $address);
        $this->assertSame('from@example.com', $address->email());
        $this->assertSame(null, $address->name());
    }
    
    public function testWithAllAttributes()
    {
        $address = new Address(email: 'from@example.com', name: 'John');
        
        $this->assertSame('from@example.com', $address->email());
        $this->assertSame('John', $address->name());
    }
    
    public function testJsonSerializeMethod()
    {
        $address = new Address(email: 'from@example.com', name: 'John');
        
        $this->assertSame(
            ['email' => 'from@example.com', 'name' => 'John'],
            $address->jsonSerialize()
        );
    }
}