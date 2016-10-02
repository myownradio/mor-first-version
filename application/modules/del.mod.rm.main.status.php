<?php
$profile_tracks = track::getTracksCount(user::getCurrentUserId());
$profile_time = track::getTracksDuration(user::getCurrentUserId());
$profile_time_left = user::userUploadLeft();
?>
<div class="rm_status_wrap">
    <ul class="rm_status_list rm_status_total rm_fl_left">
        <li>Total tracks count <span data-count="0" id="total_tracks_count">0</span></li>
        <li>Total tracks duration <span data-seconds="0" id="total_tracks_time">0:00:00</span></li>
        <li class="rm_status_processing_item hidden">Processing tracks <span data-count="0" id="proc_tracks_count">0</span></li>
    </ul>
    <ul class="rm_status_list rm_status_selected rm_fl_left">
        <li>Selected tracks count <span id="sel_tracks_count">0</span></li>
        <li>Selected tracks duration <span id="sel_tracks_time">0:00:00</span></li>
        <li>
            <!--<input type="button" class="rm_ui_button rm_delete_track" value="Delete" />
            <input type="button" class="rm_ui_button rm_change_track"  value="Edit" />-->
        </li>
    </ul>
    <ul class="rm_status_list rm_fl_right">
        <li>Time left on account <span id="total_time_left"><?= misc::convertuSecondsToTime($profile_time_left) ?></span></li>
    </ul>
</div>