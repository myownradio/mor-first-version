<?php

class db
{

    private static $pdo = null;

    public static function connect()
    {
        $settings = config::getSection('database');
        self::$pdo = new PDO("mysql:host={$settings['db_hostname']};dbname={$settings['db_database']}", $settings['db_login'], $settings['db_password']);
    }

    public static function query($query, $params = array())
    {
        if (self::$pdo == null)
        {
            self::connect();
        }

        $res = self::$pdo->prepare($query);
        if ($res)
        {
            $res->execute($params);
            return $res->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return null;
        }
    }

    public static function query_single_col($query, $params = array())
    {
        if (self::$pdo == null)
        {
            self::connect();
        }

        $res = self::$pdo->prepare($query);
        if ($res)
        {
            $res->execute($params);
            $val = $res->fetch(PDO::FETCH_NUM);
            return $val[0];
        }
        else
        {
            return null;
        }
    }

    public static function query_single_row($query, $params = array())
    {
        if (self::$pdo == null)
        {
            self::connect();
        }

        $res = self::$pdo->prepare($query);
        if ( is_null($res) )
        {
            return null;
        }

        $res->execute($params);
        if ( $res->rowCount() > 0 )
        {
            return $res->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            return null;
        }
    }

    public static function query_update($query, $params = array())
    {
        if (self::$pdo == null)
        {
            self::connect();
        }
        $res = self::$pdo->prepare($query);
        $res->execute($params);
        return $res->rowCount();
    }

    public static function lastInsertId($name = NULL)
    {
        return self::$pdo->lastInsertId($name);
    }

}
