<?php
namespace Tabula;

use Tabula\Router\Route;
use Tabula\Database\Adapter\AbstractAdapter;
use Tabula\Auth\Pages\Login;
use Tabula\Auth\Pages\Register;

use Tabula\Auth\Panes\UsersPane;
use Tabula\Auth\Panes\OptionsPane;

class Auth {
    private $user;

    //Can use this to check if user is logged in
    public $isLoggedIn = false;

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;

        $router = $tabula->router;
        $router->register(new Route("/login",$this,"renderLogin"));
        $router->register(new Route("/register",$this,"renderRegister"));
        
        $adminPane = $tabula->registry->getAdminPanel();
        $adminPane->registerPane(new UsersPane($this->tabula),'Auth');
        $adminPane->registerPane(new OptionsPane($this->tabula),'Auth');

        //Check if user is logged in
        if ($this->tabula->session->hasUserId()){
            $this->user = new User(['displayname'=>'Name','email'=>'yeet'],[],[]);
            $this->isLoggedIn = true;
        } else {
            $this->user = User::guest();
            $this->isLoggedIn = false;
        }
    }

    public function renderLogin(): void{
        (new Login($this->tabula,$this))->render();
    }

    public function renderRegister(): void{
        (new Register($this->tabula,$this))->render();
    }
}
