<?php

$stream_id = application::get("stream_id", NULL);

$stream = application::singular('stream', $stream_id);

if($stream->exists())
{
    echo application::getModule("page.us.listen", array(), array('stream_id' => $stream->getStreamId()));
}
else
{
    echo application::getModule("error.404");
}
