$(document).ready(function()
{
    $(".track-accept.stream").livequery(function() {
        $(this).droppable({
            drop: function(event, ui) {
                var stream_id = $(this).attr('data-stream-id');
                var track_id = ui.helper.attr('track-id');
                addTracksToStream(stream_id, track_id);
                $(this).toggleClass('selected', false);
            },
            over: function(event, ui) {
                $(this).toggleClass('selected', true);
            },
            out: function(event, ui) {
                $(this).toggleClass('selected', false);
            },
            accept: ".rm_library .rm_tracks_item",
            tolerance: "pointer"
        });
    });

});