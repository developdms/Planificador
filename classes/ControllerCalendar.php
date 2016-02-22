<?php

class ControllerCalendar {

    public static function hadle() {
        $session = new Session();
        $user = $session->getUser();
        date_default_timezone_set('Europe/Madrid');
        $method = Request::req('op') . Request::req('set');
        $db = new Database();
        if ($method == '' || $method == null && $user != null) {
            return self::getCalendar($db);
        } else if (method_exists(__CLASS__, $method)) {
            return self::$method($db);
        } else if (!method_exists(__CLASS__, $method) && $user != null) {
            return self::viewCalendar();
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

//    public static function viewSingUp() {
//        return file_get_contents('view/login.html');
//    }

    public static function viewCalendar() {
        $page = file_get_contents('view/horario.html');
        $session = new Session();
        $user = $session->getUser();
        if ($user->getAdministator() == 1) {
            return str_replace('{gestion}', file_get_contents('view/gestion.html'), $page);
        }
        return str_replace('{gestion}', '<div id="ed" class="gest">Editar mi usuario</div><div id="de" class="gest">Darme de baja</div><div id="ex" class="gest">Salir</div>', $page);
    }

    public static function viewNewUser() {
        $session = new Session();
        $user = $session->getUser();
        return str_replace('{alias}', '', file_get_contents('view/signup.html'));
    }

    public static function viewEditUser() {
        $session = new Session();
        $user = $session->getUser();
        return str_replace('{alias}', $user->getAlias(), file_get_contents('view/signup.html'));
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

    public static function isLoged() {
        $session = new Session();
        if ($session->getUser()) {
            return '{"res":"1"}';
        }
        return '{"res":"0"}';
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
        $params['alias'] = Request::req('alias');
        $params['password'] = sha1(Request::req('password'));
        $rpass = sha1(Request::req('password'));
        if ($params['password'] != $rpass) {
            return '{"res":"-3"}';
        }
        if ($params['alias'] != null && $params['alias'] != '' && $params['password'] != null && $params['password'] != '') {
            $manager = new ManagerUser($db);
            if ($manager->exists($params['alias']) == false) {
                $res = $manager->insert(new User(null, $params['alias'], $params['password'], 1, 0));
                return '{"res":"' . $res . '"}';
            } else {
                return '{"res":"-1"}';
            }
        }
        return '{"res":"-2"}';
    }
    
    public static function setEdit(Database $db) {
        $params['alias'] = Request::req('alias');
        $params['password'] = sha1(Request::req('password'));
        $rpass = sha1(Request::req('password'));
        $session = new Session();
        $user = $session->getUser();
        if ($params['password'] != $rpass) {
            return '{"res":"-3"}';
        }
        if ($params['alias'] != null && $params['alias'] != '' && $params['password'] != null && $params['password'] != '') {
            $manager = new ManagerUser($db);
            if ($manager->exists($params['alias']) == false) {
                $res = $manager->set(new User($user->getId(), $params['alias'], $params['password'], 1, 0));
                return '{"res":"' . $res . '"}';
            } else {
                return '{"res":"-1"}';
            }
        }
        return '{"res":"-2"}';
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
        $session = new Session();
        $user = $session->getUser();
        $date = Request::req('date');
        $hour = Request::req('hour');
        if ($date == null || $date == '' || $hour == null || $hour == '') {
            return '{"action":"insert","res":"-2"}';
        } else if (self::getBookingNumber($db) > 0) {
            return '{"action":"insert","res":"-1","date":"' . $date . '","hour":"' . $hour . '"}';
        } else {
            $object = new Booking(NULL, $date, $hour, 1, $user->getId());
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
        $session = new Session();
        $user = $session->getUser();
        $params['date'] = Request::req('date');
        $params['hour'] = Request::req('hour');
        $params['user'] = $user->getId();
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
        $objects = json_decode($calendar, true);
        $manager = new ManagerUser($db);
        $calendar = '[';
        for ($i = 0; $i < count($objects); $i++) {
            $user = $manager->get($objects[$i]["user"]);
            $calendar .= '{"user":"' . $user->getAlias() . '"},';
        }
        $db->close();
        if (strlen($calendar) > 1) {
            $calendar = substr($calendar, 0, -1);
        }
        $calendar .= ']';
        return $calendar;
    }
    
    public static function setOut(Database $db) {
        $session = new Session();
        $user = $session->getUser();
        $user->setActive(0);
        $manager = new ManagerUser($db);
        $res = $manager->set($user);
        $db->close();
        return '{"res":"'.$res.'"}';
    }

}
