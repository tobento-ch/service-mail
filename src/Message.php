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
 * Message
 */
class Message implements MessageInterface
{
    use HasMessage;
    
    /**
     * Create a new Message.
     */
    public function __construct()
    {
        $this->to = new Addresses();
        $this->cc = new Addresses();
        $this->bcc = new Addresses();
        $this->parameters = new Parameters();
    }
}