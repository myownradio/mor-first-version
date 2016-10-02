<?php

$stream_id   = application::post('stream_id',  NULL, REQ_INT);
$stream_sync = application::post('radio_sync', 0, REQ_INT);

if(is_null($stream_id))
{
    misc::errorJSON("BAD_REQUEST");
}

if(!application::singular('stream', $stream_id)->Exists())
{
    misc::errorJSON("BAD_REQUEST");
}

echo json_encode(application::singular('stream', $stream_id)->getStreamStatus($stream_sync));
