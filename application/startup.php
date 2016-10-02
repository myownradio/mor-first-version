<?php

define("NEW_DIR_RIGHTS", 0770);
define("REG_MAIL", 'noreply@myownradio.biz');

spl_autoload_register('classloader');

function classloader($class_name) {
        $filename = strtolower($class_name) . '.php';
        $file = 'application/classes/' . $filename;
        if (file_exists($file) == false) {
            return false;
        }
        include ($file);
}

