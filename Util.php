<?php

class Util {

    public static function dayName($number) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $week;
        switch ($lang) {
            case "fr":
                $week = array('','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
            case "it":
                $week = array('','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato','Domenica');
            case "en":
                $week = array('','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
            default:
                $week = array('','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');
        }
        return $week[$number];
    }
}