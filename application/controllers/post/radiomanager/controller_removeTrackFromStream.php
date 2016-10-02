<?php

$unique_id = application::post('unique_id', NULL, 'string');
$stream_id = application::post('stream_id', NULL, 'int');
if (is_string($unique_id) && is_int($stream_id))
{
    echo application::singular('stream', $stream_id)->reloadTracks()->removeTrack($unique_id);
}