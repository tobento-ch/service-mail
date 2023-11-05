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

use Psr\Container\ContainerInterface;
use Tobento\Service\Autowire\Autowire;

/**
 * LazyMailers
 */
class LazyMailers implements MailersInterface, MailerInterface
{
    /**
     * @var Autowire
     */
    protected Autowire $autowire;
    
    /**
     * @var array<string, MailerInterface>
     */
    protected array $createdMailers = [];
    
    /**
     * Create a new LazyMailers.
     *
     * @param ContainerInterface $container
     * @param array $mailers
     */
    public function __construct(
        ContainerInterface $container,
        protected array $mailers,
    ) {
        $this->autowire = new Autowire($container);
    }
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'lazyMailers';
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
        if (isset($this->createdMailers[$name])) {
            return $this->createdMailers[$name];
        }
        
        if (!isset($this->mailers[$name])) {
            return null;
        }
        
        if ($this->mailers[$name] instanceof MailerInterface) {
            return $this->mailers[$name];
        }
        
        // create mailer from callable:
        if (is_callable($this->mailers[$name])) {
            return $this->autowire->call($this->mailers[$name], ['name' => $name]);
        }
        
        // create mailer from factory:
        if (!isset($this->mailers[$name]['factory'])) {
            return null;
        }
        
        $factory = $this->autowire->resolve($this->mailers[$name]['factory']);
        
        if (! $factory instanceof MailerFactoryInterface) {
            return null;
        }
        
        $config = $this->mailers[$name]['config'] ?? [];
        
        return $this->createdMailers[$name] = $factory->createMailer($name, $config);
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
            $name = $this->getFirstMailerName();
        } else {
            $name = $sendWithMailer->name();
        }
        
        $mailer = $this->mailer($name);
        
        if (is_null($mailer)) {
            throw new MailerException('Mailer ['.$name.'] not found');
        }
        
        $mailer->send($message);
    }
    
    /**
     * Returns the first mailer name.
     *
     * @return string
     */
    protected function getFirstMailerName(): string
    {
        $firstKey = array_key_first($this->mailers);
        
        return $firstKey ?: 'default';
    }
}