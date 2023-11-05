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

namespace Tobento\Service\Mail\Parameter;

use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Filesystem\File as BaseFile;
use JsonSerializable;

/**
 * File
 */
class File implements ParameterInterface, JsonSerializable
{
    /**
     * @var BaseFile
     */
    protected BaseFile $file;
    
    /**
     * Create a new File.
     *
     * @param string|BaseFile $file
     * @param null|string $filename
     * @param bool $inline
     */
    public function __construct(
        string|BaseFile $file,
        protected null|string $filename = null,
        protected bool $inline = false,
    ) {
        $this->file = is_string($file) ? new BaseFile($file) : $file;
    }
    
    /**
     * Returns the file.
     *
     * @return BaseFile
     */
    public function file(): BaseFile
    {
        return $this->file;
    }
    
    /**
     * Returns the filename.
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->filename ?: $this->file->getBasename();
    }
    
    /**
     * Returns the inline.
     *
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->inline;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'file' => $this->file()->getFile(),
            'filename' => $this->filename(),
            'inline' => $this->isInline(),
        ];
    }
}