<?php

$track_id = application::get('track_id', NULL, REQ_INT);

if(is_null($track_id))
{
    exit(application::getModule("error.404"));
}

$track_instance = application::singular("track", $track_id);

if(!$track_instance->exists())
{
    exit(application::getModule("error.404"));
}

if($track_instance->getTrackOwner() != user::getCurrentUserId())
{
    exit(application::getModule("error.permission"));
}

if(!file_exists($track_instance->lowQualityFile()))
{
    exit(application::getModule("error.404"));
}

$fh = new fileread($track_instance->lowQualityFile());

header("Content-Type: audio/mpeg");

$rndPos = rand(30, 70) / 100;

//misc::writeDebug("Random: $rndPos");

$fh->seek((int)($fh->size() * $rndPos));

while(!$fh->feof())
{
    $data = $fh->read(4096);
    echo $data;
    flush();
}

unset($fh);