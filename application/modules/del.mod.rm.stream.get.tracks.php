<?php 

$stream_id = application::get('stream_id', NULL, REQ_INT);
        
if(application::singular('stream', $stream_id)->getOwner() != user::getCurrentUserId()) 
{
    exit();
}
 

?>
<input type="hidden" name="stream_id" value="<?= application::singular('stream', $stream_id)->getStreamId() ?>" />
<?php 
$current_track = application::singular('stream', $stream_id)->currentPlayingTrack();
$i = 0; 
foreach(application::singular('stream', $stream_id)->getTracks() as $track) 
{ 
$i ++; ?>
<li class="stream_track<?= (!is_null($current_track) && $track['unique_id'] == $current_track->getUnique()) ? " active" : "" ?>">
    <div class="track_item_wrap" data-unique="<?= $track['unique_id'] ?>" data-stream="<?= application::singular('stream', $stream_id)->getStreamId() ?>">
        <div class="deleteFromStream">Remove</div>
        <div class="setCurrent">Play</div>
        <div class="title"><span><?= $track['artist'] ?></span> - <?= $track['title'] ?></div>
    </div>
    <input type="hidden" name="new_order[]" value="<?= $track['unique_id'] ?>" />
</li>
<?php } if($i==0) { ?>
<li>This stream is empty</li>
<?php } ?>
