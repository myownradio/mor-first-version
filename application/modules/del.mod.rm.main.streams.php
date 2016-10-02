<?php
$stream_id = application::get('stream_id', NULL, REQ_INT);
foreach(stream::getStreams(user::getCurrentUserId()) as $stream) {
?>
<li data-stream-id="<?= $stream['sid'] ?>" data-name="<?= htmlspecialchars($stream['name'], ENT_QUOTES) ?>" class="track-accept stream <?= (!is_null($stream_id) && ($stream['sid'] == $stream_id)) ? "current" : "" ?>">
    <div title="Listen to this stream" class="rm_fl_right rm_playStream">
        <a target="_blank" href="/listen/<?= strlen($stream['permalink']) > 0 ? $stream['permalink'] : $stream['sid'] ?>#play">listen</a>
    </div>
    <div title="Number of tracks in stream" class="rm_badge rm_fl_right"><?= (new stream($stream['sid']))->getTracksCount() ?></div>
    <a href="/radiomanager/stream?stream_id=<?= $stream['sid'] ?>" title="<?= htmlspecialchars($stream['name'], ENT_QUOTES) ?>">
        <i class="icon-feed"></i><?= htmlspecialchars($stream['name']) ?>
    </a>
</li>
<?php }