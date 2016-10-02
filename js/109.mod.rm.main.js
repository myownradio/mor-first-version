$(document).ready(function(){
    $(".rm_tracks_data").livequery(function(){
        var data = JSON.parse(atob($(this).attr('content')));
        $(this).remove();
        $("#streamTrackTemplate").tmpl(data).appendTo(".rm_tracks_body");
        tracklistNumbering();
        //checkCurrentTrack();
    });
    
});

$("[auto-time]").livequery(function(){
    $(this).text(secondsToHms($(this).attr("auto-time")));
});


function ajaxGetLibraryTracks(replace)
{

    $("body").addClass("ajaxBusy");
    var lastTrack = $(".rm_tracks_item").length;
    $.post("/radiomanager/json/getLibTracklist", { 
        from      : replace || false ? 0 : lastTrack,
        filter    : $("#filterBox").val(),
        authtoken : mor.user_token
    }, function(data){
        if(replace || false === true)
        {
            $("body").addClass("partial");
            crearAllTracks();
        }
        var json = JSON.parse(data);
        if(json.length < 50)
        {
            $("body").removeClass("partial");
        }
        $("#streamTrackTemplate").tmpl(json).appendTo(".rm_tracks_body");
        tracklistNumbering();        
        $("body").removeClass("ajaxBusy");
    });
}

function ajaxGetStreamTracks(replace)
{
    $("body").addClass("ajaxBusy");
    var lastTrack = $(".rm_tracks_item").length;
    $.post("/radiomanager/json/getStreamTracklist", { 
        stream_id : active_stream.stream_id,
        from      : replace || false ? 0 : lastTrack,
        authtoken : mor.user_token
    }, function(data){
        if(replace || false === true)
        {
            $("body").addClass("partial");
            crearAllTracks();
        }        
        var json = JSON.parse(data);
        if(json.length < 50)
        {
            $("body").removeClass("partial");
        }
        $("#streamTrackTemplate").tmpl(json).appendTo(".rm_tracks_body");
        tracklistNumbering();
        updateCurrentTrack(true);
        $("body").removeClass("ajaxBusy");
    });
}

function updateRadioManagerInterface()
{
    updateLibraryCounters();
}

function updateLibraryCounters()
{
    $("#total_tracks_count").text(mor.tracks_count);
    $("#total_tracks_time").text(secondsToHms(mor.tracks_duration));

    $("#info_hm").html(secondsToHandM(mor.tracks_time_limit - mor.tracks_duration));
    $("#total_time_left").text(secondsToHms(mor.tracks_time_limit - mor.tracks_duration));

}
