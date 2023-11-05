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

namespace Tobento\Service\Mail;

use ArrayIterator;
use Traversable;

/**
 * Addresses
 */
class Addresses implements AddressesInterface
{
    /**
     * @var array<int, AddressInterface>
     */
    protected array $addresses = [];
    
    /**
     * Create a new Addresses.
     *
     * @param AddressInterface ...$address
     */
    public function __construct(
        AddressInterface ...$address,
    ) {
        $this->addresses = $address;
    }

    /**
     * Add a new address.
     *
     * @param AddressInterface $address
     * @return static $this
     */
    public function add(AddressInterface $address): static
    {
        $this->addresses[] = $address;
        
        return $this;
    }
    
    /**
     * Returns a new instance with the mapped addresses.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static
    {
        $new = clone $this;
        $new->addresses = array_map($mapper, $this->addresses);
        return $new;
    }
    
    /**
     * Returns the addresses.
     *
     * @return array<int, AddressInterface>
     */
    public function all(): array
    {
        return $this->addresses;
    }
    
    /**
     * Returns the number of addresses.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->addresses);
    }
    
    /**
     * Returns true if has no addresses, otherwise false.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return $this->count() === 0;
    }
    
    /**
     * Get the iterator. 
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->map(
            fn(AddressInterface $address): array => $address->jsonSerialize()
        )->all();
    }
    
    /**
     * Returns the string representation of the parameters.
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}