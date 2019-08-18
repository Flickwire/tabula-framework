<?php
namespace Tabula\Auth\Panes;

use Tabula\Auth\Models\Users;
use Tabula\Auth\Models\Groups;
use Tabula\Renderer\Page;
use Tabula\Admin\AdminPane;

class GroupsPane extends AdminPane {
    private $request;
    private $groupModel;
    private $userModel;

    public function render(): string{
        $this->request = $this->tabula->registry->getRequest();
        $this->userModel = new Users($this->tabula);
        $this->groupModel = new Groups($this->tabula);
        $action = $this->tabula->registry->getRequest()->get('action');
        switch($action){
            case 'create':
                return $this->createGroup();
            case 'edit':
                return $this->editGroup();
            case 'listUsers':
                return $this->listGroupUsers();
            case 'addUser':
                return $this->addUserToGroup();
            case 'removeUser':
                return $this->removeUserFromGroup();
            case 'delete':
                return $this->deleteGroup();
            default:
                return $this->listGroups();
        }
    }

    private function listGroups(): string{
        $page = new Page($this->tabula,"admin/panes/auth/listGroups.html");
        $this->tabula->renderer->addScript('auth/listGroups.js');

        $groups = $this->groupModel->getGroups();
        $page->set('groups',$groups);
        
        return $page->render(true);
    }

    private function createGroup(): string{
        $page = new Page($this->tabula,"admin/panes/auth/newGroup.html");
        $this->tabula->renderer->addScript('auth/newGroup.js');

        if ($this->request->getMethod() === 'POST'){
            $name = $this->request->get('name',true);

            $passed = $this->groupModel->validateGroup($name);

            if($passed){
                $this->groupModel->newGroup($name);
                $this->tabula->redirect($this->request->getSelf([],['action'],true));
            }

            $page->set('name', $name);
        }

        return $page->render(true);
    }

    private function editGroup(): string{
        $page = new Page($this->tabula,"admin/panes/auth/newGroup.html");
        $this->tabula->renderer->addScript('auth/newGroup.js');

        $page->set('edit',true);

        $id = $this->request->get('id');
        $group = $this->groupModel->loadGroup($id);

        if(!$group){
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }
        $page->set('name', $group['displayname']);

        if ($this->request->getMethod() === 'POST'){
            $name = $this->request->get('name',true);

            $passed = $this->groupModel->validateGroup($name);

            if($passed){
                $this->groupModel->updateGroup($id,$name);
                $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
            }

            $page->set('name', $name);
        }

        return $page->render(true);
    }

    private function deleteGroup(): string{
        if (!$this->request->has('id')){
            $this->tabula->session->addError('No group found to delete');
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }
        $id = $this->request->get('id');
        $this->groupModel->delete($id);
        $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        return '';
    }

    /**
     * List all users in group, and allow adding them
     */
    public function listGroupUsers(): string{
        if (!$this->request->has('id')){
            $this->tabula->session->addError('No group found');
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }
        $id = $this->request->get('id');
        $group = $this->groupModel->loadGroup($id);
        if(!$group){
            $this->tabula->session->addError('No group found');
            $this->tabula->redirect($this->request->getSelf([],['action','id'],true));
        }

        $page = new Page($this->tabula,"admin/panes/auth/groupUsers.html");
        $this->tabula->renderer->addScript('auth/groupUsers.js');
        $this->tabula->renderer->injectVar('group',$group);

        $users = $this->groupModel->getUsersByGroup($id);

        $page->set('group',$group);
        $page->set('users',$users);

        return $page->render(true);
    }

    /**
     * Add user to a specific group
     */
    public function addUserToGroup(): string{
        if (!$this->request->has('gid')){
            $this->tabula->session->addError('No group found');
            $this->tabula->redirect($this->request->getSelf([],['action','id','uid'],true));
        }
        $gid = $this->request->get('gid');
        if (!$this->request->has('uid')){
            $this->tabula->session->addError('No user found to add to group');
            $this->tabula->redirect($this->request->getSelf(['action'=>'listUsers','id'=>$gid],['gid'],true));
        }
        $uid = $this->request->get('uid');

        $this->groupModel->addUserToGroup($uid,$gid);
        $this->tabula->redirect($this->request->getSelf(['action'=>'listUsers','id'=>$gid],['gid','uid'],true));

        return '';
    }

    /**
     * Remove a user from a group
     */
    public function removeUserFromGroup(): string{
        if (!$this->request->has('gid')){
            $this->tabula->session->addError('No group found');
            $this->tabula->redirect($this->request->getSelf([],['action','id','uid'],true));
        }
        $gid = $this->request->get('gid');
        if (!$this->request->has('uid')){
            $this->tabula->session->addError('No user found to remove from group');
            $this->tabula->redirect($this->request->getSelf(['action'=>'listUsers','id'=>$gid],['gid'],true));
        }
        $uid = $this->request->get('uid');

        $this->groupModel->removeUserFromGroup($uid,$gid);
        $this->tabula->redirect($this->request->getSelf(['action'=>'listUsers','id'=>$gid],['gid','uid'],true));

        return '';
    }

    /**
     * Return the name of your admin pane,
     * for the menu
     */
    public function getName(): string{
        return "Groups";
    }

    /**
     * Return a url-friendly slug for your pane
     */
    public function getSlug(): string{
        return "auth-groups";
    }

    /**
     * Return an icon for the menu if you want to
     */
    public function getIcon(): ?string{
        return "users";
    }
}