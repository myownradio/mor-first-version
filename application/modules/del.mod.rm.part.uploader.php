<?php
$hoursLeft = floor(user::userUploadLeft() / 3600000);
$minsLeft = floor(user::userUploadLeft() / 60000) % 60;
?>
<div class="rm_window_header">Upload audio file(s)<div class="rm_window_close_wrap"><img src="/images/closeButton.gif" /></div></div>
<div class="rm_window_body">
    <div style="text-align: center; padding-top: 16px; padding-bottom: 10px;">
        <div class="rm_upload_prompt">
            You have <span id="info_hm"><?= $hoursLeft ?> hours and <?= $minsLeft ?> minutes</span> left for upload.<br>
            Click <b>browse</b> to select files you wish to upload.
            <div class="rm_upload_frame">
                <input type="file" class="rm_input_files" style="display: none;" multiple="multiple" accept="audio/*" />
                <input type="button" class="rm_ui_def_button rm_browse" value="Browse..." />
            </div>
        </div>
        <div class="rm_upload_progress_wrap">
            <div id="title">Uploading file <span id="curr_id">1</span> of <span id="total_id">1</span>...</div>
            <div id="progress_background">
                <div id="progress_handle">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="rm_window_bottom">
    <input type="button" class="rm_ui_def_button rm_close" value="Cancel" />
</div>

