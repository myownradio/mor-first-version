<?php

class controller 
{

    private static function post_controller($route) 
    {
    /* Doing some action */
        user::loginBySession();
        
        switch($route) 
        {
            case 'login': /* DONE */
                echo user::loginUser();
                break;
            case 'request': /* Request registration e-mail */
                echo user::requestRegistration();
                break;
            case 'confirm': /* Complete registration */
                echo user::registerUser();
                break;
            case 'radiomanager/upload': 
                echo tracks::AudioUpload();
                break;
            case 'radiomanager/changeinfo':
                echo tracks::changeInfo(
                        application::post('tid'), 
                        application::post('artist'), 
                        application::post('title'), 
                        application::post('genre')
                        );
                break;
            case 'radiomanager/removetrack':
                echo tracks::Remove(application::post('tid'));
                break;
            default:
		echo 'UNKNOWN_REQUEST';
        }
    }
	
    private static function get_controller($route) 
    {
	header("Content-Type: text/html; charset=utf8");
	switch($route) 
        {
            case 'api':
                $action = application::get('get_info');
                switch ($action)
                {
                    case 'current_track':
                        print_r( streams::getCurrentTrack(application::get('stream_id')) );
                        break;
                }
                break;
            case 'stream':              /* Display radio stream page */
                $stream     = stream::getStreamPosition(1);
                $next       = stream::getNextTrack(1, $stream['id']);
                echo 'Current time: ' . date("H:i:s", time()) . '<br>';
                echo 'Stream 1 position: <br>';
                echo 'track_id=<b>' . $stream['id'] . '</b> <br>';
                echo 'track_time=<b>' . misc::convertSecondsToTime($stream['time'] / 1000) . '</b> <br>';
                echo 'track_title=<b>' . $stream['title'] . '</b><br>';
                echo 'stream_pos=<b>'.$stream['position'].'</b><br>';
                echo 'Next track: ' . $next['artist'] . ' - ' . $next['title'] . '<br>';
                echo 'RTC: ' . streams::rtcCalculate(1, '2');
                break;
            case 'radiomanager': 
		echo '<html><body><form method="post" action="/radiomanager/upload" enctype="multipart/form-data"><input type="file" name="file" accept="audio/*" /><input type="submit" value="Upload" /></form></body></html>';
                break;
            case 'rnd':
		print_r( streams::getTracksFromStream(1) );
		break;
            default:
                /* Displaying 404 */
                echo '404';
                break;
        }
    }
	
    static function start() 
    {
        $route = trim( application::get('route', 'index'), '/' );

        if( application::getMethod() == 'GET' ) 
        {
            self::get_controller($route);
        }
        else
        {
            self::post_controller($route);
        }
        
    }

}
