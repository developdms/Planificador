<?php

class Database {

    private $conection, $status, $query;

    function __construct() {
        try {
            $this->conection = new PDO('mysql:host=' . Constan::SERVER . ';' .
                    'dbname=' . Constan::DATABASE, Constan::DBUSER, Constan::DBPASSWORD, array(PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8')
            );
            $this->status = 1;
        } catch (PDOException $e) {
            $this->status = 0;
        }
    }

    function sendQuery($sql, $params = array()) {
        $this->query = $this->conection->prepare($sql);
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                $this->query->bindValue($key, $value);
            }
        }
        return $this->query->execute();
    }

    function read($table, $proyection = '*', $params = array(), $order = '1', $limit = '') {
        $campos = '1 = 1';
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                $campos .= ' and ' . $key . '= :' . $key;
            }
        }
        //$campos = substr($campos, 0, -4);
        $limit = "";
        if ($limit != '') {
            $limit = 'limit =' . $limit;
        }
        $sql = "select $proyection from $table where $campos order by $order $limit";
        return $this->sendQuery($sql, $params);
    }

    function select($table, $proyection = '*', $condition = '1 = 1', $params = array(), $order = '1', $limit = '') {
        $limit = "";
        if ($limit != '') {
            $limit = 'limit =' . $limit;
        }
        $sql = "select $proyection from $table where $condition order by $order $limit";
        return $this->sendQuery($sql, $params);
    }

    function update($table, $insertParams, $conditionParams) {
        
        $sql = "UPDATE $table SET ";
        $params = array();
        foreach ($insertParams as $key => $value) {
            $sql .= $key . " = :$key, ";
            $params[$key] = $value;
        }
        $sql = substr($sql, 0, -2);
        $sql .= ' WHERE ';
        foreach ($conditionParams as $key => $value) {
            $sql .= $key . " = :_$key AND ";
            $params["_$key"] = $value;
        }
        $sql = substr($sql, 0, -5);
        if ($this->sendQuery($sql, $params)) {
            return $this->getCount();
        }
        return $sql;
    }

    function insert($table, $params = array(), $auto = true) {
        // Si en la inserción el id es autonumérico devolvemos el último id.
        // Si en la inserción el id NO es autonumérico devolvemos el número de filas afectadas, que será 1 o 0
        // Si la consulta no se realiza de forma correcta devolvemos false
        $sql = "INSERT INTO $table";
        $columns = ' (';
        $values = '(';
        foreach ($params as $key => $value) {
            $columns .= $key . ', ';
            $values .= ':' . $key . ', ';
        }
        $columns = substr($columns, 0, -2);
        $values = substr($values, 0, -2);

        $sql .= "$columns) VALUES $values)";

        if ($this->sendQuery($sql, $params)) {
            if ($auto) {
                return $this->getId();
            } else {
                return $this->getCount();
            }
        }
        return false;
    }

    function delete($table, $condition, $params = array()) {
        $sql = "DELETE FROM $table WHERE $condition";
        if ($this->sendQuery($sql, $params)) {
            return $this->getCount();
        }
        return false;
    }

    //Método pendiente de revisar y completar la sentencia (Mira en update).
    function erase($table, $params = array()) {
        $sql = "DELETE FROM $table WHERE 1=1";
        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
        }
        if ($this->sendQuery($sql, $params)) {
            return $this->getCount();
        }
        return false;
    }

    function count($table, $params = array()) {
        $sql = "SELECT COUNT(*) FROM $table";
        if (count($params) > 0) {
            $sql .= " where 1=1 ";
            foreach ($params as $key => $value) {
                $sql .= "and $key=:$key ";
            }
        }
        $this->sendQuery($sql, $params);
        return $this->getRow()[0];
    }

    function getCount() {
        if ($this->query !== NULL) {
            return $this->query->rowCount();
        }
        return 0;
    }

    function getId() {
        if ($this->conection !== NULL) {
            return $this->conection->lastInsertId();
        }
        return -1;
    }

    function getRow() {
        $r = $this->query->fetch();
        if ($r === NULL) {
            $this->query->closeCursor();
        }
        return $r;
    }

    function sum($table, $params = array()) {
        $sql = "SELECT SUM(amount*price) as resultado FROM $table";
        if (count($params) > 0) {
            $sql .= " where 1=1 ";
            foreach ($params as $key => $value) {
                $sql .= "and $key=:$key ";
            }
        }
        $this->sendQuery($sql, $params);

        return $this->getRow()[0];
    }

    function close() {
        $this->conection = NULL;
    }

    function getConectionStatus() {
        return $this->conection->errorInfo();
    }

    function getQueryError() {
        return $this->query->errorInfo();
    }

    public function status() {
        return $this->status;
    }

}
