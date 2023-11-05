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
 * SendWithMailer
 */
class SendWithMailer implements ParameterInterface, JsonSerializable
{
    /**
     * Create a new SendWithMailer.
     *
     * @param string $name The mailer name.
     */
    public function __construct(
        protected string $name,
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
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
        ];
    }
}