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
use Tobento\Service\Mail\Addresses;
use Tobento\Service\Mail\AddressesInterface;
use Tobento\Service\Mail\AddressInterface;
use Tobento\Service\Mail\Address;

/**
 * AddressesTest
 */
class AddressesTest extends TestCase
{
    public function testCreateAddresses()
    {
        $addresses = new Addresses(
            new Address('from@example.com'),
        );
        
        $this->assertInstanceof(AddressesInterface::class, $addresses);
    }
    
    public function testAddMethod()
    {
        $addresses = new Addresses();
        
        $addresses->add(new Address('from@example.com'))
            ->add(new Address('to@example.com'));
        
        $this->assertSame(2, $addresses->count());
    }
    
    public function testMapMethod()
    {
        $addresses = new Addresses(
            new Address('from@example.com'),
            new Address('to@example.com'),
        );
        
        $to = $addresses->map(function(AddressInterface $address): string {
            return $address->email();
        })->all();
        
        $this->assertSame(['from@example.com', 'to@example.com'], $to);
    }
    
    public function testAllMethod()
    {
        $addresses = new Addresses();
        
        $this->assertSame(0, $addresses->count());
        
        $addresses = new Addresses(
            new Address('from@example.com'),
            new Address('to@example.com'),
        );
        
        $this->assertSame(2, $addresses->count());
    }
    
    public function testEmptyMethod()
    {
        $addresses = new Addresses();
        
        $this->assertTrue($addresses->empty());
        
        $addresses = new Addresses(
            new Address('from@example.com'),
            new Address('to@example.com'),
        );
        
        $this->assertFalse($addresses->empty());
    }
    
    public function testCountMethod()
    {
        $addresses = new Addresses();
        
        $this->assertSame(0, $addresses->count());
        
        $addresses = new Addresses(
            new Address('from@example.com'),
            new Address('to@example.com'),
        );
        
        $this->assertSame(2, $addresses->count());
    }
    
    public function testIteration()
    {
        $addresses = new Addresses(
            new Address('from@example.com'),
            new Address('to@example.com'),
        );
        
        foreach($addresses as $address) {
            $this->assertInstanceof(AddressInterface::class, $address);
        }
    }
    
    public function testJsonSerializeMethod()
    {
        $addresses = new Addresses();
        
        $this->assertSame([], $addresses->jsonSerialize());
        
        $addresses = new Addresses(
            new Address('from@example.com', 'John'),
            new Address('to@example.com'),
        );
        
        $this->assertSame(
            [
                ['email' => 'from@example.com', 'name' => 'John'],
                ['email' => 'to@example.com', 'name' => null]
            ],
            $addresses->jsonSerialize()
        );
    }
    
    public function testToStringMethod()
    {
        $addresses = new Addresses();
        
        $this->assertSame('[]', $addresses->__toString());
        
        $addresses = new Addresses(
            new Address('from@example.com', 'John'),
        );

        $this->assertSame(
            json_encode([['email' => 'from@example.com', 'name' => 'John']]),
            $addresses->__toString()
        );
    }
}