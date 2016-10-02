<?php

class application
{

    private static $args = null;

    private static function init()
    {
        self::$args = array(
            'METHOD' =>
                filter_input(INPUT_SERVER, 'REQUEST_METHOD'),
            'GET' =>
                !is_null( filter_input_array(INPUT_GET) ) ?
                filter_input_array(INPUT_GET) :
                NULL,
            'POST' =>
                !is_null( filter_input_array(INPUT_POST) ) ?
                filter_input_array(INPUT_POST) :
                NULL,
            'CLIENT' =>
                !is_null(filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP')) ?
                filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP') :
                filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
        );
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

    static function get($param, $default = null)
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return isset(self::$args['GET'][$param]) ? self::$args['GET'][$param] : $default;
    }

    static function post($param, $default = null)
    {
        if (empty(self::$args))
        {
            self::init();
        }
        return isset(self::$args['POST'][$param]) ? self::$args['POST'][$param] : $default;
    }

}
