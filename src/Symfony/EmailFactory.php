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

use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\AddressInterface;
use Tobento\Service\Mail\TemplateInterface;
use Tobento\Service\Mail\Template;
use Tobento\Service\Mail\ParametersInterface;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Psr\Http\Message\StreamInterface;

/**
 * EmailFactory
 */
class EmailFactory implements EmailFactoryInterface
{
    /**
     * Create a new EmailFactory.
     *
     * @param RendererInterface $renderer
     * @param array $config
     */
    public function __construct(
        protected RendererInterface $renderer,
        protected array $config = [],
    ) {}

    /**
     * Returns a new instance with the specified config.
     *
     * @param array $config
     * @return static
     */
    public function withConfig(array $config): static
    {
        $new = clone $this;
        $new->config = $config;
        return $new;
    }
    
    /**
     * Create email from message.
     *
     * @param MessageInterface $message
     * @return Email
     */
    public function createEmailFromMessage(MessageInterface $message): Email
    {
        $email = new Email();
        $email = $this->handleConfig($message, $email);
        $email = $this->handleAddresses($message, $email);
        $email = $this->handleContent($message, $email);
        $email = $this->handleFiles($message, $email);
        $email = $this->handleHeaders($message, $email);
        $email = $this->handleMetadata($message, $email);
        $email = $this->handleTags($message, $email);
        return $email;
    }

    /**
     * Handles the config.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleConfig(MessageInterface $message, Email $email): Email
    {
        if (isset($this->config['from'])) {
            if ($this->config['from'] instanceof AddressInterface) {
                $email->from(new Address(
                    $this->config['from']->email(),
                    (string)$this->config['from']->name()
                ));
            } elseif (is_string($this->config['from'])) {
                $email->from(new Address($this->config['from']));
            }
        }
        
        if (isset($this->config['replyTo'])) {
            if ($this->config['replyTo'] instanceof AddressInterface) {
                $email->replyTo(new Address(
                    $this->config['replyTo']->email(),
                    (string)$this->config['replyTo']->name()
                ));
            } elseif (is_string($this->config['replyTo'])) {
                $email->replyTo(new Address($this->config['replyTo']));
            }
        }
        
        if (
            isset($this->config['parameters'])
            && $this->config['parameters'] instanceof ParametersInterface
        ) {
            foreach($this->config['parameters'] as $parameter) {
                $message->parameters()->add($parameter);
            }
        }
        
        return $email;
    }
    
    /**
     * Handles the addresses.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     * @psalm-suppress InvalidArgument
     */
    protected function handleAddresses(MessageInterface $message, Email $email): Email
    {
        // handle from:        
        if ($message->getFrom()) {
            $email->from(new Address(
                $message->getFrom()->email(),
                (string)$message->getFrom()->name()
            ));
        }
        
        // handle reply to:        
        if ($message->getReplyTo()) {
            $email->replyTo(new Address(
                $message->getReplyTo()->email(),
                (string)$message->getReplyTo()->name()
            ));
        }
        
        // handle to:        
        if (isset($this->config['alwaysTo'])) {
            if ($this->config['alwaysTo'] instanceof AddressInterface) {
                $email->to(new Address(
                    $this->config['alwaysTo']->email(),
                    (string)$this->config['alwaysTo']->name()
                ));
            } elseif (is_string($this->config['alwaysTo'])) {
                $email->to(new Address($this->config['alwaysTo']));
            }
        }
        
        if (empty($email->getTo())) {
            $to = $message->getTo()->map(function(AddressInterface $address): Address {
                return new Address($address->email(), (string)$address->name());
            })->all();
            
            $email->to(...$to);
        }
        
        // handle cc and bcc:
        $cc = $message->getCc()->map(function(AddressInterface $address): Address {
            return new Address($address->email(), (string)$address->name());
        })->all();
        
        $bcc = $message->getBcc()->map(function(AddressInterface $address): Address {
            return new Address($address->email(), (string)$address->name());
        })->all();
        
        return $email->cc(...$cc)->bcc(...$bcc);
    }
    
    /**
     * Handles the content.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleContent(MessageInterface $message, Email $email): Email
    {
        $email->subject($message->getSubject());
        
        if ($message->getHtml() instanceof TemplateInterface) {
            
            $data = $message->getHtml()->data();
            
            $data['message'] = new TemplateMessage($email, $message->getSubject());
            
            $template = new Template(
                name: $message->getHtml()->name(),
                data: $data
            );
            
            $html = $this->renderer->renderTemplate($template);
            $email->html($html);
        } elseif (is_string($message->getHtml())) {
            $email->html($message->getHtml());
        }
        
        if ($message->getText() instanceof TemplateInterface) {
            $text = $this->renderer->renderTemplate($message->getText());
            $email->text($text);
        } elseif (is_string($message->getText())) {
            $email->text($message->getText());
        }
        
        // create text from html:
        // will be redone with Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface
        // in a later version when we use php >= 8.1
        if (is_null($message->getText()) && is_string($email->getHtmlBody())) {
            $text = (new HtmlToTextConverter)->convert($email->getHtmlBody());            
            $email->text($text);
        }        
        
        return $email;
    }
    
    /**
     * Handles the files.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleFiles(MessageInterface $message, Email $email): Email
    {
        $files = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\File
        );
        
        foreach($files as $file) {
            
            // skip file if it does not exist:
            if (!$file->file()->isFile()) {
                continue;
            }
            
            if ($file->isInline()) {
                $email->embedFromPath($file->file()->getFile(), $file->filename());
            } else {
                $email->attachFromPath($file->file()->getFile(), $file->filename());
            }
            
            // mailer 6.2 with php 8.1
            //$email->addPart(new DataPart(new File($file->file()->getFile())));
        }
        
        $files = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\StreamFile
        );
        
        foreach($files as $file) {
            if ($file->isInline()) {
                $email->embed(
                    $this->createResourceFromStream($file->stream()),
                    $file->filename(),
                    $file->mimeType()
                );
            } else {
                $email->attach(
                    $this->createResourceFromStream($file->stream()),
                    $file->filename(),
                    $file->mimeType()
                );
            }
        }
        
        $files = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\ResourceFile
        );
        
        foreach($files as $file) {
            if ($file->isInline()) {
                $email->embed($file->resource(), $file->filename(), $file->mimeType());
            } else {
                $email->attach($file->resource(), $file->filename(), $file->mimeType());
            }
        }
        
        return $email;
    }

    /**
     * Create resource from stream.
     *
     * @param StreamInterface $stream
     * @return resource
     */
    protected function createResourceFromStream(StreamInterface $stream)
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, (string)$stream);
        return $resource;
    }
    
    /**
     * Handles the headers.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleHeaders(MessageInterface $message, Email $email): Email
    {
        $headers = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\Headerable
        );
        
        foreach($headers as $header) {
            $this->handleHeader($header, $email);
        }
        
        return $email;
    }
    
    /**
     * Handle the header.
     *
     * @param Parameter\Headerable $header
     * @param Email $email
     * @return void
     */
    protected function handleHeader(Parameter\Headerable $header, Email $email): void
    {
        switch ($header::class) {
            case Parameter\TextHeader::class:
                $email->getHeaders()->addTextHeader($header->name(), $header->value());
                break;
            case Parameter\IdHeader::class:
                if (strtolower($header->name()) === 'references') {
                    $email->getHeaders()->addTextHeader($header->name(), implode(' ', $header->ids()));
                } else {
                    $email->getHeaders()->addIdHeader($header->name(), $header->ids());
                }
                break;
            case Parameter\PathHeader::class:
                $email->getHeaders()->addPathHeader($header->name(), $header->address()->email());
                break;                
        }
    }
    
    /**
     * Handles the metadata.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleMetadata(MessageInterface $message, Email $email): Email
    {
        $metadata = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\Metadata
        );
        
        foreach($metadata as $md) {
            foreach($md->metadata() as $key => $value) {
                $email->getHeaders()->add(new MetadataHeader($key, $value));
            }
        }
        
        return $email;
    }
    
    /**
     * Handles the tags.
     *
     * @param MessageInterface $message
     * @param Email $email
     * @return Email
     */
    protected function handleTags(MessageInterface $message, Email $email): Email
    {
        $tags = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\Tags
        );
        
        foreach($tags as $tag) {
            foreach($tag->tags() as $value) {
                $email->getHeaders()->add(new TagHeader($value));
            }
        }
        
        return $email;
    }
}