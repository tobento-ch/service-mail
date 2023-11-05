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

/**
 * ParametersInterface
 */
interface ParametersInterface extends IteratorAggregate, JsonSerializable, Stringable
{
    /**
     * Add a new parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function add(ParameterInterface $parameter): static;
    
    /**
     * Returns a new instance with the filtered parameters.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns the first parameter of null if none.
     *
     * @return null|ParameterInterface
     */
    public function first(): null|ParameterInterface;
    
    /**
     * Returns the parameters.
     *
     * @return array<int, ParameterInterface>
     */
    public function all(): array;
}