<?php

class stream {

    static function getStreamPosition($id) {
        $stream = db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = ?", array($id));
        
        if( $stream == null )               return null;
        if( $stream['status'] == 0 )        return null;
        if( $stream['started'] == 0 )       return null;
        if( $stream['started_from'] == 0 )  return null;

        $tracks     = self::getTracksInStream($id);
        $duration   = tracks::getSelectionLength($tracks);
        $beginner = tracks::getTrackByIdArray($stream['started_from'], $tracks);

        if($beginner == null)               return null;
        
        $microtime = microtime(true) * 1000;
        $position = ($microtime - $stream['started'] + $stream['rtc'] + $beginner['offset']) % $duration;

        return tracks::getTrackAtPosition($position, $tracks);
    }

    static function getNextTrack($sid, $tid) {
        $stream     = db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = ?", array($sid));
        if( $stream == null )                           return null;
        if( $stream['status'] == 0 )                    return null;
        if( $stream['started'] == 0 )                   return null;
        if( $stream['started_from'] == 0 )              return null;

        $tracks      = db::query("SELECT * FROM `r_tracks` WHERE FIND_IN_SET(`tid`, :tids) AND `lores` = 1 AND `blocked` = 0 ORDER BY FIND_IN_SET(`tid`, :tids)", array('tids'=>$stream['tracklist']));
        
        if($tracks == null || count($tracks) == 0)      return null;
        
        for($i=0;$i<count($tracks);$i++) {
            if($tracks[$i]['tid'] == $tid) {
                if(isset($tracks[$i+1]))    
                    return $tracks[$i+1];
                else
                    return $tracks[0];
            }
        }
        return $tracks[0];
    }

}