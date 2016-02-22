<?php

/**
 * Description of Booking
 *
 * @author MARTIN
 */
class Booking {

    private $id, $date, $hour, $room, $user;

    function __construct($id = NULL, $date = NULL, $hour = NULL, $room = NULL, $user = NULL) {
        $this->id = $id;
        $this->date = $date;
        $this->hour = $hour;
        $this->room = $room;
        $this->user = $user;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getDate() {
        return $this->date;
    }

    public function getHour() {
        return $this->hour;
    }

    public function getRoom() {
        return $this->room;
    }

    public function getUser() {
        return $this->user;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setHour($hour) {
        $this->hour = $hour;
    }

    public function setRoom($room) {
        $this->room = $room;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    function set($param) {
        foreach ($this as $key => $value) {
            if (isset($param[$key])) {
                $this->$key = $param[$key];
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
    
    function getJSON() {
        $object = '{';
        foreach ($this as $key => $value) {
            $object .= '"'.$key.'":"'.$value.'",';
        }
        $object = substr($object, 0, -1);
        $object .= '}';
        return $object;
    }

}
