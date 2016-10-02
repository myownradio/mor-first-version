<?php

spl_autoload_register('classloader');

/* Load configuration */
Registry::set('db_hostname'	, 'localhost');
Registry::set('db_database'	, 'myownradio');
Registry::set('db_login'	, 'root');
Registry::set('db_password'	, '');

db::connect(Registry::get('db_hostname'), Registry::get('db_database'), Registry::get('db_login'), Registry::get('db_password'));

function classloader($class_name) {
        $filename = strtolower($class_name) . '.php';
        $file = $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $filename;
        if (file_exists($file) == false) {
            return false;
        }
        include ($file);
}