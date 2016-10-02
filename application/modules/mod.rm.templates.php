<?php
$path = "application/tmpl/";
$dir = opendir($path);
while($file = readdir($dir))
{
    if(is_file($path.$file))
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        echo "<script id=\"{$name}\" type=\"text/x-jquery-tmpl\">";
        echo file_get_contents($path.$file);
        echo "</script>";
    }
}
closedir($dir);
