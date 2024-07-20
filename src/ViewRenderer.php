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

namespace Tobento\Service\Mail;

use Tobento\Service\View\ViewInterface;
use Tobento\Service\Filesystem\File;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
    
/**
 * ViewRenderer
 */
class ViewRenderer implements RendererInterface
{
    /**
     * Create a new ViewRenderer.
     *
     * @param ViewInterface $view
     */
    public function __construct(
        protected ViewInterface $view,
    ) {}
    
    /**
     * Returns the evaluated content of the template rendered.
     *
     * @param TemplateInterface $template
     * @param bool $withInlineCssStyles
     * @return string
     */
    public function renderTemplate(TemplateInterface $template, bool $withInlineCssStyles = true): string
    {
        $data = $template->data();
        $data['withInlineCssStyles'] = $withInlineCssStyles;
        
        // check if message exists, otherwise create it.
        $message = $data['message'] ?? null;
        
        if (! $message instanceof TemplateMessageInterface) {
            $data['message'] = new TemplateMessage(subject: $template->name());
        }
        
        $html = $this->view->render($template->name(), $data);
        
        if (!$withInlineCssStyles || empty($this->view->assets()->all())) {
            $this->view->assets()->clear();
            return $html;
        }
        
        // Collect all css content from view assets:
        $css = '';
        
        foreach($this->view->assets()->all() as $asset) {
            $file = new File($asset->getDir().$asset->getFile());
            
            if (!$file->isFile()) {
                continue;
            }
            
            if ($file->getExtension() !== 'css') {
                continue;
            }
            
            $css .= $file->getContent();
        }
        
        $this->view->assets()->clear();
        
        if ($css === '') {
            return $html;
        }
        
        if (str_contains($css, ':root')) {
            $css = $this->replaceRootVars($css);
        }
        
        // Convert css to inline styles:
        return (new CssToInlineStyles())->convert(
            $html,
            $css
        );
    }
    
    /**
     * Returns the css with the replaced root vars.
     *
     * @param string $css
     * @return string
     */
    protected function replaceRootVars(string $css): string
    {
        $variables = $this->collectVariables($css);
        /*$variables = [
            '--font-primary' => 'Georgia, "Times New Roman", Times, serif',
        ];*/
        
        // replace variables:
        $callback = function (array $match) use ($variables): string {
            if (array_key_exists($match[1], $variables)) {
                return $variables[$match[1]];
            }
            
            return $match[0];
        };
		
        $css = preg_replace_callback('/var\((--[a-zA-Z0-9-_]+)(?:\)|,\s*(.*)\))/', $callback, $css);
        
        // replaces :root vars:
        $css = preg_replace('/--[a-zA-Z0-9-_]+[:](.*)\;/', '', $css);
        
        return $css;
    }
    
    /**
     * Returns the collected css variables.
     *
     * @param string $css
     * @return array
     */
    protected function collectVariables(string $css): array
    {
        preg_match_all('/--[a-zA-Z0-9-_]+[:](.*)\;/', $css, $matches);
        
        $variables = [];
        
        foreach($matches[0] ?? [] as $match) {
            $data = explode(':', $match);
            $variables[$data[0]] = rtrim($data[1], ';');
        }

        return $variables;
    }
}