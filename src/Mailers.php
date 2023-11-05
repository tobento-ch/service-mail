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
 * LMailers
 */
class Mailers implements MailersInterface, MailerInterface
{
    /**
     * @var array<string, MailerInterface>
     */
    protected array $mailers = [];
    
    /**
     * Create a new Mailers.
     *
     * @param MailerInterface ...$mailers
     */
    public function __construct(
        MailerInterface ...$mailers,
    ) {
        foreach($mailers as $mailer) {
            $this->mailers[$mailer->name()] = $mailer;
        }
    }
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'mailers';
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
     * Returns the mailer if exists, otherwise null.
     *
     * @param string $name
     * @return null|MailerInterface
     */
    public function mailer(string $name): null|MailerInterface
    {
        return $this->mailers[$name] ?? null;
    }
    
    /**
     * Sends the message.
     *
     * @param MessageInterface $message
     * @return void
     * @throws MailerException
     * @psalm-suppress UndefinedInterfaceMethod
     */
    protected function sendMessage(MessageInterface $message): void
    {
        $sendWithMailer = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\SendWithMailer
        )->first();
        
        if (is_null($sendWithMailer)) {
            $mailer = $this->getFirstMailer();
        } else {
            $mailer = $this->mailer($sendWithMailer->name());
        }
        
        if (is_null($mailer)) {
            throw new MailerException('No mailer found to send message');
        }
        
        $mailer->send($message);
    }
    
    /**
     * Returns the first mailer or null if none.
     *
     * @return null|MailerInterface
     */
    protected function getFirstMailer(): null|MailerInterface
    {
        $firstKey = array_key_first($this->mailers);
        
        return is_null($firstKey) ? null : $this->mailers[$firstKey];
    }
}