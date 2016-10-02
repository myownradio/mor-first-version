<?php

class stream
{

    private $stream_object = NULL,
            $stream_tracks = NULL,
            $stream_stats = NULL;

    function __construct($stream_id)
    {
        $this->stream_object = db::query_single_row("SELECT * FROM `r_streams` WHERE `sid` = :sid OR `permalink` = :sid", array('sid' => $stream_id));
    }
    
    function getStreamStatus($sync = 0)
    {
        $currentTrack = $this->getState() ? $this->currentPlayingTrack($sync) : null;
        $item = array(
            'stream_id' => $this->getStreamId(),
            'stream_status' => $this->getState()
        );
        
        if($currentTrack)
        {
            $nextTrack = $this->getTrackAfter($currentTrack);
            $item = array_merge($item, array(
                  'unique_id' => $currentTrack->getUnique(),
                'now_playing' => $currentTrack->getTrackCaption(),
                'next_track'  => $nextTrack->getTrackCaption(),
                   'duration' => $currentTrack->getTrackDuration(),
                   'position' => $currentTrack->getTrackCursor(),
                  'time_left' => $currentTrack->getTrackDuration() - $currentTrack->getTrackCursor(),
            ));
        }
        
        return $item;
    }

    function getTrackAfter($track)
    {
        $current = $track->getUnique();
        $next_will_be = false;
        foreach($this->getStreamTracks() as $track_item)
        {
            if($next_will_be)
            {
                return new otrack($track_item);
            }
            if($track_item['unique_id'] === $current)
            {
                $next_will_be = true;
            }
        }
        return new otrack($this->getStreamTracks()[0]);
    }
    
    private function getStreamTracks($from = NULL, $limit = NULL)
    {
        if (is_null($this->stream_tracks))
        {
            $this->reloadTracks();
        }
        if($from === NULL && $limit === NULL)
        {
            return $this->stream_tracks;
        }
        else
        {
            return array_slice($this->stream_tracks, $from, $limit);
        }
        
    }

    private function getStreamStats()
    {
        if (is_null($this->stream_stats))
        {
            $this->reloadStats();
        }
        return $this->stream_stats;
    }

    function reloadTracks()
    {
        $tracks = db::query("SELECT a.*, b.`unique_id`, b.`t_order` FROM `r_tracks` a, `r_link` b WHERE a.`tid` = b.`track_id` AND b.`stream_id` = ? AND a.`lores` = 1 ORDER BY b.`t_order`", array($this->stream_object['sid']));
        $this->stream_tracks = array();
        $time_offset = 0;
        foreach ($tracks as $track)
        {
            $track['offset'] = $time_offset;
            $time_offset += $track['duration'];
            $this->stream_tracks[] = $track;
        }
        return $this;
    }

    function reloadStats()
    {
        $this->stream_stats = db::query_single_row("SELECT * FROM `r_static_stream_vars` WHERE `stream_id` = ?", array($this->getStreamId()));
        return $this;
    }

    function Exists()
    {
        return is_array($this->stream_object);
    }

    function getDuration()
    {
        return $this->getStreamStats()['tracks_duration'];
    }

    function getRealDuration()
    {
        $duration = 0;
        foreach($this->getStreamTracks() as $track)
        {
            $duration += (int) $track['duration'];
        }
        return $duration;
    }
    
    function getOwner()
    {
        return (int) $this->stream_object['uid'];
    }
    function getPermalink()
    {
        return $this->stream_object['permalink'];
    }
    
    function getTracks($from = NULL, $limit = NULL)
    {
        return $this->getStreamTracks($from, $limit);
    }

    function getTracksCount()
    {
        return (int) $this->getStreamStats()['tracks_count'];
    }

    function getStreamId()
    {
        return (int) $this->stream_object['sid'];
    }

    function getState()
    {
        return (int) $this->stream_object['status'];
    }

    function getStreamName()
    {
        return $this->stream_object['name'];
    }

    function getStreamInfo()
    {
        return $this->stream_object['info'];
    }
    
    function currentPlayingTime($realtime = false, $sync = 0)
    {
        if ($this->getState() == 0 || $this->getDuration() == 0)
        {
            return NULL;
        }

        $stream = $this->stream_object;
        return (application::getMicroTime($realtime) - $stream['started'] + $stream['started_from'] - $sync) % $this->getDuration();
    }

    function currentPlayingTrack($realtime = false, $sync = 0)
    {
        if ($this->getState() == 0)
        {
            return NULL;
        }
        $current_time = $this->currentPlayingTime($realtime, $sync);
        foreach ($this->getStreamTracks() as $track)
        {
            if (($current_time >= $track['offset']) && ($current_time <= ($track['offset'] + $track['duration'])))
            {
                $track['cursor'] = $current_time - $track['offset'];
                return new otrack($track);
            }
        }
    }

    function playingTrackAfter($unique_id)
    {
        if ($this->getState() == 0)
        {
            return NULL;
        }
        $next_track = false;
        foreach ($this->getStreamTracks() as $track)
        {
            if ($next_track == true)
            {
                $track['cursor'] = 0;
                return new otrack($track);
            }
            if ($track['unique_id'] == $unique_id)
            {
                $next_track = true;
            }
        }
        $track = $this->getStreamTracks()[0];
        $track['cursor'] = 0;
        return new otrack($track);
    }

    function setCurrentTrack($unique_id)
    {

        if ( ! $this->Exists())
        {
            return misc::outputJSON("SET_ERROR_NO_STREAM");
        }

        if ($this->stream_object['uid'] != user::getCurrentUserId())
        {
            return misc::outputJSON("SET_ERROR_NOT_STREAM_OWNER");
        }

        foreach ($this->getStreamTracks() as $track)
        {
            if ($unique_id == $track['unique_id'])
            {
                $new_offset = $track['offset'];
            }
        }

        if ( ! isset($new_offset))
        {
            return misc::outputJSON("SET_ERROR_NO_TRACK");
        }

        $change_state_time = application::getMicroTime();

        db::query_update("UPDATE `r_streams` SET `status` = 1, `started` = ?, `started_from` = ? WHERE `sid` = ?", array($change_state_time, $new_offset, $this->getStreamId()));
        db::query_update("INSERT INTO `m_events_log` SET `user_id` = ?, `event_type` = 'STREAM_SET_CURRENT', `event_target` = ?, `event_value` = ?", array(
            user::getCurrentUserId(), $this->getStreamId(), $unique_id
        ));
        myRedis::set(sprintf("myownradio.biz:state_changed:stream_%d", $this->getStreamId()), $change_state_time);

        return misc::outputJSON("SET_SUCCESS");
    }

    function setState($state, $offset = 0, $restart = true)
    {
        if ( ! $this->Exists())
        {
            return misc::outputJSON("CHANGE_STATE_ERROR_NO_STREAM");
        }

        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("CHANGE_STATE_ERROR_NOT_STREAM_OWNER");
        }

        $change_state_time = application::getMicroTime();

        if ($state == 1)
        {
            $responce = db::query_update("UPDATE `r_streams` SET `status` = 1, `started` = ?, `started_from` = ? WHERE `sid` = ?", array($change_state_time, $offset, $this->getStreamId()));
        }
        else
        {
            $responce = db::query_update("UPDATE `r_streams` SET `status` = 0, `started` = 0, `started_from` = 0 WHERE `sid` = ?", array($this->getStreamId()));
        }

        if ($restart == true)
        {
            myRedis::set(sprintf("myownradio.biz:state_changed:stream_%d", $this->getStreamId()), $change_state_time);
        }

        return misc::outputJSON("CHANGE_STATE_SUCCESS");
    }

    function modifyCurrentTrackOffset()
    {
        if ($this->getState() == 0)
        {
            return false;
        }

        $current_track = $this->currentPlayingTrack();
        $track_found = false;

        if ( ! is_null($current_track) )
        {
            misc::writeDebug("Now playing: " . $current_track->getTrackCaption());
            $prevUnique = $current_track->getUnique();
            $prevCursor = $current_track->getTrackCursor();

            foreach ($this->reloadTracks()->getStreamTracks() as $track)
            {
                if ($track['unique_id'] == $prevUnique)
                {
                    $this->setState(1, $track['offset'] + $prevCursor, false);
                    $track_found = true;
                    break;
                }
            }
        }
        else
        {
            misc::writeDebug("Synchronizing: Nothing played.");
        }
        if ($track_found == false)
        {
            foreach ($this->getStreamTracks() as $track)
            {
                if ($track['t_order'] == $current_track->getTrackOrder())
                {
                    misc::writeDebug("New playing: {$track['artist']} - {$track['title']}");
                    $this->setCurrentTrack($track['unique_id']);
                    return true;
                }
            }
            // Means that current playing track is removed
            $this->setState(1, 0, true);
        }
    }

    function removeTrack($unique_ids)
    {
        // Check stream existence
        if ( ! $this->Exists() )
        {
            return misc::outputJSON("REMOVE_FROM_STREAM_ERROR_NO_STREAM");
        }

        // Check stream permission
        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("REMOVE_FROM_STREAM_ERROR_NOT_STREAM_OWNER");
        }

        
        $responce = db::query_update("DELETE FROM `r_link` WHERE FIND_IN_SET(`unique_id`, ?) AND `stream_id` = ?", array($unique_ids, $this->stream_object['sid']));
        if ($responce > 0)
        {
            $this->streamOptimize();
            $this->modifyCurrentTrackOffset();
            return misc::outputJSON("REMOVE_FROM_STREAM_SUCCESS");
        }
        else
        {
            return misc::outputJSON("REMOVE_FROM_STREAM_ERROR_NOT_IN_DB");
        }
    }

    function addNewTrack($track_id)
    {
        // Check stream existence
        if ( ! $this->Exists())
        {
            return misc::outputJSON("ADD_TO_STREAM_ERROR_NO_STREAM");
        }

        // Check stream permission
        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("ADD_TO_STREAM_ERROR_NOT_STREAM_OWNER");
        }

        $this->getStreamTracks();

        $track_last = $this->getStreamStats()['tracks_count'];
        $addedTracks = 0;

        foreach (explode(",", $track_id) as $el)
        {

            $track_test = new track($el);
            // Check track existence
            if ( ! $track_test->Exists())
            {
                continue;
            }
            // Check track lores
            if ( ! $track_test->hasLowResolution())
            {
                continue;
            }
            // Check track permission
            if ($track_test->getTrackOwner() != user::getCurrentUserId())
            {
                continue;
            }

            $track_unique = $this->genUniqueId();
            $track_last ++;
            $addedTracks ++;

            db::query_update("INSERT INTO `r_link` VALUES (?, ?, ?, ?)", array(
                $this->getStreamId(),
                $el,
                $track_last,
                $track_unique
            ));
        }

        $this->modifyCurrentTrackOffset();
        return misc::outputJSON("ADD_TO_STREAM_SUCCESS");
    }

    function streamReorder($target, $index)
    {
        // Check stream existence
        if ( ! $this->Exists())
        {
            return misc::outputJSON("REARRANGE_STREAM_ERROR_NO_STREAM");
        }

        // Check stream permission
        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("REARRANGE_STREAM_ERROR_NOT_STREAM_OWNER");
        }
        
        $this->reloadTracks();

        $responce = db::query_update("CALL NEW_STREAM_SORT(?, ?, ?)", array($this->getStreamId(), $target, $index));
        if ($responce > 0)
        {
            db::query_update("INSERT INTO `m_events_log` SET `user_id` = ?, `event_type` = 'STREAM_SORT', `event_target` = ?, `event_value` = ?", array(
                user::getCurrentUserId(), $target, $index
            ));
            $this->modifyCurrentTrackOffset();
            return misc::outputJSON("REARRANGE_STREAM_SUCCESS");
        }
        else
        {
            return misc::outputJSON("REARRANGE_NOT_CHANGED");
        }
    }

    function changeInfo($name, $info, $genres)
    {
        if ( ! $this->Exists())
        {
            return misc::outputJSON("CHANGE_INFO_ERROR_STREAM_NOT_EXISTS");
        }

        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("CHANGE_INFO_ERROR_UNAUTHORIZED");
        }

        $result = db::query_update("UPDATE `r_streams` SET `name` = ?, `info` = ?, `genres` = ? WHERE `sid` = ?", array($name, $info, $genres, $this->getStreamId()));

        if ($result > 0)
        {
            return misc::outputJSON("CHANGE_INFO_SUCCESS");
        }
        else
        {
            return misc::outputJSON("CHANGE_INFO_UNCHANGED");
        }
    }

    function selfDelete()
    {
        if ( ! $this->Exists())
        {
            return misc::outputJSON("STREAM_DELETE_ERROR_NO_STREAM");
        }

        if ($this->getOwner() != user::getCurrentUserId())
        {
            return misc::outputJSON("STREAM_DELETE_ERROR_UNAUTHORIZED");
        }

        db::query_update("DELETE FROM `r_streams` WHERE `sid` = ?", array($this->getStreamId()));

        return misc::outputJSON("STREAM_DELETE_SUCCESS");
    }

    function listenersCount()
    {
        return (int) db::query_single_col("SELECT `listeners_count` FROM `r_static_listeners_count` WHERE `stream_id` = ?", array($this->getStreamId()));
    }

    static function createStream($name, $info = "", $genres = "", $permalink = "")
    {
        if (user::getCurrentUserId() == 0)
        {
            return misc::outputJSON('CREATE_STREAM_ERROR_UNAUTHORIZED');
        }

        if(db::query_single_col("SELECT COUNT(*) FROM `r_streams` WHERE `permalink` = ?", array($permalink)) > 0 && $permalink != "")
        {
            return misc::outputJSON('CREATE_STREAM_ERROR_PERM_USED');
        }
        
        $query = "INSERT INTO `r_streams` (`uid`, `name`, `info`, `genres`, `permalink`) VALUES (?, ?, ?, ?, ?)";
        $result = db::query($query, array(user::getCurrentUserId(), $name, $info, $genres, $permalink));
        if ($result > 0)
        {
            return misc::outputJSON('CREATE_STREAM_SUCCESS');
        }
        else
        {
            return misc::outputJSON('CREATE_STREAM_ERROR');
        }
    }

    static function getStreams($user_id)
    {
        return db::query("SELECT * FROM `r_streams` WHERE `uid` = ?", array($user_id));
    }

    static function getStreamsSimple($user_id)
    {
        $result = array();
        $data = db::query("SELECT * FROM `r_streams` WHERE `uid` = ?", array($user_id));
        foreach($data as $row)
        {
            $result[$row['sid']] = $row['name'];
        }
        return $result;
    }

    static function getStreamsCount($user_id)
    {
        return (int) db::query_single_col("SELECT COUNT(*) FROM `r_streams` WHERE `uid` = ?", array($user_id));
    }
    
    static function streamList()
    {
        return db::query("SELECT * FROM `r_streams` WHERE `status` = 1");
    }

    private function streamOptimize()
    {
        db::query_update("call p_optimize_stream(?)", array($this->getStreamId()));
    }

    private function genUniqueId()
    {
        do
        {
            $generated = misc::generateId();
        }
        while (db::query_single_col("SELECT COUNT(*) FROM `r_link` WHERE `unique_id` = ?", array($generated)) > 0);
        return $generated;
    }

}
