<?php

class user
{

    private static $userData = NULL;
    private static $userRights = NULL;
    private static $userSubs = NULL;
    private static $uid;

    static function loginBySession()
    {
        if(application::getMethod() == "GET")
        {
            $token = session::get('authtoken');
        }
        else
        {
            $token = application::post('authtoken');
        }
        
        session::end();
        
        $data = db::query_single_row('SELECT a.`token`, b.`uid`, b.`login`, b.`name`, b.`rights` FROM `r_sessions` a LEFT JOIN `r_users` b ON a.`uid` = b.`uid` WHERE a.`token` = ? AND a.`ip` = ?', array($token, application::getClient()));
        
        if (!is_null($data))
        {
            self::$userData = $data;

            // Update user's last activity
            db::query_update('UPDATE `r_users` SET `last_visit_date` = ? WHERE `uid` = ?', array(time(), self::$userData['uid']));
            // Get user's persmissions/limits
            self::$userRights = db::query_single_row("SELECT * FROM `r_limitations` WHERE `level` = IFNULL((SELECT `plan` FROM `r_subscriptions` WHERE `uid` = ? AND `expire` > ? ORDER BY `id` DESC LIMIT 1), 0)", array(self::$userData['uid'], time()));
            // Get user's current subscription
            self::$userSubs = db::query_single_row("SELECT * FROM `r_subscriptions` WHERE `uid` = ? AND `expire` > ? ORDER BY `id` DESC LIMIT 1", array(self::$userData['uid'], time()));

            return misc::outputJSON('SESSION_LOGIN_SUCCESS');
        }
        else
        {
            return misc::outputJSON('SESSION_LOGIN_FAILED');
        }
    }

    static function getUserByUid($uid)
    {
        return db::query_single_row('SELECT * FROM `r_users` WHERE `uid` = ?', array($uid));
    }
    
    static function loginUser()
    {
        $user = application::post('login');
        $password = application::post('password');
        $passwd = md5($user . $password);
        $saveCookie = application::post('remember', 'off', REQ_STRING);
        
        misc::writeDebug($saveCookie);
        
        $data = db::query_single_row('SELECT * FROM `r_users` WHERE `login` = ? AND `password` = ?', array($user, $passwd));
        if (!is_null($data))
        {
            if($saveCookie === "on")
            {
                session::init(true);
            }
            
            db::query_update('UPDATE `r_users` SET `last_visit_date` = ? WHERE `uid` = ?', array(time(), $data['uid']));

            // Generate token
            $token = self::createToken($data['uid'], application::getClient(), filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'), session::getID());
            session::set('authtoken', $token);

            return misc::outputJSON('LOGIN_SUCCESS');
        }
        return misc::outputJSON('LOGIN_FAILED');
    }

    static function createToken($uid, $ip, $ua, $session_id)
    {
        do
        {
            $token = md5($uid . $ip . rand(1,1000000) . "tokenizer" . time());
        }
        while(db::query_single_col("SELECT COUNT(*) FROM `r_sessions` WHERE `token` = ?", array($token)) > 0);
        
        db::query_update("INSERT INTO `r_sessions` SET `uid` = ?, `ip` = ?, `token` = ?, `authorized` = NOW(), `http_user_agent` = ?, `session_id` = ?", array(
            $uid, $ip, $token, $ua, $session_id
        ));
        
        return $token;
    }
    
    static function logoutUser()
    {
        $token = session::get('authtoken');
        db::query_update("DELETE FROM `r_sessions` WHERE `token` = ?", array($token));
        session::remove('authtoken');
        session::end();
        return misc::outputJSON('LOGOUT_SUCCESS');
    }

    static function getCurrentUserId()
    {
        return ! is_null(self::$userData['uid']) ? self::$userData['uid'] : 0;
    }

    static function getCurrentUserName()
    {
        return ! is_null(self::$userData['login']) ? self::$userData['login'] : "Guest";
    }
    
    static function getCurrentUserToken()
    {
        return !is_null(self::$userData['token']) ? self::$userData['token'] : "";
    }

    static function getCurrentUserRights()
    {
        return !is_null(self::$userData['rights']) ? self::$userData['rights'] : 0;
    }
    
    static function requestRegistration()
    {
        $mail = strtolower(application::post('email', "", REQ_STRING));

        if ( ! preg_match("/^[\w\S]+@[\w\S]+\.[\w]{2,4}$/m", $mail))
        {
            return misc::outputJSON('REG_ERROR_INCORRECT_EMAIL');
        }

        if (db::query_single_col("SELECT COUNT(`uid`) FROM `r_users` WHERE `mail` = ?", array($mail)) > 0)
        {
            return misc::outputJSON('REG_ERROR_EMAIL_EXISTS');
        }

        $code = md5($mail . "@myownradio.biz@" . $mail);

        $confirmLink = base64_encode(json_encode(array('email' => $mail, 'code' => $code)));

        $subject = "Registration on myownradio.biz";

        $headers  = "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $headers .= "From: myownradio.biz <no-reply@myownradio.biz>\r\n";

        $message = "<span style='font-size: 12pt;'>";
        $message .= "<b>Registration on myownradio.biz</b><br><br>";
        $message .= "To confirm your email and complete registration on <b>myownradio.biz</b> please follow this link:<br>";
        $message .= "<a href='http://myownradio.biz/confirm/$confirmLink'>http://myownradio.biz/confirm/$confirmLink</a><br>";
        $message .= "<br>";
        $message .= "<br>";
        $message .= "Sincerely yours,<br>";
        $message .= "The myownradio team";
        $message .= "</span>";

        if (mail($mail, $subject, $message, $headers, "-fno-reply@myownradio.biz"))
        {
            misc::writeDebug("Registration request: " . $mail);
            return misc::outputJSON('REG_MESSAGE_SENT', $mail);
        }
        else
        {
            misc::writeDebug("Registration request failed: " . $mail);
            return misc::outputJSON('REG_MESSAGE_DIDNT_SEND');
        }
    }

    static function registerUser()
    {

        //TODO: login, password1, password2, name, info

        if(strlen(application::post('login')) < 3)
        {
            return misc::outputJSON('REG_ERROR_SHORT_LOGIN');
        }
        
        if(strlen(application::post('password1')) < 6)
        {
            return misc::outputJSON('REG_ERROR_SHORT_PASSWORD');
        }

        if (application::post('password1') != application::post('password2'))
        {
            return misc::outputJSON('REG_ERROR_PASSWORDS_MISMATCH');
        }

        $code = application::get('code');

        try
        {
            $codeArray = json_decode(base64_decode($code), true);
        }
        catch (Exception $ex)
        {
            return misc::outputJSON('REG_ERROR_NOT_ENOUTH_PARAMS');
        }

        if (md5($codeArray['email'] . "@myownradio.biz@" . $codeArray['email']) != $codeArray['code'])
        {
            return misc::outputJSON('CODE_INCORRECT');
        }

        if (db::query_single_col("SELECT COUNT(`uid`) FROM `r_users` WHERE `mail` = ?", array($codeArray['email'])) > 0)
        {
            return misc::outputJSON('REG_ERROR_EMAIL_EXISTS');
        }

        //STOP CODE

        $ins = db::query_update('INSERT INTO `r_users` SET `mail` = ?, `login` = ?, `password` = ?, `name` = ?, `info` = ?, `register_date` = ?', array(
                    $codeArray['email'],
                    application::post('login'),
                    md5(application::post('login') . application::post('password1')),
                    application::post('name'),
                    application::post('info'),
                    time()
        ));

        if ($ins > 0)
        {
            $uid = db::lastInsertId();
            misc::writeDebug(sprintf("New user registered: id=%d, mail=%s", $uid, $codeArray['email']));
            self::createUserDirectory($uid);
            return misc::outputJSON('REG_COMPLETE');
        }
        else
        {
            return misc::outputJSON('REG_ERROR_USER_EXISTS');
        }
    }

    static function codeCheck()
    {

        $code = application::get('code');

        try
        {
            $codeArray = json_decode(base64_decode($code), true);
        }
        catch (Exception $ex)
        {
            return application::getModule("us.error.regcode");
        }

        if (db::query_single_col("SELECT COUNT(`uid`) FROM `r_users` WHERE `mail` = ?", array($codeArray['email'])) > 0)
        {
            return application::getModule("us.error.confirm");
        }

        if (md5($codeArray['email'] . "@myownradio.biz@" . $codeArray['email']) != $codeArray['code'])
        {
            return application::getModule("us.error.regcode");
        }

        return application::getModule("page.us.signup2");
    }

    private static function createUserDirectory($user_id)
    {
        $new_path = sprintf("%s/ui_%d", config::getSetting("content", "content_folder"), $user_id);
        if ( ! is_dir($new_path))
        {
            return mkdir($new_path, NEW_DIR_RIGHTS, true);
        }
        return false;
    }

    static function userUploadLimit()
    {
        return (int) self::$userRights['upload_limit'] * 60000;
    }

    static function userStreamsMax()
    {
        return (int) self::$userRights['streams_max'];
    }

    static function userUploadLeft()
    {
        if (self::userUploadLimit() > 0)
        {
            $total_time = track::getTracksDuration(self::getCurrentUserId());
            return (self::userUploadLimit() - $total_time > 0) ? (self::userUploadLimit() - $total_time) : 0;
        }
        else
        {
            return false;
        }
    }

    static function userActivePlan()
    {
        return self::$userRights['level'];
    }

    static function userPlanExpire()
    {
        return ! is_null(self::$userSubs) ? self::$userSubs['expire'] : 0;
    }

}
