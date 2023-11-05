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
 * RendererInterface
 */
interface RendererInterface
{
    /**
     * Returns the evaluated content of the template rendered.
     *
     * @param TemplateInterface $template
     * @param bool $withInlineCssStyles
     * @return string
     */
    public function renderTemplate(TemplateInterface $template, bool $withInlineCssStyles = true): string;
}