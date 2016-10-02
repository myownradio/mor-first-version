<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$stream_id = application::get('stream_id', NULL, REQ_INT);

if(is_null($stream_id))
{
    header("HTTP/1.1 400 Bad Request");
    exit("HTTP/1.1 400 Bad Request");
}

if(!application::singular('stream', $stream_id)->Exists())
{
    header("HTTP/1.1 400 Bad Request");
    exit("HTTP/1.1 400 Bad Request");
}

header("Content-Type: application/octet-stream; charset=utf-8");
header(sprintf("Content-Disposition: attachment; filename=\"stream_%d_%s.m3u\"", 
        application::singular('stream', $stream_id)->getStreamId(),
        application::singular('stream', $stream_id)->getStreamName()
        ));
?>
#EXTM3U
#EXTINF:-1,<?= application::singular('stream', $stream_id)->getStreamName() ?>

http://myownradio.biz:7777/stream_<?= $stream_id ?>
