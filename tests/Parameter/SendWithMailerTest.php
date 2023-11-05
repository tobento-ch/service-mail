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

namespace Tobento\Service\Mail\Test\Parameter;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter\SendWithMailer;

/**
 * SendWithMailerTest
 */
class SendWithMailerTest extends TestCase
{
    public function testSendWithMailer()
    {
        $param = new SendWithMailer(name: 'foo');
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame('foo', $param->name());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new SendWithMailer(name: 'foo');
        
        $this->assertSame(
            ['name' => 'foo'],
            $param->jsonSerialize()
        );
    }
}