<?php
namespace Tabula\Renderer;

/**
 * Use this to render a page from a template
 * 
 * @author Skye
 */
class Page {
    private $tabula;
    private $template;
    private $vars = [];

    public function __construct(Tabula $tabula, string $template){
        $this->template = $template;
    }

    public function has(string $key){
        return \array_key_exists($key, $this->vars);
    }

    public function get(string $key): mixed{
        if (\array_key_exists($key,$this->vars)){
            return $this->vars[$key];
        }
        return null;
    }

    public function set(string $key, mixed $value): void{
        $this->vars[$key] = $value;
    }

    public function render(){
        $this->tabula->renderer->render($this->template, $this->vars);
    }

}