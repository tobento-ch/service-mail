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

namespace Tobento\Service\Mail\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\Symfony\HtmlToTextConverter;

/**
 * HtmlToTextConverterTest
 */
class HtmlToTextConverterTest extends TestCase
{
    public function testConvert()
    {
        $text = (new HtmlToTextConverter())->convert(
            html: '<p>lorem</p>'
        );

        $this->assertSame('lorem', $text);
    }
}