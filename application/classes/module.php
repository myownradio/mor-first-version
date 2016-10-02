<?php

class module {

    static function getModule($module) {
        $module_path = "application/modules/";
        $module_file = $module_path . "module_" . $module . ".php";
        include $module_file;
        $inst = new $module();
        return $inst->generate();
    }

}

class module_global_class {
    function generate() {
        $view_path = "application/templates/view_" . get_class($this) . ".html";
        $content = file_get_contents($view_path);
        $content = preg_replace_callback("/\[module\](.+?)\[\/module\]/", array($this, 'parseModules'), $content);
        $content = preg_replace_callback("/\[model\](.+?)\[\/model\]/", array($this, 'parseModels'), $content);
        return $content;
    }
    
    function parseModules($module_string) {
        $module_name = $module_string[1];
        return module::getModule($module_name);
    }

    function parseModels($model_string) {
        $model_name = $model_string[1];
        return call_user_func($model_name);
    }
}