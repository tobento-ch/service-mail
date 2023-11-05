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
 * IdHeader
 */
class IdHeader implements ParameterInterface, JsonSerializable, Headerable
{
    /**
     * Create a new IdHeader.
     *
     * @param string $name
     * @param string $ids
     */
    public function __construct(
        protected string $name,
        protected string|array $ids
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
     * Returns the ids.
     *
     * @return string|array
     */
    public function ids(): string|array
    {
        return $this->ids;
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
            'ids' => $this->ids(),
        ];
    }
}