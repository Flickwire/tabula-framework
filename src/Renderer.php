<?php
namespace Tabula;

use ArrayObject;

/**
 * Experimental twig renderer
 * Might be removed later, idk yet
 * 
 * @author Skye
 */
class Renderer {
    private $tabula;
    private $twig;
    private $loader;

    private $scripts;

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;
        $this->scripts = new ArrayObject();

        $templateDir = $tabula->registry->getTemplateDir();
        $cacheDir = $tabula->registry->getTemplateCacheDir();
        $debug = $tabula->registry->getDebug();

        if ($debug){
            $cacheDir = false;
        }

        //Main template dir for a specific project.
        //Will take precedent over paths added by modules
        $this->loader = new \Twig\Loader\FilesystemLoader($templateDir);
        $this->twig = new \Twig\Environment($this->loader, [
            'cache' => $cacheDir,
            'debug' => $debug,
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

        $this->twig->addGlobal('baseurl','//' . $_SERVER['HTTP_HOST'] .$tabula->registry->getUriBase());
        $this->twig->addGlobal('request',$tabula->registry->getRequest());
        $this->twig->addGlobal('lang','en');
        $this->twig->addGlobal('sitename',$tabula->registry->getSiteName());
    }

    public function registerTemplateDir(string $path): void{
        $this->loader->addPath($path);
    }

    public function registerScriptDir(string $path): void{
        $this->loader->addPath($path,'scripts');
    }

    public function addScript(string $script): void{
        $this->scripts[] = '@scripts/' . $script;
    }

    public function render(string $template, array $vars, bool $toString = false): ?string{
        $template = str_replace('/',DS,$template);

        $vars['___includeScripts'] = $this->scripts;
        
        $output = $this->twig->render($template, $vars);

        if ($toString) return $output;

        echo $output;
        return null;
    }
}