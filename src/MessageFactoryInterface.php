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
 * MessageFactoryInterface
 */
interface MessageFactoryInterface
{
    /**
     * Create a message from array.
     *
     * @param array $message
     * @return MessageInterface
     * @throws MessageException
     */
    public function createFromArray(array $message): MessageInterface;
    
    /**
     * Create a message from JSON string.
     *
     * @param string $json
     * @return MessageInterface
     * @throws MessageException
     */
    public function createFromJsonString(string $json): MessageInterface;
}