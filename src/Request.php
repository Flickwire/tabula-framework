<?php
namespace Tabula;

class Request {
    private $tabula;

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;
    }

    public function has(string $key){
        return \array_key_exists($key, $_POST) || \array_key_exists($key, $_GET);
    }

    public function get(string $key, bool $post = false){
        if (\array_key_exists($key,$_POST)){
            return $_POST[$key];
        }
        if (!$post){
            if (\array_key_exists($key,$_GET)){
                return $_GET[$key];
            }
        }
        return null;
    }

    /**
     * Get the current page URI,
     * relative to the base url of Tabula
     * 
     * @author Skye
     */
    public function getUri(){
        $base = $this->tabula->registry->getUriBase();
        $requestUri = $_SERVER['REQUEST_URI'];
        if(\strncmp($base,$requestUri,strlen($base)) === 0){
            return \str_replace(DS,'/',\substr($requestUri,\strlen($base))); //Find base path of project
        }
        throw new \Exception('How did you get here? I have no idea where to route you. Go away.');
    }

    /**
     * Get a link with specific query parameters added or removed
     * 
     * @author Skye
     */
    public function getSelf(array $insert = [], array $remove = [], bool $absolute = false): string{
        $query = $_GET;
        //remove things
        $query = \array_diff_key($query, \array_flip($remove));
        //add things
        $query = \array_merge($query, $insert);
        $host = $absolute ? ('//' . $_SERVER['HTTP_HOST']) : '';
        return $host . \strtok($_SERVER["REQUEST_URI"],'?') . '?' . \http_build_query($query);
    }

    /**
     * Check the request method
     * 
     * @author Skye
     */
    public function getMethod(): string{
        return $_SERVER['REQUEST_METHOD'];
    }
}