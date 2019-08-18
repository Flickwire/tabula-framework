<?php
namespace Tabula\Auth\Panes;

use Tabula\Auth\Models\Users;
use Tabula\Renderer\Page;
use Tabula\Admin\AdminPane;

class UsersPane extends AdminPane {
    private $request;
    private $userModel;

    public function render(): string{
        $this->request = $this->tabula->registry->getRequest();
        $this->userModel = new Users($this->tabula);
        $action = $this->tabula->registry->getRequest()->get('action');
        switch($action){
            case 'create':
                return $this->createUser();
            case 'edit':
                return $this->editUser();
            case 'delete':
                return $this->deleteUser();
            default:
                return $this->listUsers();
        }
    }

    private function listUsers(): string{
        $page = new Page($this->tabula,"admin/panes/auth/listUsers.html");
        $this->tabula->renderer->addScript('auth/listUsers.js');

        $users = $this->userModel->getUsers();
        $page->set('users',$users);
        
        return $page->render(true);
    }

    private function createUser(): string{
        $page = new Page($this->tabula,"admin/panes/auth/newUser.html");
        $this->tabula->renderer->addScript('auth/newUser.js');

        if ($this->request->getMethod() === 'POST'){
            $name = $this->request->get('name',true);
            $email = $this->request->get('email',true);
            $password = $this->request->get('password',true);
            $password2 = $this->request->get('password2',true);

            $passed = $this->userModel->validateUser($name,$email,$password,$password2);

            if($passed){
                $this->userModel->newUser($name,$email,$password);
                $this->tabula->redirect($this->request->getSelf([],['action'],true));
            }

            $page->set('email', $email);
            $page->set('name', $name);
        }

        return $page->render(true);
    }

    private function editUser(): string{
        $page = new Page($this->tabula,"admin/panes/auth/newUser.html");
        $this->tabula->renderer->addScript('auth/editUser.js');

        $page->set('edit',true);

        $id = $this->request->get('id');
        $user = $this->userModel->loadUser($id);

        if(!$user){
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }

        $page->set('email', $user['email']);
        $page->set('name', $user['displayname']);

        if ($this->request->getMethod() === 'POST'){
            $name = $this->request->get('name',true);
            $email = $this->request->get('email',true);
            $password = $this->request->get('password',true);
            $password2 = $this->request->get('password2',true);

            $passed = $this->userModel->validateUser($name,$email,$password,$password2,$id);

            if($passed){
                $this->userModel->updateUser($id,$name,$email,$password);
                $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
            }

            $page->set('email', $email);
            $page->set('name', $name);
        }

        return $page->render(true);
    }

    private function deleteUser(): string{
        if (!$this->request->has('id')){
            $this->tabula->session->addError('No user found to delete');
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }
        $id = $this->request->get('id');
        if ($this->tabula->session->getUserId() === $id){
            $this->tabula->session->addError('Cannot delete your own user');
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }
        $this->userModel->delete($id);
        $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        return '';
    }

    /**
     * Return the name of your admin pane,
     * for the menu
     */
    public function getName(): string{
        return "Users";
    }

    /**
     * Return a url-friendly slug for your pane
     */
    public function getSlug(): string{
        return "auth-users";
    }

    /**
     * Return an icon for the menu if you want to
     */
    public function getIcon(): ?string{
        return "user";
    }
}