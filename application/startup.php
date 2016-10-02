<?php

define("NEW_DIR_RIGHTS", 0770);
define("REG_MAIL", 'noreply@myownradio.biz');

define("REQ_INT", 'int');
define("REQ_STRING", 'string');

define("START_TIME", microtime(true));

spl_autoload_register(function ($class_name)
{
    $filename = strtolower($class_name) . '.php';
    $file = 'application/classes/' . $filename;
    if (file_exists($file) == false)
    {
        return false;
    }
    include ($file);
});

