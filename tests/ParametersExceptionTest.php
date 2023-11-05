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
use Tobento\Service\Mail\ParametersException;
use Exception;

/**
 * ParametersExceptionTest
 */
class ParametersExceptionTest extends TestCase
{
    public function testParametersException()
    {
        $e = new ParametersException('content');
        
        $this->assertInstanceof(Exception::class, $e);
    }
}