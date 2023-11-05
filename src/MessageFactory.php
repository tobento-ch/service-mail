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

use JsonException;
use Throwable;

/**
 * MessageFactory
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var ParametersFactoryInterface
     */
    protected ParametersFactoryInterface $parametersFactory;
    
    /**
     * Create a new MessageFactory.
     *
     * @param null|ParametersFactoryInterface $parametersFactory
     */
    public function __construct(
        null|ParametersFactoryInterface $parametersFactory = null,
    ) {
        $this->parametersFactory = $parametersFactory ?: new ParametersFactory();
    }

    /**
     * Create a message from array.
     *
     * @param array $message
     * @return MessageInterface
     * @throws MessageException
     */
    public function createFromArray(array $message): MessageInterface
    {
        $msg = new Message();
        
        if (!is_null($to = $this->toAddresses($message['to'] ?? null))) {
            $msg->to(...$to->all());
        }
        
        if (!is_null($cc = $this->toAddresses($message['cc'] ?? null))) {
            $msg->cc(...$cc->all());
        }
        
        if (!is_null($bcc = $this->toAddresses($message['bcc'] ?? null))) {
            $msg->bcc(...$bcc->all());
        }
        
        if (!is_null($from = $this->toAddress($message['from'] ?? null))) {
            $msg->from($from);
        }
        
        if (!is_null($replyTo = $this->toAddress($message['replyTo'] ?? null))) {
            $msg->replyTo($replyTo);
        }
        
        $msg->subject($this->toString($message['subject'] ?? ''));
        
        if (!is_null($text = $this->toContent($message['text'] ?? null))) {
            $msg->text($text);
        }
        
        if (!is_null($html = $this->toContent($message['html'] ?? null))) {
            $msg->html($html);
        }
        
        if (isset($message['parameters']) && is_array($message['parameters'])) {
            try {
                foreach($this->parametersFactory->createFromArray($message['parameters']) as $parameter) {
                    $msg->parameter($parameter);
                }
            } catch (Throwable $e) {
                throw new MessageException($e->getMessage(), (int)$e->getCode(), $e);
            }
        }
        
        return $msg;
    }
    
    /**
     * Create a message from JSON string.
     *
     * @param string $json
     * @return MessageInterface
     * @throws MessageException
     */
    public function createFromJsonString(string $json): MessageInterface
    {
        try {
            $data = json_decode($json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new MessageException($e->getMessage(), (int)$e->getCode(), $e);
        }
        
        if (!is_array($data)) {
            throw new MessageException('Invalid parameters data: must be a valid array');
        }
        
        return $this->createFromArray($data);
    }

    /**
     * To address.
     *
     * @param mixed $address
     * @return null|AddressesInterface
     */
    protected function toAddresses(mixed $addresses): null|AddressesInterface
    {
        if (is_null($addresses)) {
            return null;
        }
        
        if (!is_array($addresses)) {
            return new Addresses();
        }
        
        $adrs = new Addresses();
        
        foreach($addresses as $address) {
            if (!is_null($adr = $this->toAddress($address))) {
                $adrs->add($adr);
            }
        }
        
        return $adrs;
    }
    
    /**
     * To address.
     *
     * @param mixed $address
     * @return null|AddressInterface
     */
    protected function toAddress(mixed $address): null|AddressInterface
    {
        if (is_array($address)) {
            $email = $address['email'] ?? $address[0] ?? '';
            $name = $address['name'] ?? $address[1] ?? null;
            return new Address(
                email: $this->toString($email),
                name: is_string($name) ? $name : null,
            );
        }
        
        if (is_string($address)) {
            return new Address(email: $address);
        }
        
        return null;
    }
    
    /**
     * To content.
     *
     * @param mixed $content
     * @return null|string|TemplateInterface
     */
    protected function toContent(mixed $content): null|string|TemplateInterface
    {
        if (is_array($content)) {
            $data = $content['data'] ?? [];
            
            return new Template(
                name: $this->toString($content['name'] ?? ''),
                data: is_array($data) ? $data : [],
            );
        }
        
        if (is_string($content)) {
            return $content;
        }
        
        return null;
    }
    
    /**
     * To string.
     *
     * @param mixed $value
     * @return string
     */
    protected function toString(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}