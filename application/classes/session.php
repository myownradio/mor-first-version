<?php

class session
{

    static function get($arg)
    {
        if (!empty(filter_input(INPUT_COOKIE, 'PHPSESSID')))
        {
            if (session_status() == PHP_SESSION_NONE)
            {
                self::init();
            }
            if (isset($_SESSION[$arg]))
            {
                return $_SESSION[$arg];
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    static function set($arg, $val)
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            self::init();
        }
        $_SESSION[$arg] = $val;
        return new self();
    }

    static function remove($arg)
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            self::init();
        }
        if (isset($_SESSION[$arg]))
        {
            unset($_SESSION[$arg]);
        }
        return self;
    }

    static function destroy()
    {
        unset($_SESSION);
        session_unset();
        session_destroy();
    }

    static function end()
    {
        if (session_status() != PHP_SESSION_NONE)
        {
            session_write_close();
            $_SESSION = null;
        }
        return self;
    }

    static private function init()
    {
        session_set_cookie_params(60 * 60 * 24 * 14);
        session_start();
    }

}
