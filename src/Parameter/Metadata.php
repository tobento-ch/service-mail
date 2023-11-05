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
 * Metadata
 */
class Metadata implements ParameterInterface, JsonSerializable
{
    /**
     * Create a new Metadata.
     *
     * @param array<string, string> $metadata
     */
    public function __construct(
        protected array $metadata
    ) {}
    
    /**
     * Returns the metadata.
     *
     * @return array<string, string>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return ['metadata' => $this->metadata()];
    }
}