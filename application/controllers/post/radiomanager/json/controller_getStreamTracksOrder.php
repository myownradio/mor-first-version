<?php

$stream_id = application::post("stream_id", null, REQ_STRING);

if(empty($stream_id))
{
    misc::errorJSON("NO_STREAM");
}

if(!application::singular("stream", $stream_id)->exists())
{
    misc::errorJSON("NO_STREAM");
}

if(application::singular("stream", $stream_id)->getOwner() != user::getCurrentUserId())
{
    misc::errorJSON("NO_ACCESS");
}


$order_list = array();
foreach(application::singular("stream", $stream_id)->getTracks() as $track)
{
    $order_list[] = $track['unique_id'];
}

echo json_encode(array(
    'stream_id' => $stream_id,
    'tracks_order' => $order_list
        ));
