<?php

/**
 * Description of Controller
 *
 * @author MARTIN
 */
class Controller {

    //put your code here
    public static function hadle() {
        $method = Request::req('op') . Request::req('do');

        $session = new Session();
        $user = $session->getUser();
        if ($method == '' && $user != NULL) {
            return self::panelview();
        } else if ($method != '' && method_exists(__CLASS__, $method)) {
            return self::$method();
        } else {
            return self::frontview();
        }
    }

    /*
      ----------------------------
     * Funciones de vista
      ----------------------------

      --------------------------
     * FRONTEND
      --------------------------
     */

    private static function frontview() {
        $pattern = file_get_contents('../modules/artist/view/index.html');
        $content = file_get_contents('../modules/artist/view/templateindex.html');
        return str_replace('{variable}', $content, $pattern);
    }

    private static function aboutview() {
        $pattern = file_get_contents('../modules/artist/view/index.html');
        $content = file_get_contents('../modules/artist/view/templateabout.html');
        return str_replace('{variable}', $content, $pattern);
    }

    /*
      --------------------------
     * BACKEND
      --------------------------
     */

    private static function signupview($page = NULL) {
        $user = self::user();
        if (!$page) {
            $page = file_get_contents('../modules/users/view/signup.php');
        }
        if ($user != NULL && $user->getActive() == 1 && $user->getAdministrator() == 1) {
            $select = '<div class="form-group">
                                        <label for="password" class="white-font">Tipo de usuario</label>
                                        <select name="form-last-name" class="form-last-name form-control" id="form-last-name">
                                            <option>Administrador</option>
                                            <option>Personal</option>
                                            <option>Normal</option>
                                        </select>
                                    </div>';
            $page = str_replace('{select}', $select, $page);
        } else if ($user != NULL && $user->getActive() == '1' && $user->getPersonal() == '1') {
            $select = '<div class="form-group">
                                        <label for="password" class="white-font">Tipo de usuario</label>
                                        <select name="form-last-name" class="form-last-name form-control" id="form-last-name">
                                            <option>Personal</option>
                                            <option>Normal</option>
                                        </select>
                                    </div>';
            $page = str_replace('{select}', $select, $page);
        } else {
            $select = '';
            $page = str_replace('{select}', $select, $page);
        }
        return $page;
    }

    private static function loginview() {
        if (!self::user()) {
            return file_get_contents('../modules/users/view/signin.php');
        }
        return self::panelview();
    }

    private static function panelview() {
        return self::paneloption(file_get_contents('../modules/users/view/controlpanel.php'));
    }

    private static function paneloption($page) {
        $user = self::user();
        if ($user->getAdministrator() == 1) {
            return str_replace('{opciones}', file_get_contents('../modules/users/view/option-admin.php'), $page);
        } else if ($user->getPersonal() == 1) {
            
        }
        return str_replace('{opciones}', file_get_contents('../modules/users/view/option-normal.php'), $page);
    }

    private static function listuserview() {
        $users = self::listusers();
        $page = file_get_contents('../modules/users/view/controlpanel.php');
        $table = file_get_contents('../modules/users/view/usertable.php');
        $set = '';
        foreach ($users as $user) {
            $pattern = file_get_contents('../modules/users/view/userlistrow.php');
            foreach ($user->get() as $key => $value) {
                if ($value == '0') {
                    $value = 'NO';
                } else if ($value == '1') {
                    $value = 'SI';
                }
                $pattern = str_replace('{' . $key . '}', $value, $pattern);
            }
            $pattern = str_replace('{editar}', '<a href="?op=edit&do=view&email=' . $user->getEmail() . '"><button>Editar</button></a>', $pattern);
            $pattern = str_replace('{borrar}', '<a href="?op=delete&do=set&email=' . $user->getEmail() . '"><button>Borrar</button></a>', $pattern);
            $set .= $pattern;
        }
        $table = str_replace('{filas}', $set, $table);
        $page = str_replace('{opciones}', $table, $page);
        return $page;
    }

    private static function editview($param = NULL) {
        $email = $param;
        if ($email == NULL) {
            $email = Request::req('email');
        }
        $page = file_get_contents('../modules/users/view/edit.php');
        $item = self::user($email);
        foreach ($item->get() as $key => $value) {
            $page = str_replace('{' . $key . '}', $value, $page);
        }
        $active = Render::input('checkbox', 'active', 1, NULL, array('form-control'), $item->getActive());
        $administrator = Render::input('checkbox', 'administrator', 1, NULL, array('form-control'), $item->getAdministrator());
        $personal = Render::input('checkbox', 'personal', 1, NULL, array('form-control'), $item->getPersonal());
        $page = $page = str_replace('{activo}', $active, $page);
        $page = $page = str_replace('{administrador}', $administrator, $page);
        $page = $page = str_replace('{pers}', $personal, $page);
        return self::signupview($page);
    }

    /*
      ----------------------------------------
     * Artista
      ----------------------------------------
     */

    private static function artistsignupview() {
        $page = file_get_contents('../modules/artist/view/templateeditprofile.html');
        $page = $page = str_replace('{name}', '', $page);
        $page = $page = str_replace('{biography}', '', $page);
    }

    private static function editartistview() {

        $page = file_get_contents('../modules/artist/view/templateeditprofile.html');

        return $page;
    }

    /* ----------------------------
     * Funciones de acción
     * ----------------------------
     */

    private static function signupset() {
        $user = new User();
        $params = Request::reqFull();
        $user->set($params);
        if($params['alias'] == ''){
            $params['alias'] = $params['email'];
        }
        $db = new Database();
        $manager = new ManagerUser($db);
        if (!$manager->exists($user)) {
            date_default_timezone_set('Europe/Madrid');
            $today = getdate();
            $user->setDischargeDate($today['year'] . '-' . $today['mon'] . '-' . $today['mday']);
            $r = $manager->insert($user);

            if ($r > 0) {
                header('Location:users/extra/sendmail.php?user=' . Encryption::sha1Encrypt($params['alias']) . '&email=' . urlencode($params['email']) . '&time=' . Server::getRequestDate());
            }
        }
        $db->close();
    }

    private static function login() {
        $params['alias'] = Request::req('alias');
        $params['password'] = sha1(Request::req('password'));

        $db = new Database();
        $manager = new ManagerUser($db);
        $user = $manager->login($params);
        $db->close();
        if (!$user) {
            return self::loginview();
        } else {
            $session = new Session();
            $session->setUser($user);
            return self::panelview();
        }
    }

    private static function logout() {
        $session = new Session();
        $session->erase('_user');
        $session->destroy();
        return self::loginview();
    }

    private static function locklock() {
        $params['alias'] = Request::req('alias');
        $params['password'] = sha1(Request::req('password'));

        $db = new Database();
        $manager = new ManagerUser($db);
        $user = $manager->login($params);
        $db->close();
        if (!$user) {
            return self::loginview();
        } else {
            return self::panelview();
        }
    }

    private static function unlockset() {
        $params['alias'] = Request::req('user');
        $time = Request::req('time');

        $db = new Database();
        $manager = new ManagerUser($db);

        $user = $manager->unlock($params);
        $db->close();

        return self::loginview();
    }

    private static function user($param = NULL) {
        $user = NULL;
        if ($param == NULL) {
            $session = new Session();
            $user = $session->getUser();
        } else {
            $db = new Database();
            $manager = new ManagerUser($db);
            $user = $manager->get($param);
            $db->close();
        }
        return $user;
    }

    private static function listusers() {
        $user = self::user();
        $db = new Database();
        $manager = new ManagerUser($db);
        $users = $manager->getList();
        $db->close();
        return $users;
    }

    private static function editset() {
        $user = self::user();
        if ($user && Request::get('password') == Request::get('rpassword')) {
            $db = new Database();
            $manager = new ManagerUser($db);
            $user->set(Request::reqFull());
            $res = $manager->set($user, Request::req('old-email'));
            $db->close();
        }
        header('location:?op=edit&do=view');
    }

}
