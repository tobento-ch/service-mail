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

/**
 * HtmlToTextConverter, will be later remove and replaced
 * with Syfmony HtmlToTextConverterInterface when we use higher PHP version
 *
 * @credits (c) Fabien Potencier <fabien@symfony.com>
 */
class HtmlToTextConverter
{
    /**
     * Covert the specified html to plain text.
     *
     * @param string $html
     * @param null|string $charset
     */
    public function convert(string $html, null|string $charset = null): string
    {
        return strip_tags(preg_replace('{<(head|style)\b.*?</\1>}is', '', $html));
    }
}