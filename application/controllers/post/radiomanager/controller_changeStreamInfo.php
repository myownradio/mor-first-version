<?php

$strm_name   = application::post('stream_name', NULL, REQ_STRING);
$strm_info   = application::post('stream_info', NULL, REQ_STRING);
$strm_genres = application::post('stream_genres', NULL, REQ_STRING);
$stream_id   = application::post('stream_id', NULL, REQ_INT);

if(is_null($strm_name) || is_null($strm_info) || is_null($strm_genres) || is_null($stream_id))
{
    exit("Some parametes missed.");
}

echo application::singular('stream', $stream_id)->changeInfo($strm_name, $strm_info, $strm_genres);
