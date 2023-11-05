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

namespace Tobento\Service\Mail\Queue;

use Tobento\Service\Queue\JobHandlerInterface;
use Tobento\Service\Queue\JobInterface;
use Tobento\Service\Queue\JobException;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MessageFactoryInterface;

/**
 * MailJobHandler
 */
class MailJobHandler implements JobHandlerInterface
{
    /**
     * Create a new MailJobHandler.
     *
     * @param MailerInterface $mailer
     * @param MessageFactoryInterface $messageFactory
     */
    public function __construct(
        private MailerInterface $mailer,
        private MessageFactoryInterface $messageFactory,
    ) {}

    /**
     * Handles the specified job.
     *
     * @param JobInterface $job
     * @return void
     * @throws JobException
     */
    public function handleJob(JobInterface $job): void
    {
        $message = $this->messageFactory->createFromArray($job->getPayload());

        $this->mailer->send($message);
    }
}