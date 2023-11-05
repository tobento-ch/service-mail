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
use InvalidArgumentException;

/**
 * ResourceFile
 */
class ResourceFile implements ParameterInterface
{
    /**
     * Create a new ResourceFile.
     *
     * @param resource $resource
     * @param string $filename
     * @param null|string $mimeType
     * @param bool $inline
     */
    public function __construct(
        protected $resource,
        protected string $filename,
        protected null|string $mimeType = null,
        protected bool $inline = false,
    ) {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid resource specified');
        }
    }
    
    /**
     * Returns the resource.
     *
     * @return resource
     */
    public function resource()
    {
        return $this->resource;
    }
    
    /**
     * Returns the filename.
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }
    
    /**
     * Returns the mime type.
     *
     * @return string
     */
    public function mimeType(): null|string
    {
        return $this->mimeType;
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
}