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
 * QueueHandlerInterface
 */
interface QueueHandlerInterface
{
    /**
     * Handle the message.
     *
     * @param MessageInterface $message
     * @return void
     */
    public function handle(MessageInterface $message): void;
}