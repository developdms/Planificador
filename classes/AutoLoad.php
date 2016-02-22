<?php
/**
 * Description of AutoLoad
 *
 * @author MARTIN
 */
class AutoLoad {

    static public function carga($clase) {
        
        $archivo = "classes/" . str_replace('\\', '/', $clase) . '.php';
        if (file_exists($archivo)) {
            require $archivo;
        } else if (file_exists('../' . $archivo)) {
            require '../' . $archivo;
        } else if (file_exists('../../' . $archivo)) {
            require '../../' . $archivo;
        } else if (file_exists('../../../' . $archivo)) {
            require '../../../' . $archivo;
        } else {
            $modules = FileManager::scan('../modules');
            foreach ($modules as $value) {
                if (file_exists($value.'/' . $archivo)){
                    require $value.'/' . $archivo;
                    break;
                }
            }
        }
    }

}

spl_autoload_register('AutoLoad::carga');
