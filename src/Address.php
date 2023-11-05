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

/**
 * Address
 */
class Address implements AddressInterface
{
    /**
     * Create a new Address.
     *
     * @param string $email
     * @param null|string $name
     */
    public function __construct(
        protected string $email,
        protected null|string $name = null
    ) {}
    
    /**
     * Returns the email.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }
    
    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function name(): null|string
    {
        return $this->name;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'email' => $this->email(),
            'name' => $this->name(),
        ];
    }
}