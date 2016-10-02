<?php

class controller
{

    private static $obIgnore = array(
        'radiomanager/previewAudio',
        'radiomanager/eventListen',
        'streamStatus'
    );

    static function start()
    {
        user::loginBySession();

        $route = preg_replace('/(\.(html|php)$)|(\/+$)/', '', application::get('route', 'index'));
        
        //echo $route; exit();

        $route_exp = explode('/', $route);

        // Check private zone
        if ($route_exp[0] == 'radiomanager' && user::getCurrentUserId() == 0)
        {
            if (application::getMethod() === "GET")
            {
                header("HTTP/1.1 401 Unauthorized");
                header("refresh:0;url=/login");
                exit();
            }
            else
            {
                misc::errorJSON("ERROR_UNAUTHORIZED");
            }
        }

        // test for module alias
        $module = application::getModuleNameByAlias($route);
        if($module)
        {
            echo application::getModule($module, array(), application::getAll());
            exit();
        }
        
        $route_exp[count($route_exp) - 1] = sprintf('controller_%s', $route_exp[count($route_exp) - 1]);

        // Check universal module
        foreach (array('uni', strtolower(application::getMethod())) as $method)
        {
            $ctrl_file = sprintf("application/controllers/%s/%s.php", $method, implode('/', $route_exp));
            if (file_exists($ctrl_file))
            {
                if (is_bool(array_search($route, self::$obIgnore)))
                {
                    header("Connection: close");
                    ob_start("ob_gzhandler");
                    include $ctrl_file;
                    header("Content-Length: " . ob_get_length());
                    ob_end_flush();
                    flush();

                    //application::saveStat();
                    //misc::writeDebug("do something in the background");               
                }
                else
                {
                    include $ctrl_file;
                }
                exit();
            }
        }

        if (application::getMethod() == 'GET')
        {
            header("HTTP/1.1 404 Not found");
            application::getModule("error.404");
        }
        else
        {
            misc::errorJSON("ERROR_NOT_FOUND");
        }
    }

}
