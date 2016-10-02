<?php

$module = application::get("name", NULL, REQ_STRING);
$section = application::get("type", NULL, REQ_STRING);

$sections = array( // content-type, cacheable, executable, section
    'html'  => array('text/html', false, false, 'html'),
    'css'   => array('text/css', true, false, 'css'),
    'js'    => array('text/javascript', false, false, 'js'),
    'tmpl'  => array('text/x-jquery-tmpl', true, false, 'tmpl'),
    'exec'  => array('text/html', false, true, 'html')
);

if ( ! application::moduleExists($module))
{
    header("HTTP/1.1 404 Not Found");
    exit("<h1>File not found</h1>");
}

$data = application::fetchModule($module);

if ( ! isset($data[$sections[$section][3]]))
{
    header("HTTP/1.1 404 Not Found");
    exit("<h1>File not found</h1>");
}

if($sections[$section][1] == true)
{
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $data['unixmtime']) . " GMT");
    header('Cache-Control: max-age=0');

    /* Проверим не кэшировано ли изображение на стороне клиента */
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $data['unixmtime'])
    {
        header($_SERVER["SERVER_PROTOCOL"] . ' 304 Not Modified');
        die();
    }
}

header("Content-Type: " . $sections[$section][0]);

if($sections[$section][2])
{
    echo application::getModule($module, array(), application::getAll());
}
else
{
    echo $data[$sections[$section][3]];
}