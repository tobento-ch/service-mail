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
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\Template;
use Tobento\Service\View;
use Tobento\Service\Dir;

/**
 * ViewRendererTest
 */
class ViewRendererTest extends TestCase
{
    protected function createViewRenderer(): ViewRenderer
    {
        return new ViewRenderer(
            new View\View(
                new View\PhpRenderer(
                    new Dir\Dirs(
                        new Dir\Dir(realpath(__DIR__.'/views/')),
                    )
                ),
                new View\Data(),
                new View\Assets(__DIR__.'/src/', 'https://example.com/src/')
            )
        );
    }

    public function testImplementsRendererInterface()
    {
        $renderer = $this->createViewRenderer();
        
        $this->assertInstanceof(RendererInterface::class, $renderer);
    }
    
    public function testRenderMissingViewReturnEmptyString()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(new Template('missing', []));
        
        $this->assertSame('', $content);
    }
    
    public function testRenderWelcomeView()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(new Template('welcome', ['name' => 'John']));
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>Welcome</title></head><body>Welcome, John</body></html>',
            $content
        );
    }
    
    public function testRenderWelcomeTextView()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(new Template('welcome-text', ['name' => 'John']));
        
        $this->assertSame(
            'Welcome, John',
            $content
        );
    }
    
    public function testRenderMessageVariableIsAvailableInView()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(
            new Template('message-subject', [])
        );
        
        $this->assertSame('message-subject', $content);
    }
    
    public function testRendersCssInline()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(
            new Template('about', [])
        );
        
        $this->assertSame(
            '<!doctype html><html><head><title>About</title></head><body style="font-size:100%;"><h1 style="font-size:24px;">About</h1></body></html>',
            preg_replace('/([\[(:>\+])\s+/', '$1', $content)
        );
    }
    
    public function testRendersWithoutCssInline()
    {
        $renderer = $this->createViewRenderer();
        
        $content = $renderer->renderTemplate(
            template: new Template('about', []),
            withInlineCssStyles: false,
        );
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>About</title><link href="https://example.com/src/app.css" rel="stylesheet" type="text/css"></head><body><h1>About</h1></body></html>',
            $content
        );
    }    
}