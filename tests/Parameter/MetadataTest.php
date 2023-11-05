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
use Tobento\Service\Mail\Parameter\Metadata;

/**
 * MetadataTest
 */
class MetadataTest extends TestCase
{
    public function testMetadata()
    {
        $param = new Metadata(metadata: ['name' => 'value']);
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame(['name' => 'value'], $param->metadata());
    }
    
    public function testJsonSerializeMethod()
    {
        $param = new Metadata(metadata: ['name' => 'value']);
        
        $this->assertSame(
            ['metadata' => ['name' => 'value']],
            $param->jsonSerialize()
        );
    }
}