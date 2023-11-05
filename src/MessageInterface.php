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

use JsonSerializable;
use Stringable;

/**
 * MessageInterface
 */
interface MessageInterface extends JsonSerializable, Stringable
{
    /**
     * Returns the from address.
     *
     * @return null|AddressInterface
     */
    public function getFrom(): null|AddressInterface;
    
    /**
     * Returns the addresses to send to.
     *
     * @return AddressesInterface
     */
    public function getTo(): AddressesInterface;
    
    /**
     * Returns the addresses to send cc.
     *
     * @return AddressesInterface
     */
    public function getCc(): AddressesInterface;
    
    /**
     * Returns the addresses to send bcc.
     *
     * @return AddressesInterface
     */
    public function getBcc(): AddressesInterface;
    
    /**
     * Returns the address to reply to.
     *
     * @return null|AddressInterface
     */
    public function getReplyTo(): null|AddressInterface;
    
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string;
    
    /**
     * Returns the text or null if none specified.
     *
     * @return null|string|TemplateInterface
     */
    public function getText(): null|string|TemplateInterface;
    
    /**
     * Set the text.
     *
     * @param string $text
     * @return static $this
     */
    public function text(string|TemplateInterface $text): static;
    
    /**
     * Returns the html or null if none specified.
     *
     * @return null|string|TemplateInterface
     */
    public function getHtml(): null|string|TemplateInterface;
    
    /**
     * Set the html.
     *
     * @param string $html|TemplateInterface
     * @return static $this
     */
    public function html(string|TemplateInterface $html): static;
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface;
}