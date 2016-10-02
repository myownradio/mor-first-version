<?php

$track_id = application::post('track_id', NULL, REQ_STRING);
if(is_string($track_id))
{
    echo track::deleteTrackFile($track_id);
}
