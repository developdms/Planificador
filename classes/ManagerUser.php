<?php

class ManagerUser {
    
    private $db = NULL;
    private $table = 'User';

    function __construct($db) {
        $this->db = $db;
    }

    function login($params) {
        if ($this->db !== NULL) {
            $condition = '1=1';
            foreach ($params as $key => $value) {
                $condition .= " AND $key=:$key";
            }
            $this->db->select($this->table, '*', $condition, $params);
            $user = new User();
            $user->set($this->db->getRow());
            if ($user->getId() != NULL) {
                return $user;
            }
        }
        return NULL;
    }

    function get($id) {
        if ($this->db !== NULL) {
            $params['id'] = $id;
            $user = $this->selec($params);
            return $user;
        }
        return NULL;
    }

    function selec($params = NULL, $condition = '1=1') {
        if ($this->db !== NULL) {
            if ($params !== NULL) {
                foreach ($params as $key => $value) {
                    $condition .= " AND $key=:$key";
                }
            }
            $this->db->select($this->table, '*', $condition, $params);
            $user = new User();
            $user->set($this->db->getRow());
            return $user;
        }
        return NULL;
    }

    function exists($alias) {
        $params['alias'] = $alias;
        $condition = " alias =:alias";
        $this->db->select($this->table, '*', $condition, $params);
        $user = new User();
        $user->set($this->db->getRow());
        return $user->getId() != NULL;
    }

    function unlock($params = NULL, $condition = ' 1 = 1') {
        if ($this->db !== NULL) {
            if ($params !== NULL) {
                foreach ($params as $key => $value) {
                    $condition .= " AND sha($key)=:$key";
                }
            }
            $this->db->select($this->table, '*', $condition, $params);
            $user = new User();
            $user->set($this->db->getRow());
            return $user;
        }
        return NULL;
    }

    function delete($Code) {
        if ($this->db !== NULL) {
            $params['Code'] = $Code;
            return $this->db->erase($this->table, $params);
        }
        return false;
    }

    function erase(User $param) {
        if ($this->db !== NULL) {
            return $this->delete($param->getEmail());
        }
    }

    function set(User $param, $pkcode = NULL) {
        $parametros = $param->get();
        $parametrosWhere = array();
        if ($pkcode !== NULL) {
            $parametrosWhere["id"] = $pkcode;
        } else {
            $parametrosWhere["id"] = $param->getId();
        }
        return $this->db->update($this->table, $parametros, $parametrosWhere);
    }

    function insert(User $param) {
        $parametros = $param->get();
        return $this->db->insert($this->table, $parametros, false);
    }

    function getList($params = NULL, $proyection = '*', $order = '1', $limit = '') {
        if ($this->db != NULL) {
            $this->db->read($this->table, $proyection, $params);
            $r = array();
            while ($param = $this->db->getRow()) {
                $user = new User();
                $user->set($param);
                $r[] = $user;
            }
            return $r;
        }
        return NULL;
    }

    function getQueryError() {
        return $this->db->getQueryError();
    }
    
}
