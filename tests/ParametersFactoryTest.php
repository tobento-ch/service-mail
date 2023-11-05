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
use Tobento\Service\Mail\ParametersFactory;
use Tobento\Service\Mail\ParametersFactoryInterface;
use Tobento\Service\Mail\ParametersInterface;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\ParametersException;

class ParametersFactoryTest extends TestCase
{
    public function testThatImplementsParametersFactoryInterface()
    {
        $this->assertInstanceof(ParametersFactoryInterface::class, new ParametersFactory());
    }
    
    public function testCreateFromArrayMethod()
    {
        $params = (new ParametersFactory())->createFromArray([
            Parameter\TextHeader::class => ['name' => 'X-Custom-Header', 'value' => 'value'],
            Parameter\File::class => ['file' => '/path/document.pdf'],
        ]);
        
        $header = $params->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\TextHeader
        )->first();
        
        $this->assertSame('X-Custom-Header', $header?->name());        
        $this->assertSame(2, count($params->all()));
    }
    
    public function testCreateFromArrayMethodWithNonClassName()
    {
        $params = (new ParametersFactory())->createFromArray([
            'Tobento\Service\Mail\Parameter\File:1' => ['file' => '/path/document1.pdf'],
            'Tobento\Service\Mail\Parameter\File:0' => ['file' => '/path/document1.pdf'],
        ]);
        
        $this->assertSame(2, count($params->all()));
    }
    
    public function testCreateFromArrayMethodThrowsParametersExceptionOnFailure()
    {
        $this->expectException(ParametersException::class);
        
        $params = (new ParametersFactory())->createFromArray([
            Parameter\File::class => ['invalid' => 'foo'],
        ]);
    }
    
    public function testCreateFromJsonStringMethod()
    {
        $params = (new ParametersFactory())->createFromJsonString(json_encode([
            Parameter\TextHeader::class => ['name' => 'X-Custom-Header', 'value' => 'value'],
            Parameter\File::class => ['file' => '/path/document.pdf'],
        ]));
        
        $header = $params->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\TextHeader
        )->first();
        
        $this->assertSame('X-Custom-Header', $header?->name());        
        $this->assertSame(2, count($params->all()));
    }
    
    public function testCreateFromStringMethodWithNonClassName()
    {
        $params = (new ParametersFactory())->createFromJsonString(json_encode([
            'Tobento\Service\Mail\Parameter\File:1' => ['file' => '/path/document1.pdf'],
            'Tobento\Service\Mail\Parameter\File:0' => ['file' => '/path/document1.pdf'],
        ]));
        
        $this->assertSame(2, count($params->all()));
    }
    
    public function testCreateFromStringMethodThrowsParametersExceptionOnFailure()
    {
        $this->expectException(ParametersException::class);
        
        $params = (new ParametersFactory())->createFromJsonString(json_encode([
            Parameter\File::class => ['invalid' => 'foo'],
        ]));
    }
}