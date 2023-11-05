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

namespace Tobento\Service\Mail\Event;

use Tobento\Service\Mail\MessageInterface;

/**
 * MessageQueued
 */
class MessageQueued
{
    /**
     * Create a new MessageQueued.
     *
     * @param MessageInterface $message
     */
    public function __construct(
        protected MessageInterface $message
    ) {}
    
    /**
     * Returns the message.
     *
     * @return MessageInterface
     */
    public function message(): MessageInterface
    {
        return $this->message;
    }
}