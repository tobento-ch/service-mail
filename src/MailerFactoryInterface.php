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
 * MailerFactoryInterface
 */
interface MailerFactoryInterface
{
    /**
     * Create a new mailer based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return MailerInterface
     */
    public function createMailer(string $name, array $config = []): MailerInterface;
}