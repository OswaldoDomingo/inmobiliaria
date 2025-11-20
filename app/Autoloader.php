<?php
// app/Autoloader.php

class Autoloader {

    public static function registrar() {
        spl_autoload_register(function ($clase) {
            // Convertimos el namespace (si usáramos) o nombre de clase a ruta de archivo
            // Ejemplo: Si pido "ControladorPropiedades", buscará en app/controllers/
            
            $archivo = '';
            
            if (file_exists(APP . 'controllers/' . $clase . '.php')) {
                $archivo = APP . 'controllers/' . $clase . '.php';
            } elseif (file_exists(APP . 'models/' . $clase . '.php')) {
                $archivo = APP . 'models/' . $clase . '.php';
            } elseif (file_exists(APP . 'core/' . $clase . '.php')) { // Para clases base
                $archivo = APP . 'core/' . $clase . '.php';
            }

            if (file_exists($archivo)) {
                require_once $archivo;
            } else {
                // Opcional: Lanzar error si no encuentra la clase crítica
                // die("La clase $clase no se pudo cargar."); 
            }
        });
    }
}