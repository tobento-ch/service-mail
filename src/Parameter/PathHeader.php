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

namespace Tobento\Service\Mail\Parameter;

use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\AddressInterface;
use Tobento\Service\Mail\Address;
use JsonSerializable;

/**
 * PathHeader
 */
class PathHeader implements ParameterInterface, JsonSerializable, Headerable
{
    /**
     * @var AddressInterface
     */
    protected AddressInterface $address;
    
    /**
     * Create a new PathHeader.
     *
     * @param string $name
     * @param string|array|AddressInterface $address
     */
    public function __construct(
        protected string $name,
        string|array|AddressInterface $address
    ) {
        if (is_string($address)) {
            $address = [$address];
        }
        
        if (is_array($address)) {
            $email = $address['email'] ?? $address[0] ?? '';
            $name = $address['name'] ?? $address[1] ?? null;
            $this->address = new Address(email: $email, name: $name);
        } else {
            $this->address = $address;
        }
    }
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the address.
     *
     * @return AddressInterface
     */
    public function address(): AddressInterface
    {
        return $this->address;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'address' => $this->address()->jsonSerialize(),
        ];
    }
}