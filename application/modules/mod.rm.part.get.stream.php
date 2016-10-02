<?php
if(empty($_MODULE['stream_id']))
{
    return false;
}
$stream = application::singular('stream', $_MODULE['stream_id']); 
?>
<li data-stream-id="<?= $stream->getStreamId() ?>" data-name="<?= htmlspecialchars($stream->getStreamName(), ENT_QUOTES) ?>" class="track-accept stream">
    <div title="Listen to this stream" class="rm_fl_right rm_playStream">
        <a target="_blank" href="/listen/<?= $stream->getStreamId() ?>#play">listen</a>
    </div>
    <div title="Number of tracks in stream" class="rm_badge rm_fl_right"><?= $stream->getTracksCount() ?></div>
    <a href="/radiomanager/stream?stream_id=<?= $stream->getStreamId() ?>" title="<?= htmlspecialchars($stream->getStreamName(), ENT_QUOTES) ?>">        
        <i class="icon-feed"></i><?= htmlspecialchars($stream->getStreamName()) ?>
    </a>
</li>
