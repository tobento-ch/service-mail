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
 * Tags
 */
class Tags implements ParameterInterface, JsonSerializable
{
    /**
     * Create a new Tags.
     *
     * @param array<int, string> $tags
     */
    public function __construct(
        protected array $tags
    ) {}
    
    /**
     * Returns the tags.
     *
     * @return array<int, string> $tags
     */
    public function tags(): array
    {
        return $this->tags;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return ['tags' => $this->tags()];
    }
}