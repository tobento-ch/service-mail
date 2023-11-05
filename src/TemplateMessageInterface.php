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

/**
 * TemplateMessageInterface
 */
interface TemplateMessageInterface
{
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function subject(): string;
    
    /**
     * Emdeds a file (image) and returns the image src.
     *
     * @param string|File|StreamInterface $file
     * @param null|string $mimeType
     * @return string Must be escaped or a save string
     */
    public function embed(string|File|StreamInterface $file, null|string $mimeType = null): string;
}