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
use Throwable;

/**
 * MessageNotSent
 */
class MessageNotSent
{
    /**
     * Create a new MessageNotSent.
     *
     * @param MessageInterface $message
     * @param Throwable $exception
     */
    public function __construct(
        protected MessageInterface $message,
        protected Throwable $exception
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
    
    /**
     * Returns the exception.
     *
     * @return Throwable
     */
    public function exception(): Throwable
    {
        return $this->exception;
    }
}