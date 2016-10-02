<?php

$strm_name   = application::post('stream_name', NULL, REQ_STRING);
$strm_info   = application::post('stream_info', "", REQ_STRING);
$strm_genres = application::post('stream_genres', "", REQ_STRING);
$strm_permalink = application::post('stream_perm', "", REQ_STRING);

if(is_null($strm_name))
{
    exit("Some parametes missed.");
}

echo stream::createStream($strm_name, $strm_info, $strm_genres, $strm_permalink);
