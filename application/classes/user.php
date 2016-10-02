<?php

/* roman: define moved to startup.php */

class user
{
    /* REG_ERROR_USER_EXISTS, REG_CHECK_FAILED, REG_COMPLETE LOGIN_SUCCESS, LOGIN_FAILED, REG_MAIL */

    static function loginBySession()
    {
        /* TODO: make login by session */
    }
    
    static function registerUser()
    {
        if (empty(application::post('email')) || empty(application::post('login')) || empty(application::post('password')))
        {
            return 'REG_CHECK_FAILED';
        }
        else
        {
            $mail = application::post('email');
            $login = application::post('login');
            $password = application::post('password');
            $name = application::post('name');
            $info = application::post('info');
            $reg_date = time();
            $visit_date = $reg_date;
            $passwd = md5($login . $password);

            $data = db::query_update('INSERT INTO `r_users` VALUES (null, ?, ?, ?, ?, ?, ?, ?)', array($mail, $login, $passwd, $name, $info, $reg_date, $visit_date));
            if ($data == 0)
            {
                return 'REG_ERROR_USER_EXISTS';
            }
            else
            {
                return 'REG_COMPLETE';
            }
        }
    }



    static function loginUser()
    {
        $user = application::post('login');
        $password = application::post('password');
        $passwd = md5($user . $password);
        $data = db::query_single_row('SELECT * FROM `r_users` WHERE `login` = ? AND `password` = ?', array($user, $passwd));
        if ( is_array($data) )
        {
            session::set('login', $data['login']);
            session::set('password', $data['password']);
            db::query_update('UPDATE `r_users` SET `visit_date`=? WHERE `uid`=?', array(time(), session::get('uid')));

            return print_r($data, true);
            /*return 'LOGIN_SUCCESS';*/
        }
        return 'LOGIN_FAILED';
    }

    static function logoutUser()
    {
        session::destroy();
    }

    static function getCurrentUserId()
    {
        return 1;
    }

    static function init()
    {
        
    }

    static function requestRegistration()
    {
        $mail = application::post('email');
        
        if(preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $mail))
        {
            $link = md5($mail.time());

            db::query_update('UPDATE `r_users` SET `mail`=?, `confirmation_code`=?', array($mail, $link));

            $subject = 'Подтверждение регистрации на myownradio.biz';
            $message = 'Для завершения регистрации перейдите по указанной ссылке и заполните необходимые поля: ' . "http://myownradio.biz/registration/$link";
            $headers  = "MIME-Version: 1.0 \r\n";
            $headers .= "Content-Type: text/plain  charset=utf8 \r\n";
            $headers .= 'From:' .REG_MAIL. '<'.REG_MAIL.">\r\n";
            if ( mail($mail, $subject, $message, $headers) )
            {
                return 'REG_MESSAGE_SENT';
            }
            else
            {
                return 'REG_MESSAGE_DIDNT_SEND';
            }
            
        }
        else
        {
            return 'REG_ERROR_INCORRECT_EMAIL';
        }

    }
}
