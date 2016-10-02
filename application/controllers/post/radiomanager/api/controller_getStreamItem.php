<?php

$stream_id = application::post("stream_id", NULL, REQ_INT);

if(is_null($stream_id))
{
    exit();
}

echo application::getModule("rm.part.get.stream", array(), array('stream_id' => $stream_id));