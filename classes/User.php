<?php

class User {
    
  private $id, $alias, $password, $active, $administator;

    //(si no es administrador sólo puede crear usuarios normales no personal) 
    function __construct($id = NULL, $alias = NULL, $password = NULL, $active = 0, $administator = 0) {
        $this->id = $id;
        $this->password = $password;
        $this->alias = $alias;
        $this->active = $active;
        $this->administrator = $administator;
    }

    public function getId() {
        return $this->id;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getActive() {
        return $this->active;
    }

    public function getAdministator() {
        return $this->administator;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function setAdministator($administator) {
        $this->administator = $administator;
    }

    function set($param) {
        foreach ($this as $key => $value) {
            if (isset($param[$key])) {
                if($key == 'password'){
                $this->$key = sha1($param[$key]);
                }else{
                    $this->$key = $param[$key];
                }
            }
        }
    }

    function get() {
        $param = array();
        foreach ($this as $key => $value) {
            $param[$key] = $value;
        }
        return $param;
    }   
    
}
