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

use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\TemplateInterface;
use Tobento\Service\Queue\QueueInterface;
use Tobento\Service\Queue\Job;

/**
 * QueueHandler
 */
class QueueHandler implements QueueHandlerInterface
{
    /**
     * Create a new QueueHandler.
     *
     * @param QueueInterface $queue
     * @param RendererInterface $renderer
     * @param null|string $queueName The default queue used if no specific is defined on the message.
     */
    public function __construct(
        protected QueueInterface $queue,
        protected RendererInterface $renderer,
        protected null|string $queueName = null,
    ) {}
    
    /**
     * Handle the message.
     *
     * @param MessageInterface $message
     * @return void
     */
    public function handle(MessageInterface $message): void
    {
        $queue = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\Queue
        )->first();
        
        // render templates:
        if (
            $queue instanceof Parameter\Queue
            && $queue->renderTemplates()
        ) {
            if ($message->getText() instanceof TemplateInterface) {
                $message->text($this->renderer->renderTemplate($message->getText()));
            }
            
            if ($message->getHtml() instanceof TemplateInterface) {
                $message->html($this->renderer->renderTemplate($message->getHtml()));
            }
        }
                
        $job = new Job(
            name: MailJobHandler::class,
            payload: $message->jsonSerialize(),
        );
        
        if ($queue instanceof Parameter\Queue) {
            
            if ($queue->name()) {
                $job->queue($queue->name());
            } elseif ($this->queueName) {
                $job->queue($this->queueName);
            }
            
            if ($queue->delay() > 0) {
                $job->delay($queue->delay());
            }
            
            $job->retry($queue->retry());
            $job->priority($queue->priority());
            
            if ($queue->encrypt()) {
                $job->encrypt();
            }
        }
        
        $this->queue->push($job);
    }
}