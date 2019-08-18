<?php
namespace Tabula\Auth\Models;

use Tabula\Auth\User;

class Groups{
    private $tabula;
    private $db;
    private $table = 'tb_usergroups';
    private $tableCoupleUsers = 'tb_users_usergroups';
    private $tableUsers = 'tb_users';

    public function __construct($tabula){
        $this->db = $tabula->db;
        $this->tabula = $tabula;
    }

    public function getGroups(int $offset = 0, int $limit = 0){
        $query = "SELECT id, displayname FROM {$this->table}";
        if ($limit !== 0){
            $query .= " LIMIT ?i";
            if ($offset !== 0){
                $query .= " OFFSET ?i";
                return $this->db->query($query,$limit,$offset);
            }
            return $this->db->query($query,$limit);
        }
        return $this->db->query($query);
    }
/*
    public function get(int $id){
        $query = "SELECT id, displayname FROM {$this->table} WHERE id = ?i";
        $result = $this->db->query($query,$id)->fetch();
        if (!$result){
            return null;
        }
        return new User($result['id'],$result['displayname'],$result['email']);
    }
*/
    public function newGroup(string $name): string{

        $query = "INSERT INTO {$this->table}(displayname) VALUES (?s)";

        $this->db->query($query,$name);
        return $this->db->lastInsertId();
    }

    public function delete($id){
        $query = "DELETE FROM {$this->table} WHERE id = ?i";
        $this->db->query($query,$id);
    }

    public function nameUsed(string $name, $id = null){
        $group = $this->tabula->db->query("SELECT id FROM {$this->table} WHERE displayname = ?s",$name)->fetch();
        if (!$group) {
            return false;
        }
        if(!is_null($id) && $id = $group['id']){//Existing group can keep using their current name
            return false;
        }
        return true;
    }

    
    public function validateGroup($name, $id = null){
        $session = $this->tabula->session;

        //name too short
        if(\is_null($name) || \strlen($name) < 1){
            $session->addError('Please enter a group name');
            return false;
        }

        if($this->nameUsed($name,$id)){
            $session->addError('A group with the provided name already exists');
            return false;
        }

        return true;
    }

    public function loadGroup($id){
        $query = "SELECT id, displayname FROM {$this->table} WHERE id = ?s";
        return $this->db->query($query,$id)->fetch();
    }

    /**
     * Get all users for a particular group ID
     */
    public function getUsersByGroup($id){
        $query = "SELECT B.id, B.displayname, B.email FROM {$this->tableCoupleUsers} AS A LEFT JOIN {$this->tableUsers} AS B ON B.id = A.user WHERE A.usergroup = ?s";
        return $this->db->query($query,$id);
    }

    public function updateGroup($id,$name){
        $query = "UPDATE {$this->table} SET displayname = ?s WHERE id = ?i";
        $this->db->query($query,$name,$id);
    }

    public function findUsersToAdd($id,$qString){
        $qPercent = '%'.$qString.'%';
        $query = "SELECT * FROM
                (SELECT A.id, A.displayname, A.email from {$this->tableUsers} AS A
                LEFT JOIN 
                    (SELECT * FROM {$this->tableCoupleUsers}
                    WHERE usergroup = ?i) AS B
                ON A.id = B.user
                WHERE B.usergroup IS NULL
                AND A.email <> 'GUEST') AS C
            WHERE MATCH(C.displayname, C.email) AGAINST(?s)
            OR C.displayname LIKE ?s OR C.email LIKE ?s";
        return $this->db->query($query,$id,$qString,$qPercent,$qPercent);
    }

    public function addUserToGroup($uid,$gid){
        $query = "SELECT COUNT(1) FROM {$this->tableCoupleUsers}
            WHERE usergroup = ?i
            AND user = ?i";
        $exists = $this->db->query($query,$gid,$uid)->fetch();
        if($exists["COUNT(1)"]){
            $this->tabula->session->addError('Cannot add user to group; they are already in it');
            return;
        }
        $query = "INSERT INTO {$this->tableCoupleUsers}(user, usergroup)
            VALUES (?i, ?i);";
        $this->db->query($query,$uid,$gid);
    }

    public function removeUserFromGroup($uid,$gid){
        $query = "SELECT COUNT(1) FROM {$this->tableCoupleUsers}
            WHERE usergroup = ?i
            AND user = ?i";
        $exists = $this->db->query($query,$gid,$uid)->fetch();
        if(!$exists["COUNT(1)"]){
            $this->tabula->session->addError('Cannot remove user from group they do not belong to');
            return;
        }
        $query = "DELETE FROM {$this->tableCoupleUsers}
            WHERE user = ?i
            AND usergroup = ?i;";
        $this->db->query($query,$uid,$gid);
    }
}