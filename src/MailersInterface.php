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
 * MailersInterface
 */
interface MailersInterface
{
    /**
     * Returns the mailer if exists, otherwise null.
     *
     * @param string $name
     * @return null|MailerInterface
     */
    public function mailer(string $name): null|MailerInterface;
    
    /**
     * Returns all mailer names.
     *
     * @return array<int, string>
     */
    public function names(): array;
}