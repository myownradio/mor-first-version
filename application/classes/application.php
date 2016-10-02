<?php

class application
{

    private static $args = NULL;
    private static $objects = array();
    private static $utime = NULL;
    static $listener_id = NULL;

    static function getMicroTime($realtime = false)
    {
        if (is_null(self::$utime) || ($realtime == true))
        {
            self::$utime = microtime(true) * 1000;
        }
        return self::$utime;
    }

    static function singular()
    {
        $args = func_get_args();
        $serial = serialize($args);
        if (count($args) == 0)
        {
            return null;
        }
        else
        {
            $class = array_shift($args);
        }
        if ( ! isset(self::$objects{$serial}))
        {
            $refl = new ReflectionClass($class);
            self::$objects{$serial} = call_user_func_array(array($refl, "newInstance"), $args);
        }
        return self::$objects{$serial};
    }

    static function saveStat()
    {
        db::query_update("INSERT INTO `r_stats_memory` VALUES (NULL, ?, ?, ?, ?, ?, NOW())", array(
            application::getClient(),
            user::getCurrentUserId(),
            "http" . (isset($_SERVER['HTTPS']) ? "s" : "") .  "://" . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI'),
            filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')
        ));
    }

    private static function init()
    {
        self::$args = array(
            'METHOD' =>
            filter_input(INPUT_SERVER, 'REQUEST_METHOD'),
            'LANG' =>
            substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2),
            'GET' =>
            ! is_null(filter_input_array(INPUT_GET)) ?
                    filter_input_array(INPUT_GET) :
                    NULL,
            'POST' =>
            ! is_null(filter_input_array(INPUT_POST)) ?
                    filter_input_array(INPUT_POST) :
                    NULL,
            'CLIENT' =>
            ! is_null(filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP')) ?
                    filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP') :
                    filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
            'ROOT' =>
            filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')
        );
    }

    static function getClient()
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return self::$args['CLIENT'];
    }

    static function getLanguage()
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return self::$args['LANG'];
    }

    static function getApplication()
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return self::$args;
    }

    static function getMethod()
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return self::$args['METHOD'];
    }

    static function getRoot()
    {
        return self::$args['ROOT'];
    }

    static function get($param, $default = NULL, $type = "string")
    {
        if (empty(self::$args))
        {
            self::init();
        }
        if (!isset(self::$args['GET'][$param]))
        {
            return $default;
        }
        switch ($type)
        {
            case 'string':
                return (string) self::$args['GET'][$param];
            case 'int':
                return (int) self::$args['GET'][$param];
            default:
                return self::$args['POST'][$param];
        }
    }
    
    static function getAll()
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return self::$args['GET'];
    }

    static function post($param, $default = NULL, $type = "string")
    {
        if (empty(self::$args))
        {
            self::init();
        }
        if (!isset(self::$args['POST'][$param]))
        {
            return $default;
        }

        switch ($type)
        {
            case 'string':
                return (string) self::$args['POST'][$param];
            case 'int':
                return (int) self::$args['POST'][$param];
            default:
                return self::$args['POST'][$param];
        }
    }

    static function parseModules($contents, $history = array(), $_MODULE = NULL )
    {
        return preg_replace_callback("/\<\!\-\-\s+module\:(.+)\s+\-\-\>/", function ($match) use ($history, $_MODULE)
        {
            return self::getModule($match[1], $history, $_MODULE);
        }, $contents);
    }

    static function getModuleNameByAlias($alias)
    {
        return db::query_single_col("SELECT `name` FROM `r_modules` WHERE `alias` = ? LIMIT 1", array($alias));
    }
    
    static function getModule($data, $history = array(), $_MODULE = NULL)
    {
        /* prevention of recursion  */
        if (is_int(array_search($data, $history)))
        {
            return sprintf("Recursive call: Module \"%s\" called again from module \"%s\"!", $data, end($history));
        }

        $module_path = sprintf("application/modules/mod.%s.php", $data);
        $r = NULL;
        if (file_exists($module_path))
        {
            $history[] = $data;
            $module_content = file_get_contents($module_path); 
            $module_content = self::parseModules( $module_content, $history, $_MODULE );
            $r = misc::execute( $module_content, $_MODULE ); 
        }
        else if(self::moduleExists($data)) 
        {
            $history[] = $data;
            $module = self::fetchModule($data);
            
            if(application::getMethod() === "GET")
            {
                $module_content = $module['html'];
                
                // CSS Style Sheet
                if(strlen($module['css']) > 0)
                {
                    if(strpos($module['html'], '<!-- include:css -->', 0) !== false)
                    {
                        $module_content = str_replace('<!-- include:css -->', "<link rel=\"stylesheet\" type=\"text/css\" href=\"/modules/css/{$data}.css\" />\r\n", $module_content);
                    }
                    else
                    {
                        $module_content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"/modules/css/{$data}.css\" />\r\n";
                    }
                }

                // Templates
                if(strlen($module['tmpl']) > 0)
                {
                    if(strpos($module['html'], '<!-- include:tmpl -->', 0) !== false)
                    {
                        $module_content = str_replace('<!-- include:tmpl -->', $module['tmpl'], $module_content);
                    }
                    else
                    {
                        $module_content .= $module['tmpl'];
                    }
                }
                
                // JavaScript
                if(strlen($module['js']) > 0)
                {
                    if(strpos($module['html'], '<!-- include:js -->', 0) !== false)
                    {
                        $module_content = str_replace('<!-- include:js -->', "<script src=\"/modules/js/{$data}.js\"></script>\r\n", $module_content);
                    }
                    else
                    {
                        $module_content .= "<script src=\"/modules/js/{$data}.js\"></script>\r\n";
                    }
                }

                $module_content = self::parseModules( $module_content, $history, $_MODULE );
                $r = misc::execute( $module_content, $_MODULE ); 
            }
            else
            {
                $r = misc::execute( $module['post'], $_MODULE ); 
            } 
            
        }
        else
        {
            $r = sprintf("Module \"%s\" not found!", $data);
        }
        return $r;
    }
    
    static function moduleExists($name)
    {
        $redisKey = "myownradio.biz:modules:{$name}";
        if(myredis::handle()->exists($redisKey))
        {
            return true;
        }
        return db::query_single_col("SELECT COUNT(*) FROM `r_modules` WHERE `name` = ?", array($name));
    }

    static function fetchModule($name)
    {
        /*
        $redisKey = "myownradio.biz:modules:{$name}";
        if(myredis::handle()->exists($redisKey))
        {
            $keys = array("html", "css", "js", "tmpl", "post");
            $return = myredis::handle()->hgetall($redisKey);
            foreach($keys as $key)
            {
                if(!isset($return[$key]))
                {
                    $return[$key] = "";
                }
            }
        } */
        return db::query_single_row("SELECT *, UNIX_TIMESTAMP(`modified`) as `unixmtime` FROM `r_modules` WHERE `name` = ?", array($name));
    }
}
