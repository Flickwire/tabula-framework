<?php
namespace Tabula\Auth;

use Tabula\Tabula;
use Tabula\Auth;
use Tabula\Auth\Models\Groups;

class API {
    private $tabula;
    private $request;
    private $auth;
    private $user;

    public function __construct(Tabula $tabula, Auth $auth){
        $this->tabula = $tabula;
        $this->request = $tabula->registry->getRequest();
        $this->auth = $auth;
        $this->user = $auth->user;
    }

    /**
     * Routing entrypoint to the auth mini api
     */
    public function begin(){
        if(!$this->request->has('action')){
            return;
        }
        $action = $this->request->get('action');

        switch ($action) {
            case 'findUsersForGroup':
                return $this->findUsersForGroup();
            default:
                return;
        }
    }

    /**
     * Ajax search for adding user to group
     */
    public function findUsersForGroup(){
        if(!$this->request->has('q')){
            return;
        }
        if(!$this->request->has('gid')){
            return;
        }
        $query = $this->request->get('q');
        $gid = $this->request->get('gid');
        $modelGroups = new Groups($this->tabula);
        $results = $modelGroups->findUsersToAdd($gid,$query);
        $parsedResults = array();
        foreach ($results as $result) {
            $parsedResults['results'][] = [
                'name' => $result['displayname'],
                'email' => $result['email'],
                'id' => $result['id']
            ];
        }
        print(json_encode($parsedResults));
        return;
    }
}