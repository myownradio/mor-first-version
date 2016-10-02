<?php

$stream_id = application::post('stream_id', NULL, REQ_INT);

if(!is_null($stream_id))
{
    echo application::singular('stream', $stream_id)->selfDelete();
}