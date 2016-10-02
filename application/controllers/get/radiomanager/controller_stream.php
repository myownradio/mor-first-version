<?php

$stream_id = application::get('stream_id', NULL, REQ_INT);

if(is_null($stream_id))
{
    exit(application::getModule("error.404"));
}

if(!application::singular('stream', $stream_id)->Exists())
{
    exit(application::getModule("error.404"));

}

if(application::singular('stream', $stream_id)->getOwner() != user::getCurrentUserId())
{
    exit(application::getModule("error.404"));
}

echo application::getModule("page.rm.stream", array(), array('stream_id' => $stream_id));
