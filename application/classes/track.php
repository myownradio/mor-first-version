<?php

class track
{

    protected $track_object = NULL;

    function __construct($track_id)
    {
        if(is_int($track_id) || is_string($track_id))
        {
            $this->track_object = db::query_single_row("SELECT * FROM `r_tracks` WHERE `tid` = ?", array($track_id));
        }
        else if(is_array($track_id))
        {
            $this->track_object = $track_id;
        }
    }

    function originalFile()
    {
        if(is_array($this->track_object))
        {
            return sprintf("%s/ui_%d/a_%03d_original.%s", 
                    config::getSetting("content", "content_folder"), 
                    $this->track_object['uid'], 
                    $this->track_object['tid'], 
                    $this->track_object['ext']
                    );
        }
        return null;
    }

    function hasLowResolution()
    {
        return $this->track_object['lores'] == '1';
    }
    
    function lowQualityFile()
    {
        if(is_array($this->track_object))
        {
            return sprintf("%s/ui_%d/lores_%03d.mp3", config::getSetting("content", "content_folder"), $this->track_object['uid'], $this->track_object['tid']);
        }
        return null;
    }

    function makeArray()
    {
        return $this->track_object;
    }
    
    function Exists()
    {
        return is_array($this->track_object);
    }

    function getTrackOwner()
    {
        if(is_array($this->track_object))
        {
            return $this->track_object['uid'];
        }
    }
    
    function getTrackCaption()
    {
        return sprintf("%s - %s", $this->track_object['artist'], $this->track_object['title']);
    }

    function getTrackId()
    {
        return (int) $this->track_object['tid'];
    }
    
    function getTrackTitle()
    {
        return $this->track_object['title'];
    }
    
    function getTrackArtist()
    {
        return $this->track_object['artist'];
    }
    
    function getTrackDuration()
    {
        return (int) $this->track_object['duration'];
    }

    
    function getTrackStreams()
    {
        $stream_array = array();
        foreach(db::query("SELECT `stream_id`, `unique_id` FROM `r_link` WHERE `track_id` = ?", array($this->getTrackId())) as $stream)
        {
            if(isset($stream_array[$stream['stream_id']]))
            {
                $stream_array[$stream['stream_id']][] = $stream['unique_id'];
            }
            else
            {
                $stream_array[$stream['stream_id']] = array($stream['unique_id']);
            }
        }
        return $stream_array;
    }
    
    function selfDelete()
    {
        if(is_array($this->track_object))
        {
            db::query_update("DELETE FROM `r_tracks` WHERE `tid` = ?", array($this->track_object['tid']));
            $this->track_object = NULL;
        }
    }

    /* 
     * Static methods section
     */
    static function updateFileInfo($track_id, $artist = "", $title = "", $album = "", $track_number = "", $genre = "", $date = "")
    {
        $track = new track($track_id);
        
        if(!$track->Exists())
        {
            return misc::outputJSON("UPDATE_ERROR_TRACK_NOT_EXISTS") ;
        }
        
        if($track->getTrackOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("UPDATE_ERROR_NOT_OWNER");
        }
        
        $rows = db::query_update("UPDATE `r_tracks` SET `artist` = ?, `title` = ?, `album` = ?, `track_number` = ?, `genre` = ?, `date` = ? WHERE `tid` = ?", array(
            $artist,
            $title,
            $album,
            $track_number,
            $genre,
            $date,
            $track_id
        ));
        
        unset($track);
        
        if($rows > 0)
        {
            return misc::outputJSON("UPDATE_SUCCESS");
        }
        else
        {
            return misc::outputJSON("UPDATE_NOT_MODIFIED");
        }
    }
    
    static function deleteTrackFile($track_id)
    {
        $successfullyDeleted = 0;
        $trackList = explode(",", $track_id);
        foreach( $trackList as $tr )
        {
            $track = new track($tr);
            
            if(!$track->Exists())
            {
                unset($track);
                continue;
            }
            
            if($track->getTrackOwner() != user::getCurrentUserId())
            {
                unset($track);
                continue;
            }
            
            foreach($track->getTrackStreams() as $stream_id=>$unique_ids)
            {
                $stream_instance = new stream($stream_id);
                $stream_instance->reloadTracks()->removeTrack(implode(",", $unique_ids));
                unset($stream_instance);
            }
            
            // Delete physical file
            $or_file = $track->originalFile();
            $lq_file = $track->lowQualityFile();

            $or_st = file_exists($or_file) ? unlink($or_file) : 1;
            $lq_st = file_exists($lq_file) ? unlink($lq_file) : 1;

            misc::writeDebug("Original: " . $or_st);
            misc::writeDebug("Low quality: " . $lq_st);
            
            if(! ($or_st && $lq_st) )
            {
                unset($track);
                continue;
            }
            
            $track->selfDelete();
            
            unset($track);
            
            $successfullyDeleted ++;
        }

        if( $successfullyDeleted == count($trackList) )
        {    return misc::outputJSON("DELETE_SUCCESS"); }
        else if( $successfullyDeleted == 0 )
        {    return misc::outputJSON("NOTHING_DELETED"); }
        else
        {    return misc::outputJSON("DELETE_PARTIAL"); }
    }

    static function uploadFile($file)
    {
        if( !isset($file) || $file['error'] != 0)
        {
            return misc::outputJSON("UPLOAD_ERROR_NO_FILE") ;
        }

        if(array_search($file['type'], config::getSetting('upload', 'supported_audio')) == -1)
        {
            return misc::outputJSON("UPLOAD_ERROR_UNSUPPORTED");
        }

        $audio_tags = misc::get_audio_tags($file['tmp_name']);

        if(empty($audio_tags['DURATION']))
        {
            return misc::outputJSON("UPLOAD_ERROR_CORRUPTED_AUDIO");
        }

        if($audio_tags['DURATION'] > user::userUploadLeft())
        {
            return misc::outputJSON("UPLOAD_ERROR_NO_SPACE");
        }
        
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        
        db::query_update("INSERT INTO `r_tracks` SET "
                . "`uid` = ?, "
                . "`filename` = ?, "
                . "`ext` = ?, "
                . "`track_number` = ?, "
                . "`artist` = ?, "
                . "`title` = ?, "
                . "`album` = ?, "
                . "`genre` = ?, "
                . "`date` = ?, "
                . "`duration` = ?, "
                . "`filesize` = ?, "
                . "`uploaded` = ?", 
            array(
                user::getCurrentUserId(),
                $file['name'],
                $extension,
                empty($audio_tags["TRACKNUMBER"])   ? ""                    : $audio_tags["TRACKNUMBER"],
                empty($audio_tags["PERFORMER"])     ? "Unknown Artist"      : $audio_tags["PERFORMER"],
                empty($audio_tags["TITLE"])         ? $file['name']         : $audio_tags["TITLE"],
                empty($audio_tags["ALBUM"])         ? "Unknown Album"       : $audio_tags["ALBUM"],
                empty($audio_tags["GENRE"])         ? "Unknown Genre"       : $audio_tags["GENRE"],
                empty($audio_tags["RECORDED_DATE"]) ? ""                    : $audio_tags["RECORDED_DATE"],
                $audio_tags["DURATION"],
                filesize($file['tmp_name']),
                time()
            )
        );

        $last_id = db::lastInsertId();

        if(!$last_id)
        {
            return misc::outputJSON("UPLOAD_WAS_NOT_ADDED");
        }

        if(move_uploaded_file($file['tmp_name'], (new track($last_id))->originalFile()))
        {
            misc::writeDebug(sprintf("User #%s successfully uploaded track \"%s\"", user::getCurrentUserId(), $file['name']));
            return misc::outputJSON("UPLOAD_SUCCESS");
        }
        else
        {
            return misc::outputJSON("UPLOAD_ERROR_DISK_ACCESS_ERROR");
        }
        
    }
    
    static function getTracks($user_id, $from = 0, $limit = 5000)
    {
        $track_data = db::query("SELECT * FROM `r_tracks` WHERE `uid` = ? ORDER BY `uploaded` DESC LIMIT $from, $limit", array($user_id));
        return $track_data;
    }

    static function getFilteredTracks($user_id, $match = "*", $from = 0, $limit = 5000)
    {
        $track_data = db::query("SELECT * FROM `r_tracks` WHERE `uid` = ? AND MATCH(`artist`, `album`, `title`, `genre`) AGAINST (? IN BOOLEAN MODE) ORDER BY `uploaded` DESC LIMIT $from, $limit", array($user_id, $match));
        return $track_data;
    }

    static function getTracksCount($user_id)
    {
        return (int) db::query_single_col("SELECT `tracks_count` FROM `r_static_user_vars` WHERE `user_id` = ?", array($user_id));
    }

    static function getTracksDuration($user_id)
    {
        return (int) db::query_single_col("SELECT `tracks_duration` FROM `r_static_user_vars` WHERE `user_id` = ?", array($user_id));
    }

}
