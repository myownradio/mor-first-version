<?php

$stream_id = application::post('stream_id', NULL, REQ_INT);
$target    = application::post('target', NULL, REQ_STRING);
$index    = application::post('index', NULL, REQ_INT);

if (is_int($stream_id))
{
    echo application::singular('stream', $stream_id)->streamReorder($target, $index);
}