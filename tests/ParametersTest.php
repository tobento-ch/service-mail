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
use Tobento\Service\Mail\Parameters;
use Tobento\Service\Mail\ParametersInterface;
use Tobento\Service\Mail\ParameterInterface;
use Tobento\Service\Mail\Parameter;

/**
 * ParametersTest
 */
class ParametersTest extends TestCase
{
    public function testCreateParameters()
    {
        $parameters = new Parameters(
            new Parameter\TextHeader(name: 'foo', value: 'foo value'),
        );
        
        $this->assertInstanceof(ParametersInterface::class, $parameters);
    }
    
    public function testAddMethod()
    {
        $foo = new Parameter\TextHeader(name: 'foo', value: 'foo value');
        $bar = new Parameter\TextHeader(name: 'bar', value: 'bar value');
        
        $parameters = (new Parameters())->add($foo)->add($bar);
        
        $this->assertTrue($foo === ($parameters->all()[0] ?? null));
        $this->assertTrue($bar === ($parameters->all()[1] ?? null));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testFilterMethod()
    {
        $parameters = new Parameters(
            new Parameter\TextHeader(name: 'foo', value: 'foo value'),
            new Parameter\Tags(['tag']),
        );
        
        $parametersNew = $parameters->filter(
            fn(ParameterInterface $p): bool => $p instanceof Parameter\Tags
        );
        
        $this->assertFalse($parameters === $parametersNew);
        $this->assertSame(1, count($parametersNew->all()));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testFirstMethod()
    {
        $parameters = new Parameters(
            new Parameter\TextHeader(name: 'foo', value: 'foo value'),
            new Parameter\Tags(['tag']),
        );
        
        $this->assertInstanceof(ParameterInterface::class, $parameters->first());
        
        $parameters = new Parameters();
        
        $this->assertSame(null, $parameters->first());
    }    
    
    public function testAllMethod()
    {
        $parameters = new Parameters();
        
        $this->assertSame(0, count($parameters->all()));
        
        $parameters = new Parameters(
            new Parameter\TextHeader(name: 'foo', value: 'foo value'),
        );
        
        $this->assertSame(1, count($parameters->all()));
    }
    
    public function testIteration()
    {
        $parameters = new Parameters(
            new Parameter\TextHeader(name: 'foo', value: 'foo value'),
            new Parameter\TextHeader(name: 'bar', value: 'bar value'),
        );
        
        foreach($parameters as $parameter) {
            $this->assertInstanceof(ParameterInterface::class, $parameter);
        }
    }
    
    public function testJsonSerializeMethod()
    {
        $params = new Parameters();
        
        $this->assertSame([], $params->jsonSerialize());
        
        $params->add(new Parameter\Tags(['foo', 'bar']));
        $params->add(new Parameter\File('/path/foo.pdf'));
        $params->add(new Parameter\File('/path/bar.pdf'));
        
        $this->assertSame(
            [
                'Tobento\Service\Mail\Parameter\Tags:0' => ['tags' => ['foo', 'bar']],
                'Tobento\Service\Mail\Parameter\File:1' => [
                    'file' => '/path/foo.pdf',
                    'filename' => 'foo.pdf',
                    'inline' => false,
                ],
                'Tobento\Service\Mail\Parameter\File:2' => [
                    'file' => '/path/bar.pdf',
                    'filename' => 'bar.pdf',
                    'inline' => false,
                ],
            ],
            $params->jsonSerialize()
        );
    }
    
    public function testToStringMethod()
    {
        $params = new Parameters();
        
        $this->assertSame('[]', $params->__toString());
        
        $params->add(new Parameter\Tags(['foo', 'bar']));

        $this->assertSame(
            json_encode(['Tobento\Service\Mail\Parameter\Tags:0' => ['tags' => ['foo', 'bar']]]),
            $params->__toString()
        );
    }
}