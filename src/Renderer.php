<?php
namespace Tabula;

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

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;

        $templateDir = $tabula->registry->getTemplateDir();
        $cacheDir = $tabula->registry->getTemplateCacheDir();
        $debug = $tabula->registry->getDebug();

        //Main template dir for a specific project.
        //Will take precedent over paths added by modules
        $this->loader = new \Twig\Loader\FilesystemLoader($templateDir);
        $this->twig = new \Twig\Environment($this->loader, [
            'cache' => $cacheDir,
            'debug' => $debug,
        ]);
    }

    public function registerTemplateDir(string $path): void{
        $this->loader->addPath($path);
    }

    public function render(string $template, array $vars): void{
        echo $this->twig->render($template, $vars);
    }
}