<?php
if(empty($_MODULE['track_id']))
{
    return false;
}
$track = (new track($_MODULE['track_id']))->makeArray(); 
?>
<div class="rm_tracks_item" data-color="<?= $track['color'] ?>" low-state="<?= $track['lores'] ?>" track-id="<?= $track['tid'] ?>" track-duration="<?= floor($track['duration'] / 1000) ?>" track-title="<?= htmlspecialchars($track['title'], ENT_NOQUOTES) ?>" track-artist="<?= htmlspecialchars($track['artist'], ENT_NOQUOTES) ?>" track-album="<?= htmlspecialchars($track['album'], ENT_NOQUOTES) ?>" track-genre="<?= htmlspecialchars($track['genre'], ENT_NOQUOTES) ?>">
    <div class="rm_tracks_cell"><i class="icon-play"></i></div>
    <div class="rm_tracks_cell"><span class="pos"></span><i title="Preview" class="preview_button icon-play"></i></div>
    <div class="rm_tracks_cell" title="<?= htmlspecialchars($track['title'], ENT_QUOTES) ?>">
        <img class="rm_loader_icon" title="Processing..." src="/images/ajax-track-loader.gif" />
        
        <?= htmlspecialchars($track['title']) ?>
    </div>
    <div class="rm_tracks_cell" title="<?= htmlspecialchars($track['artist'], ENT_QUOTES) ?>"><?= htmlspecialchars($track['artist']) ?></div>
    <div class="rm_tracks_cell" title="<?= htmlspecialchars($track['album'], ENT_QUOTES) ?>"><?= htmlspecialchars($track['album']) ?></div>
    <div class="rm_tracks_cell" title="<?= htmlspecialchars($track['genre'], ENT_QUOTES) ?>"><?= htmlspecialchars($track['genre']) ?></div>
    <div class="rm_tracks_cell"><?= misc::trackDuration($track['duration']) ?></div>
    <div class="rm_tracks_cell"><?= htmlspecialchars($track['track_number']) ?></div>
    <input type="hidden" value="/radiomanager/previewAudio?track_id=<?= $track['tid']; ?>" />
</div>

