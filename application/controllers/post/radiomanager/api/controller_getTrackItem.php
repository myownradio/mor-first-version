<?php

$track_id = application::post("track_id", NULL, REQ_INT);
$type = application::get("type", "html", REQ_STRING);

if(is_null($track_id))
{
    exit();
}

if($type === "html")
{
    echo application::getModule("rm.part.get.track", array(), array('track_id' => $track_id));
}
else if($type === "json")
{
    echo json_encode((new track($track_id))->makeArray());
}