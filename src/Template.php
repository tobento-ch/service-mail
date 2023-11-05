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
 * Template
 */
class Template implements TemplateInterface
{
    /**
     * Create a new Template.
     *
     * @param string $name
     * @param array $data
     */
    public function __construct(
        protected string $name,
        protected array $data
    ) {}
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the data.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}