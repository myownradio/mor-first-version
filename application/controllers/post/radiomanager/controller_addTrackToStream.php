<?php

$track_id = application::post('track_id', NULL, REQ_STRING);
$stream_id = application::post('stream_id', NULL, REQ_INT);
if (is_string($track_id) && is_int($stream_id))
{
    echo application::singular('stream', $stream_id)->addNewTrack($track_id);
}
else
{
    echo misc::outputJSON("ERROR_OCCURED_MISSING_PARAMETERS", array());
}