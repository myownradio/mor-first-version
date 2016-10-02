<div class="streams">
    <div class="streams_title"><a href="/radiomanager/upload">Upload</a></div>
    <div class="streams_title"><a href="/radiomanager/">Library</a></div>
    
<div class="streams_title">My Streams</div>
<ul class="streams_list">
<?php
$stream_id = application::get('stream_id', NULL, REQ_INT);
foreach(stream::getStreams(user::getCurrentUserId()) as $stream) { 
?>
<li data-id="<?= $stream['sid'] ?>" class="<?= ($stream_id == $stream['sid']) ? 'active ' : '' ?>droppable">
    <a href="/radiomanager/stream?stream_id=<?= $stream['sid'] ?>" title="<?= $stream['name'] ?>">
        <div class="stream_item">
            <img src="/images/radio-default.png" />
            <div class="title"><?= $stream['name'] ?></div>
        </div>
    </a>
</li>
<?php } ?>
</ul>
</div>