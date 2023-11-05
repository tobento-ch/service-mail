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

use Psr\EventDispatcher\EventDispatcherInterface;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\Event;
use Tobento\Service\Mail\MailerException;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Mailer
 */
class Mailer implements MailerInterface
{
    /**
     * Create a new Mailer.
     *
     * @param string $name
     * @param EmailFactoryInterface $emailFactory
     * @param TransportInterface $transport
     * @param null|QueueHandlerInterface $queueHandler
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected string $name,
        protected EmailFactoryInterface $emailFactory,
        protected TransportInterface $transport,
        protected null|QueueHandlerInterface $queueHandler = null,
        protected null|EventDispatcherInterface $eventDispatcher = null,
    ) {}
    
    /**
     * Returns the mailer name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Send one or multiple message(s).
     *
     * @param MessageInterface ...$message
     * @return void
     * @throws MailerException
     */
    public function send(MessageInterface ...$message): void
    {
        foreach($message as $msg) {
            $this->sendMessage($msg);
        }
    }
    
    /**
     * Returns the email factory.
     *
     * @return EmailFactoryInterface
     */
    public function emailFactory(): EmailFactoryInterface
    {
        return $this->emailFactory;
    }
    
    /**
     * Returns the email factory.
     *
     * @return TransportInterface
     */
    public function transport(): TransportInterface
    {
        return $this->transport;
    }    
    
    /**
     * Returns the queue handler.
     *
     * @return null|QueueHandlerInterface
     */
    public function queueHandler(): null|QueueHandlerInterface
    {
        return $this->queueHandler;
    }
    
    /**
     * Returns the event dispatcher.
     *
     * @return null|EventDispatcherInterface
     */
    public function eventDispatcher(): null|EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Sends the message.
     *
     * @param MessageInterface $message
     * @return void
     * @throws MailerException
     */
    protected function sendMessage(MessageInterface $message): void
    {
        if ($this->queueHandler) {
            
            $queue = $message->parameters()->filter(
                fn(ParameterInterface $p): bool => $p instanceof Parameter\Queue
            )->first();
            
            if (!is_null($queue)) {
                $this->queueHandler->handle($message);
                
                $this->eventDispatcher?->dispatch(new Event\MessageQueued($message));
                return;
            }
        }
        
        try {
            $this->transport->send($this->emailFactory->createEmailFromMessage($message));
        } catch (TransportExceptionInterface $e) {
            $this->eventDispatcher?->dispatch(new Event\MessageNotSent($message, $e));
            
            throw new MailerException($e->getMessage(), 0, $e);
        }
        
        $this->eventDispatcher?->dispatch(new Event\MessageSent($message));
    }
}