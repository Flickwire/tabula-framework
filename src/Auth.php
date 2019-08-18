<?php
namespace Tabula;

use Tabula\Router\Route;
use Tabula\Auth\Pages\Login;
use Tabula\Auth\Pages\Register;

use Tabula\Auth\Panes\UsersPane;
use Tabula\Auth\Panes\GroupsPane;
use Tabula\Auth\Panes\OptionsPane;
use Tabula\Auth\Models\Users;
use Tabula\Auth\API;

class Auth {
    public $user;
    private $userModel;

    //Can use this to check if user is logged in
    public $isLoggedIn = false;

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;

        $router = $tabula->router;
        $router->register(new Route("/login",$this,"renderLogin"));
        $router->register(new Route("/register",$this,"renderRegister"));

        $router->register(new Route("/api/auth",$this,"authApi"));
        
        $adminPane = $tabula->registry->getAdminPanel();
        $adminPane->registerPane(new UsersPane($this->tabula),'Auth');
        $adminPane->registerPane(new GroupsPane($this->tabula),'Auth');
        $adminPane->registerPane(new OptionsPane($this->tabula),'Auth');

        $this->userModel = new Users($tabula);

        //Check if user is logged in
        if ($this->tabula->session->hasUserId()){
            $this->user = $this->userModel->get($this->tabula->session->getUserId());
            $this->isLoggedIn = true;
        } else {
            $this->user = $this->userModel->guest();
            $this->isLoggedIn = false;
        }
    }

    public function renderLogin(): void{
        (new Login($this->tabula,$this))->render();
    }

    public function renderRegister(): void{
        (new Register($this->tabula,$this))->render();
    }

    public function authApi(): void{
        (new API($this->tabula,$this))->begin();
    }
}
