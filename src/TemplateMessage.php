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

use Tobento\Service\Filesystem\File;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * TemplateMessage
 *
 * The embed method will return the img src as Base64 encoded image data only.
 */
class TemplateMessage implements TemplateMessageInterface
{
    /**
     * Create a new TemplateMessage.
     *
     * @param string $subject
     */
    public function __construct(
        protected string $subject,
    ) {}
    
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function subject(): string
    {
        return $this->subject;
    }
    
    /**
     * Emdeds a file (image) and returns the image src.
     *
     * @param string|File|StreamInterface $file
     * @param null|string $mimeType
     * @return string Must be escaped or a save string
     */
    public function embed(string|File|StreamInterface $file, null|string $mimeType = null): string
    {
        if ($file instanceof StreamInterface) {
            
            if (is_null($mimeType)) {
                throw new InvalidArgumentException('You will need to specify a mime type');
            }
            
            return $this->esc(sprintf(
                'data:%s;base64,%s',
                $mimeType,
                base64_encode((string)$file)
            ));
        }
        
        if (is_string($file)) {            
            $file = new File($file);
        }
        
        if (!$file->isHtmlImage()) {
            throw new InvalidArgumentException('File must be an image');
        }
        
        return $this->esc(sprintf(
            'data:%s;base64,%s',
            $file->getMimeType(),
            base64_encode($file->getContent())
        ));
    }
    
    /**
     * Escapes string with htmlspecialchars.
     * 
     * @param string $string
     * @param int $flags
     * @param string $encoding
     * @param bool $double_encode
     * @return string
     */
    protected function esc(
        string $string,
        int $flags = ENT_QUOTES,
        string $encoding = 'UTF-8',
        bool $double_encode = true
    ): string {        
        return htmlspecialchars($string, $flags, $encoding, $double_encode);
    }
}