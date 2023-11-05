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
 * MailerInterface
 */
interface MailerInterface
{
    /**
     * Returns the mailer name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Send one or multiple message(s).
     *
     * @param MessageInterface ...$message
     * @return void
     * @throws MailerException
     */
    public function send(MessageInterface ...$message): void;
}