function deleteSelectedTracks()
{
    myQuestionBox("Are you sure want to delete selected tracks from account?", function() {
        var selected_ids = [];
        $(".rm_tracks_item.selected").each(function()
        {
            selected_ids.push($(this).attr('track-id'));
        });
        $.post("/radiomanager/removeTrack", {
            track_id    : selected_ids.join(","),
            authtoken   : mor.user_token
        });
    });

}

function deleteSelectedTracksFromStream()
{
    myQuestionBox("Are you sure want to remove selected tracks from stream?", function() {
        var selected_ids = [];
        var stream_id = active_stream.stream_id;
        $(".rm_tracks_item.selected").each(function()
        {
            selected_ids.push($(this).attr('data-unique'));
        });
        $.post("/radiomanager/removeTrackFromStream", {
            stream_id: stream_id,
            unique_id: selected_ids.join(","),
            authtoken: mor.user_token
        }, function(data) {
            var json = JSON.parse(data);
            if (json.code !== 'REMOVE_FROM_STREAM_SUCCESS')
            {
                alert(data);
            }
        });
    });

}


function addTracksToStream(stream_id, track_id)
{
    $.post("/radiomanager/addTrackToStream", {
        track_id: track_id,
        stream_id: stream_id,
        authtoken: mor.user_token
    }, function(data) {
        try
        {
            var json = JSON.parse(data);
            console.log(json);
        }
        catch (e)
        {
            console.log(e);
        }
    });
}

function addSelectedTracksToStream(stream_id)
{
    var selected_ids = [];
    $(".rm_tracks_item.selected[low-state='1']").each(function()
    {
        selected_ids.push($(this).attr('track-id'));
    });
    addTracksToStream(stream_id, selected_ids.join(","));
}


function callUpdateTimeLeft(timeleft)
{
    $("#total_time_left").html(secondsToHms(timeleft));
}

