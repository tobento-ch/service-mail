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

namespace Tobento\Service\Mail\Symfony;

use Tobento\Service\Mail\MessageInterface;
use Symfony\Component\Mime\Email;

/**
 * EmailFactoryInterface
 */
interface EmailFactoryInterface
{
    /**
     * Returns a new instance with the specified config.
     *
     * @param array $config
     * @return static
     */
    public function withConfig(array $config): static;
    
    /**
     * Create email from message.
     *
     * @param MessageInterface $message
     * @return Email
     */
    public function createEmailFromMessage(MessageInterface $message): Email;
}