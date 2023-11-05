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

use IteratorAggregate;
use JsonSerializable;
use Stringable;
use Countable;

/**
 * AddressesInterface
 */
interface AddressesInterface extends IteratorAggregate, JsonSerializable, Stringable, Countable
{
    /**
     * Add a new address.
     *
     * @param AddressInterface $address
     * @return static $this
     */
    public function add(AddressInterface $address): static;
    
    /**
     * Returns a new instance with the mapped addresses.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static;
    
    /**
     * Returns the addresses.
     *
     * @return array<int, AddressInterface>
     */
    public function all(): array;
    
    /**
     * Returns true if has no addresses, otherwise false.
     *
     * @return bool
     */
    public function empty(): bool;
}