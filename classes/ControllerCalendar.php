<?php

class ControllerCalendar {

    public static function hadle() {
        $session = new Session();
        $user = $session->getUser();
        date_default_timezone_set('Europe/Madrid');
        $method = Request::req('op') . Request::req('set');
        $db = new Database();
        if ($method == '' || $method == null) {
            return self::getCalendar($db);
        } else if (method_exists(__CLASS__, $method)) {
            return self::$method($db);
        } else {
            return self::viewLogin();
        }
    }

    /*
     * -----------------------------------
     *  FUNCIONES DE VISTA
     * -----------------------------------
     */

    public static function viewLogin() {
        return file_get_contents('view/login.html');
    }

    public static function viewSingUp() {
        $session = new Session();
        $user = $session->getUser();
        return str_replace('{alias}','',file_get_contents('view/signup.html'));
    }

    public static function viewCalendar() {
        $page = file_get_contents('view/horario.html');
        $session = new Session();
        $user = $session->getUser();
        if($user->getAdministator() == 1){
            return str_replace('{gestion}', file_get_contents('view/gestion.html'), $page);
        }
        return str_replace('{gestion}', '<div id="ed" class="gest">Editar mi usuario</div><div id="de" class="gest">Darme de baja</div><div id="ex" class="gest">Salir</div>', $page);
    }
    
    public static function viewEditUser() {
        $session = new Session();
        $user = $session->getUser();
        return str_replace('{alias}',$user->getAlias(),file_get_contents('view/signup.html'));
    }
    
    public static function viewDeleteUser() {
        return file_get_contents('view/signup.html');
    }
    
    public static function exitUser() {
        $session = new Session();
        $session->erase('_user');
        $session->destroy();
        return self::viewLogin();
    }

    /*
     * -----------------------------------
     *  FIN FUNCIONES DE VISTA
     * -----------------------------------
     */


    /*
     * -----------------------------------
     *  FUNCIONES DE OPERACION
     * -----------------------------------
     */

    public static function getSignup(Database $db) {
        $alias = Request::req('alias');
        $password = Request::req('password');
        if ($alias != null && $alias != '' && $password != null && $password != '') {
            $manager = new ManagerUser($db);
            $user = $manager->login($params);
        }
    }

    public static function getLogin(Database $db) {
        $params['alias'] = Request::req('alias');
        $params['password'] = sha1(Request::req('password'));
        if ($params['alias'] != null && $params['alias'] != '' && $params['password'] != null && $params['password'] != '') {
            $manager = new ManagerUser($db);
            $user = $manager->login($params);
            if ($user == null) {
                return '{"op":"getlogin","res":"0"}';
            } else if ($user->getActive() == 0) {
                return '{"op":"getlogin","res":"-1"}';
            }
            $session = new Session();
            $session->setUser($user);
            return '{"op":"getlogin","res":"1"}';
        }
    }

    public static function getEvent(Database $db, $id) {
        $manager = new ManagerCalendar($db);
    }

    public static function getCalendar(Database $db) {
        $manager = new ManagerCalendar($db);
        $today = getdate();
        $params['date'] = $today['year'] . '-' . $today['mon'] . '-' . $today['mday'];
        $calendar = $manager->getListJSON($params);
        $db->close();
        return $calendar;
    }

    public static function insertCalendar(Database $db) {
        $date = Request::req('date');
        $hour = Request::req('hour');
        if ($date == null || $date == '' || $hour == null || $hour == '') {
            return '{"action":"insert","res":"-2"}';
        } else if (self::getBookingNumber($db) > 0) {
            return '{"action":"insert","res":"-1","date":"' . $date . '","hour":"' . $hour . '"}';
        } else {
            $object = new Booking(NULL, $date, $hour, 1, 1);
            $manager = new ManagerCalendar($db);
            $res = $manager->insert($object);
            $db->close();
        }
        return '{"action":"insert","res":"' . $res . '","date":"' . $date . '","hour":"' . $hour . '"}';
    }

    public static function updateCalendar(Database $db, Calendar $object) {
        $manager = new ManagerCalendar($db);
        $res = $manager->set($object);
        $db->close();
        return '{"action":"update","res":"' . $res . '"}';
    }

    public static function deleteCalendar(Database $db) {
        $date = Request::req('date');
        $hour = Request::req('hour');
        $params['date'] = $date;
        $params['hour'] = $hour;
        $manager = new ManagerCalendar($db);
        $object = $manager->selec($params);
        $res = $manager->erase($object);
        $db->close();
        return '{"action":"delete","res":"' . $res . '"}';
    }

    public static function getBookingNumber(Database $db) {
        $params['date'] = Request::req('date');
        $params['hour'] = Request::req('hour');
        $params['user'] = 1;
        $manager = new ManagerCalendar($db);
        $res = $manager->getList($params);
        return '{action:bookingNumber,"res":"' . count($res) . '"}';
    }

    public static function getBookingDay(Database $db) {
        $manager = new ManagerCalendar($db);
        $params['date'] = Request::req('date');
        $params['hour'] = Request::req('hour');
        $calendar = $manager->getListJSON($params);
        $db->close();
        return $calendar;
    }

}
