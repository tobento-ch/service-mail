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

use Tobento\Service\Mail\MailerFactoryInterface;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\QueueHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\NullTransport;

/**
 * SmtpMailerFactory
 */
class SmtpMailerFactory implements MailerFactoryInterface
{
    /**
     * Create a new MailerFactory.
     *
     * @param EmailFactoryInterface $emailFactory
     * @param null|QueueHandlerInterface $queueHandler
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected EmailFactoryInterface $emailFactory,
        protected null|QueueHandlerInterface $queueHandler = null,
        protected null|EventDispatcherInterface $eventDispatcher = null
    ) {}
    
    /**
     * Create a new mailer based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return MailerInterface
     */
    public function createMailer(string $name, array $config = []): MailerInterface
    {
        if (empty($config)) {
            $transport = new NullTransport();
        } else {
            $transport = $this->createTransportFromConfig($config);
        }
        
        $emailFactory = $this->emailFactory;
        
        if (isset($config['defaults']) && is_array($config['defaults'])) {
            $emailFactory = $this->emailFactory->withConfig($config['defaults']);
        }
        
        return new Mailer(
            name: $name,
            emailFactory: $emailFactory,
            transport: $transport,
            queueHandler: $this->queueHandler,
            eventDispatcher: $this->eventDispatcher,
        );
    }
    
    /**
     * Creates the transport from the config.
     *
     * @param array $config
     * @return EsmtpTransport
     */
    protected function createTransportFromConfig(array $config = []): EsmtpTransport
    {
        return (new EsmtpTransportFactory())->create(new Dsn(
            !empty($config['encryption']) && $config['encryption'] === 'tls' ? (($config['port'] == 465) ? 'smtps' : 'smtp') : '',
            $config['host'] ?? '',
            $config['user'] ?? null,
            $config['password'] ?? null,
            $config['port'] ?? null,
            [],
        ));
    }
}