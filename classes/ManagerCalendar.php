<?php

class ManagerCalendar {

    private $db = NULL;
    private $table = 'Booking';

    function __construct($db) {
        $this->db = $db;
    }

    function get($id) {
        if ($this->db !== NULL) {
            $params = array();
            $params['id'] = $id;
            $booking = $this->selec($params);
            return $booking;
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
            $booking = new Booking();
            $booking->set($this->db->getRow());
            return $booking;
        }
        return NULL;
    }

    function delete($id) {
        if ($this->db !== NULL) {
            $params['id'] = $id;
            return $this->db->erase($this->table, $params);
        }
        return false;
    }

    function erase(Booking $param) {
        if ($this->db !== NULL) {
            return $this->delete($param->getId());
        }
    }

    function set(Booking $param, $pkcode = NULL) {
        $parametros = $param->get();
        $parametrosWhere = array();
        if ($pkcode !== NULL) {
            $parametrosWhere["id"] = $pkcode;
        } else {
            $parametrosWhere["id"] = $param->getId();
        }
        return $this->db->update($this->table, $parametros, $parametrosWhere);
    }

    function insert(Booking $param) {
        $parametros = $param->get();
        return $this->db->insert($this->table, $parametros, false);
    }

    function getList($params = NULL, $proyection = '*', $order = '1', $limit = '') {
        if ($this->db != NULL) {
            $this->db->read($this->table, $proyection, $params);
            $list = array();
            while ($param = $this->db->getRow()) {
                $booking = new Booking();
                $booking->set($param);
                $r[] = $booking;
            }
            return $list;
        }
        return NULL;
    }

    function getListJSON($params = NULL, $proyection = '*', $condition = null, $order = '1', $limit = '') {
        if ($this->db != NULL) {
            if ($condition == null) {
                $condition = ' 1=1';
                foreach ($params as $key => $value) {
                    $condition .= " AND $key=:$key";
                }
            }
            $this->db->select($this->table, $proyection, $condition, $params);
            $listJSON = '[';
            while ($param = $this->db->getRow()) {
                $booking = new Booking();
                $booking->set($param);
                $listJSON .= $booking->getJSON() . ',';
            }
            if (strlen($listJSON) > 1) {
                $listJSON = substr($listJSON, 0, -1);
            }
            $listJSON .= ']';
            return $listJSON;
        }
        return NULL;
    }

}
