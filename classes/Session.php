<?php

//  Para esta clase usaremos el patrón SingleTon, que es un patrón que se usa 
//  cuando sólo queremos que se pueda tener una sola instancia de una clase.
/**
 * Description of Session
 *
 * @author Usuario
 */
class Session {

    private static $init = false;

    function __construct($name = NULL) {
        if (!self::$init) {
            if ($name !== NULL) {
                session_start($name);
            }
            session_start();
        }
        self::$init = true;
    }

    function isLoged() {
        return $this->getUser() !== NULL;
    }

    function get($nombre) {
        if (isset($_SESSION[$nombre])) {
            return $_SESSION[$nombre];
        }
        return null;
    }

    function getUser() {
        return $this->get('_user');
    }

    function set($nombre, $valor) {
        $_SESSION[$nombre] = $valor;
    }

    /*
     * Debemos pasarle un objeto de la clase User 
     */

    function setUser(User $param) {
        $this->set('_user', $param);
    }

    /*
     * Borra la variable de sesión pasada como parámetro
     */

    function erase($param) {
        if (isset($_SESSION[$param])) {
            unset($_SESSION[$param]);
            return true;
        }
        return false;
    }

    function destroy() {
        session_destroy();
    }

    function sendRedirect($address = 'index.php', $finish = true) {
        header("Location:$address");
        if ($finish === true) {
            exit();
        }
    }

}
