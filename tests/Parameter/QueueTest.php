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
use Tobento\Service\Mail\Parameter\Queue;
use DateInterval;
use DateTimeImmutable;

/**
 * QueueTest
 */
class QueueTest extends TestCase
{
    public function testQueue()
    {
        $param = new Queue();
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame(null, $param->name());
        $this->assertSame(0, $param->delay());
    }
    
    public function testQueueWithParameters()
    {
        $param = new Queue(
            name: 'name',
            delay: 30,
            retry: 5,
            priority: 100,
            encrypt: true,
            renderTemplates: false,
        );
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame('name', $param->name());
        $this->assertSame(30, $param->delay());
        $this->assertSame(5, $param->retry());
        $this->assertSame(100, $param->priority());
        $this->assertTrue($param->encrypt());
        $this->assertFalse($param->renderTemplates());
    }
}