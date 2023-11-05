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
use Tobento\Service\Mail\MessageException;
use Exception;

/**
 * MessageExceptionTest
 */
class MessageExceptionTest extends TestCase
{
    public function testMessageException()
    {
        $e = new MessageException('content');
        
        $this->assertInstanceof(Exception::class, $e);
    }
}