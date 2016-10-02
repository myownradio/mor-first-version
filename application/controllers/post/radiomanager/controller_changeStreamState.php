<?php

$new_state = application::post('new_state', NULL, REQ_INT);
$new_offset = application::post('start_offset', NULL, REQ_INT);
$new_unique = application::post('new_unique', NULL, REQ_STRING);
$stream_id = application::post('stream_id', NULL, REQ_INT);

if (is_int($stream_id))
{
    if (is_null($new_unique))
    {
        echo application::singular('stream', $stream_id)->setState($new_state, $new_offset);
    }
    else
    {
        echo application::singular('stream', $stream_id)->setCurrentTrack($new_unique);
    }
}
