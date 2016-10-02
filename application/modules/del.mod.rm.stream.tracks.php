<?php

$stream_id = application::get('stream_id', NULL, REQ_INT);
$stream = application::singular("stream", $stream_id);
if($stream->getOwner() != user::getCurrentUserId()) { exit("NO_ACCESS"); }
    
?>
<script type="text/javascript">var active_stream = <?= json_encode(array(
    'stream_id'             => (int) $stream->getStreamId(),
    'tracks_count'          => (int) $stream->getTracksCount(),
    'tracks_duration'       => (int) $stream->getDuration(),
    'stream_link'           => "/listen/" . (strlen($stream->getPermalink()) > 0 ? $stream->getPermalink() : $stream->getStreamId())
)); ?></script>
<div class="rm_tracks_data" content="<?= base64_encode(json_encode($stream->getTracks(0, config::getSetting("json", "tracks_per_query"))))?>"></div>
<div class="rm_tracks_wrap">
    <div class="rm_tracks_table">
        <div class="rm_tracks_head">
            <div class="rm_tracks_cell"></div>
            <div class="rm_tracks_cell">#</div>
            <div class="rm_tracks_cell">Title</div>
            <div class="rm_tracks_cell">Artist</div>
            <div class="rm_tracks_cell">Album</div>
            <div class="rm_tracks_cell">Genre</div>
            <div class="rm_tracks_cell">Duration</div>
            <div class="rm_tracks_cell">Track #</div>
        </div>
        <div class="rm_tracks_body rm_streamview"></div>
    </div>
</div>
