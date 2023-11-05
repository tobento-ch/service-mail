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

namespace Tobento\Service\Mail\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\Template;
use Tobento\Service\Mail\TemplateInterface;

/**
 * TemplateTest
 */
class TemplateTest extends TestCase
{
    public function testTemplate()
    {
        $template = new Template(
            name: 'welcome',
            data: ['key' => 'value']
        );
        
        $this->assertInstanceof(TemplateInterface::class, $template);
        $this->assertSame('welcome', $template->name());
        $this->assertSame(['key' => 'value'], $template->data());
    }
}