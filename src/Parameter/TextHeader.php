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
use JsonSerializable;

/**
 * TextHeader
 */
class TextHeader implements ParameterInterface, JsonSerializable, Headerable
{
    /**
     * Create a new TextHeader.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(
        protected string $name,
        protected string $value
    ) {}
    
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
     * Returns the value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
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
            'value' => $this->value(),
        ];
    }
}