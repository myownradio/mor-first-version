<?php

class streams 
{

    static function getAll() 
    {
        return db::query("SELECT * FROM `r_streams` WHERE 1");
    }
    
    static function getByUser($id) 
    {
        return db::query("SELECT * FROM `r_streams` WHERE `uid` = ?", array($id));
    }
    
    static function get($id) 
    {
        return db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = ? LIMIT 1", array($id));
    }

    static function remove($id) 
    {
        // Check permission first
        //$stream = db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = ? LIMIT 1", array($id));
	db::query_update("DELETE FROM `r_streams` WHERE `sid` = ?", array($id));
		
    }

    static function changeInfo($id, $title, $genres) 
    {
        // Check permission first
        // $stream = db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = ? LIMIT 1", array($id));
        db::query_update("UPDATE `r_streams` SET `name` = ?, `genres` = ? WHERE `sid` = ?", array($title, $genres, $id));
    }
    
    static function streamTracksReorder($id, $tr) 
    {
        /*
         * tracks list is array of hashes. hashes contains two keys: track_id and unique_id.
         * unique_id used for tracks identification
         */
        db::query_update("DELETE FROM `r_link` WHERE `stream_id` = ?", array($id));
        $t_order = 1;
        foreach($tr as $track)
        {
            db::query_update("INSERT INTO `r_link` VALUES (?, ?, ?, ?)", array($id, $track['track_id'], $t_order, $track['unique_id']));
            $t_order ++;
        }
    }
    
    static function streamTracksAdd($id, $tr) 
    {

        $track_last = db::query_single_col("SELECT COUNT(*) FROM `r_link` WHERE `stream_id` = ?", array($id));
        $track_unique = self::genUniqueId();
        return db::query_update("INSERT INTO `r_link` VALUES (?, ?, ?, ?)", array($id, $tr, $track_last + 1, $track_unique));
        
    }
    
    static function streamTracksRemove($id, $unique) 
    {
        $rows = db::query_update("DELETE FROM `r_link` WHERE `unique_id` = ? AND `stream_id` = ?", array($unique, $id));
        if($rows > 0)
        {
            self::streamTrackOptimize($id);
        }
        return $rows;
    }
    
    private static function streamTrackOptimize($id) 
    {
        db::query_update("call p_optimize_stream(?)", array($id));
    }
    
    /* Tracklist Processing */
    static function getTracksFromStream($stream_id) 
    {
	$tracks = db::query("SELECT a.*, b.`unique_id` FROM `r_tracks` a, `r_link` b WHERE `a`.`tid` = `b`.`track_id` AND `b`.`stream_id` = ? AND `a`.`lores` = 1 AND `a`.`blocked` = 0 ORDER BY `b`.`t_order` ASC", array($stream_id));
	$time_offset = 0;
	foreach($tracks as &$track) 
        {
            $track['time_offset'] = $time_offset;
            $time_offset += $track['duration'];
	}
	return $tracks;
    }
	
    static function getTracksDuration($tracks) 
    {
	$duration = 0;
	foreach($tracks as $track) 
        {
            $duration += $track['duration'];
	}
	return $duration;
    }

    static function getTrackAtPosition($tracks, $position)
    {
        foreach($tracks as $track)
        {
            if ( ($track['time_offset'] <= $position) && ($track['time_offset'] + $track['duration']) >= $position )
            {
                $track['cursor_position'] = $position - $track['time_offset'];
                return $track;
            }
        }
    }
    
    static function setNewStreamPosition($stream_id, $unique_id, $time_offset)
    {
        
    }
    
    static function getCurrentTrack($stream_id)
    {
        $stream                 = self::get($stream_id);
        $stream_tracks          = self::getTracksFromStream($stream_id);
        $stream_duration        = self::getTracksDuration($stream_tracks);
        
        $time_started           = microtime(true) * 1000;
        $playlist_position      = ($stream['started_from'] + $time_started - $stream['started'] - $stream['rtc']) % $stream_duration;

        return self::getTrackAtPosition($stream_tracks, $playlist_position);
    }
    
    static function genUniqueId() 
    {
        
        do 
        {
            $gen = misc::generateId();
        } 
        while ( db::query_single_col("SELECT COUNT(*) FROM `r_link` WHERE `unique_id` = ?", array($gen)) > 0 );
        
        return $gen;
        
    }

}