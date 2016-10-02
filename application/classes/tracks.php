<?php

class tracks
{
    

    static function changeInfo($track_id, $artist, $title, $genre)
    {
        $track = db::query_single_row("SELECT * FROM `r_tracks` WHERE `tid` = ?", array($track_id));

        if (is_null($track))
        {
            return 'CHANGE_INFO_ERROR_NO_TRACK';
        }

        if ($track['uid'] != user::getCurrentUserId())
        {
            return 'CHANGE_INFO_ERROR_NO_PERMISSION';
        }

        $result = db::query_update("UPDATE `r_tracks` SET `artist` = ?, `title` = ?, `genre` = ? WHERE `tid` = ?", array($artist, $title, $genre, $track_id));
        if ($result == 1)
        {
            return 'CHANGE_INFO_SUCCESS';
        }
        else
        {
            return 'CHANGE_INFO_UNCHANGED';
        }
    }

    static function Remove($track_id)
    {
        if (is_null($track_id))
        {
            return 'REMOVE_ERROR_NO_ID';
        }

        $track = db::query_single_row("SELECT * FROM `r_tracks` WHERE `tid` = ? LIMIT 1", array($track_id));

        if (is_null($track))
        {
            return 'REMOVE_ERROR_NO_TRACK';
        }

        if ($track['uid'] != user::getCurrentUserId())
        {
            return 'REMOVE_ERROR_NO_PERMISSION';
        }

        // Delete physical file
        $file = self::getTrackFileName($track);
        $lores = self::getLoresFileName($track);

        if (file_exists($file))
        {
            unlink($file);
        }
        if (file_exists($lores))
        {
            unlink($lores);
        }

        // Delete logical file
        $res = db::query_update("DELETE FROM `r_tracks` WHERE `tid` = ?", array($track_id));

        if ($res == 1)
        {
            return 'REMOVE_TRACK_OK';
        }
        else
        {
            return 'ERROR_UNEXPECTED';
        }
    }

    static function AudioUpload()
    {
        if ( user::getCurrentUserId() == 0 )
        {
            return 'UPLOAD_ERROR_NO_PERMISSION';
        }
        
        if ( !isset($_FILES['file']) || $_FILES['file']['error'] != 0 )
        {
            return 'UPLOAD_ERROR_NO_FILE';
        }

        if ( array_search($_FILES['file']['type'], config::getSetting('upload', 'supported_audio')) == -1 )
        {
            return 'UPLOAD_ERROR_UNSUPPORTED';
        }

        $audio_tags = misc::get_audio_tags($_FILES['file']['tmp_name']);

        if (empty($audio_tags['DURATION']))
        {
            return 'UPLOAD_ERROR_CORRUPTED_AUDIO';
        }

        db::query_update("INSERT INTO `r_tracks` VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, 0)", array($uid, $_FILES['file']['name'], !empty($audio_tags['PERFORMER']) ? $audio_tags['PERFORMER'] : "Unknown Artist", !empty($audio_tags['TITLE']) ? $audio_tags['TITLE'] : "Unknown Title", !empty($audio_tags['GENRE']) ? $audio_tags['GENRE'] : "Unknown Genre", $audio_tags['DURATION']));
        $lastId = db::lastInsertId();

        if ( ! $lastId )
        {
            return 'UPLOAD_ERROR_DATABASE';
        }

        self::createPath($uid);
        move_uploaded_file($tempfile, self::getFilePath($uid, $lastId, $basename));

        return 'UPLOAD_OK';
    }

    /* Other methods */

    static function getAll()
    {
        return db::query("SELECT * FROM `r_tracks` WHERE 1");
    }

    static function getByUser($id)
    {
        return db::query("SELECT * FROM `r_tracks` WHERE `uid` = ?", array($id));
    }

    static function Get($id)
    {
        return db::query_single_row("SELECT * FROM `r_tracks` WHERE `tid` = ? LIMIT 1", array($id));
    }

    static function getTrackFileName($track)
    {
        return self::getFilePath($track['uid'], $track['tid'], $track['filename']);
    }

    static function getLoresFileName($track)
    {
        return self::getLoresPath($track['uid'], $track['tid']);
    }

    private static function getFilePath($uid, $tid, $filename)
    {
        return sprintf("%s/ui_%d/a_%03d_%s", config::getSetting("content", "content_folder"), $uid, $tid, $filename);
    }

    private static function getLoresPath($uid, $tid)
    {
        return sprintf("%s/ui_%d/lores_%03d.mp3", config::getSetting("content", "content_folder"), $uid, $tid);
    }

    private static function createPath($user_id)
    {
        $new_path = sprintf("%s/ui_%d", config::getSetting("content", "content_folder"), $user_id);
        if (!file_exists($new_path))
        {
            mkdir($new_path, NEW_DIR_RIGHTS, true);
        }
    }

    static function getSelectionLength($tracks)
    {
        $time_count = 0;
        foreach ($tracks as $track)
        {
            $time_count += $track['duration'];
        }
        return $time_count;
    }

    static function getTimeMarkedTracks($tracks)
    {
        $time_start = 0;
        foreach ($tracks as &$track)
        {
            $track['offset'] = $time_start;
            $time_start += $track['duration'];
        }
        return $tracks;
    }

    static function getTrackAtPosition($pos, $tracks)
    {
        $id = 0;
        foreach ($tracks as $track)
        {
            $id ++;
            if ($track['offset'] < $pos && $track['offset'] + $track['duration'] > $pos)
            {
                return array(
                    'id' => $track['tid'],
                    'time' => $pos - $track['offset'],
                    'title' => $track['artist'] . ' - ' . $track['title'],
                    'offset' => $track['offset'],
                    'position' => $id
                );
            }
        }
        return null;
    }

}
