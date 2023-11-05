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

use Tobento\Service\Mail\TemplateMessageInterface;
use Tobento\Service\Filesystem\File;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File as FilePart;
use Psr\Http\Message\StreamInterface;

/**
 * TemplateMessage
 */
class TemplateMessage implements TemplateMessageInterface
{
    /**
     * Create a new TemplateMessage.
     *
     * @param Email $email
     * @param string $subject
     */
    public function __construct(
        protected Email $email,
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
        $cid = bin2hex(random_bytes(10));
        
        if ($file instanceof StreamInterface) {
            $resource = fopen('php://temp', 'r+');
            fwrite($resource, (string)$file);
            
            $this->email->embed($resource, $cid, (string)$mimeType);
            return 'cid:'.$cid;
        }
        
        if (is_string($file)) {
            $file = new File($file);
        }
        
        $this->email->embedFromPath($file->getFile(), $cid);
        
        // mailer 6.2 with php 8.1
        // $this->email->addPart((new DataPart(new FilePart($file->getFile()), $cid))->asInline());
        
        return 'cid:'.$cid;
    }
}