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
 * HasMessage
 */
trait HasMessage
{
    /**
     * @var null|AddressInterface
     */
    protected null|AddressInterface $from = null;
    
    /**
     * @var AddressesInterface
     */
    protected AddressesInterface $to;
    
    /**
     * @var AddressesInterface
     */
    protected AddressesInterface $cc;
    
    /**
     * @var AddressesInterface
     */
    protected AddressesInterface $bcc;
    
    /**
     * @var null|AddressInterface
     */
    protected null|AddressInterface $replyTo = null;
    
    /**
     * @var string
     */
    protected string $subject = '';

    /**
     * @var null|string|TemplateInterface
     */
    protected null|string|TemplateInterface $text = null;
    
    /**
     * @var null|string|TemplateInterface
     */
    protected null|string|TemplateInterface $html = null;
    
    /**
     * @var ParametersInterface
     */
    protected ParametersInterface $parameters;
    
    /**
     * Set the from address.
     *
     * @param string|AddressInterface $address
     * @return static $this
     */
    public function from(string|AddressInterface $address): static
    {
        $this->from = is_string($address) ? new Address(email: $address) : $address;
        
        return $this;
    }
    
    /**
     * Returns the from address.
     *
     * @return null|AddressInterface
     */
    public function getFrom(): null|AddressInterface
    {
        return $this->from;
    }
    
    /**
     * Add an address to send to.
     *
     * @param string|AddressInterface ...$address
     * @return static $this
     */
    public function to(string|AddressInterface ...$address): static
    {
        foreach($address as $adr) {
            $this->to->add(is_string($adr) ? new Address(email: $adr) : $adr);
        }
        
        return $this;
    }
    
    /**
     * Returns the addresses to send to.
     *
     * @return AddressesInterface
     */
    public function getTo(): AddressesInterface
    {
        return $this->to;
    }
    
    /**
     * Add an address to send cc.
     *
     * @param string|AddressInterface ...$address
     * @return static $this
     */
    public function cc(string|AddressInterface ...$address): static
    {
        foreach($address as $adr) {
            $this->cc->add(is_string($adr) ? new Address(email: $adr) : $adr);
        }
        
        return $this;
    }
    
    /**
     * Returns the addresses to send cc.
     *
     * @return AddressesInterface
     */
    public function getCc(): AddressesInterface
    {
        return $this->cc;
    }
    
    /**
     * Add an address to send bcc.
     *
     * @param string|AddressInterface ...$address
     * @return static $this
     */
    public function bcc(string|AddressInterface ...$address): static
    {
        foreach($address as $adr) {
            $this->bcc->add(is_string($adr) ? new Address(email: $adr) : $adr);
        }
        
        return $this;
    }
    
    /**
     * Returns the addresses to send bcc.
     *
     * @return AddressesInterface
     */
    public function getBcc(): AddressesInterface
    {
        return $this->bcc;
    }
    
    /**
     * Set the address to reply to.
     *
     * @param string|AddressInterface $address
     * @return static $this
     */
    public function replyTo(string|AddressInterface $address): static
    {
        $this->replyTo = is_string($address) ? new Address(email: $address) : $address;
        
        return $this;
    }
    
    /**
     * Returns the address to reply to.
     *
     * @return null|AddressInterface
     */
    public function getReplyTo(): null|AddressInterface
    {
        return $this->replyTo;
    }
    
    /**
     * Set the subject.
     *
     * @param string $subject
     * @return static $this
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;
        
        return $this;
    }
    
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
    
    /**
     * Set the text.
     *
     * @param string $text
     * @return static $this
     */
    public function text(string|TemplateInterface $text): static
    {
        $this->text = $text;
        
        return $this;
    }
    
    /**
     * Returns the text or null if none specified.
     *
     * @return null|string|TemplateInterface
     */
    public function getText(): null|string|TemplateInterface
    {
        return $this->text;
    }
    
    /**
     * Set the html.
     *
     * @param string $html|TemplateInterface
     * @return static $this
     */
    public function html(string|TemplateInterface $html): static
    {
        $this->html = $html;
        
        return $this;
    }
    
    /**
     * Returns the html or null if none specified.
     *
     * @return null|string|TemplateInterface
     */
    public function getHtml(): null|string|TemplateInterface
    {
        return $this->html;
    }
    
    /**
     * Set the html as template.
     *
     * @param string $name
     * @param array $data
     * @return static $this
     */
    public function htmlTemplate(string $name, array $data = []): static
    {
        $this->html = new Template(name: $name, data: $data);
        
        return $this;
    }
    
    /**
     * Set the text as template.
     *
     * @param string $name
     * @param array $data
     * @return static $this
     */
    public function textTemplate(string $name, array $data = []): static
    {
        $this->text = new Template(name: $name, data: $data);
        
        return $this;
    }
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }
    
    /**
     * Add a parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function parameter(ParameterInterface $parameter): static
    {
        $this->parameters()->add($parameter);
        
        return $this;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'from' => $this->getFrom()?->jsonSerialize(),
            'to' => $this->getTo()->jsonSerialize(),
            'cc' => $this->getCc()->jsonSerialize(),
            'bcc' => $this->getBcc()->jsonSerialize(),
            'replyTo' => $this->getReplyTo()?->jsonSerialize(),
            'subject' => $this->getSubject(),
            'text' => $this->serializeContent($this->getText()),
            'html' => $this->serializeContent($this->getHtml()),
            'parameters' => $this->parameters()->jsonSerialize(),
        ];
    }
    
    /**
     * Returns the string representation of the parameters.
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
    
    /**
     * Serialize content.
     *
     * @param null|string|TemplateInterface $content
     * @return null|string|array
     */
    protected function serializeContent(null|string|TemplateInterface $content): null|string|array
    {
        if ($content instanceof TemplateInterface) {
            return [
                'name' => $content->name(),
                'data' => $content->data(),
            ];
        }
        
        return $content;
    }
}