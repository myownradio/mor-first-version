
var update_interval_max = 10000;
    
var timerHandle = false;

$(document).ready(function(){
    if($("body").hasClass("stream"))
    {
        checkCurrentTrack();
        $(".streamSwitch").livequery(function(){
            $(this).switch({
                val: false,
                 sw: function(status){
                    $.post("/radiomanager/changeStreamState", {
                        stream_id : active_stream.stream_id,
                        new_state : status ? 1 : 0,
                        authtoken : mor.user_token
                    }, function(data){
                        checkCurrentTrack();
                    });
                }
            });
        });
    }
});

var prevPlayed = null;
var nowPlaying = null;

function checkCurrentTrack()
{
    if(timerHandle)
    {
        window.clearTimeout(timerHandle);
    }
    
    $.post("/streamStatus", { 
        stream_id : active_stream.stream_id,
        authtoken : mor.user_token
    }, function(data){
        var json = filterAJAXResponce(data);
        var next;
        if(json.stream_status === 1)
        {
            next = json.time_left > update_interval_max ? update_interval_max : json.time_left;
        }
        else
        {
            next = 5000;
        }
        nowPlaying = json;
        updateCurrentTrack();
        timerHandle = window.setTimeout(function(){
            timerHandle = false;
            checkCurrentTrack();
        }, next);
    });
}

function updateCurrentTrack(forced)
{
    if(nowPlaying === null) return;
    
    if(nowPlaying.stream_status === 1)
    {
        $(".streamSwitch:not(.on)").addClass("on");
    }
    else
    {
        $(".rm_streamview .rm_tracks_item.nowplaying").removeClass("nowplaying");
        $(".streamSwitch.on").removeClass("on");
        $(".rm_status_wrap .ttl").text("[stopped]");
        return;
    }
    
    if((forced || false) || (prevPlayed === null) || (prevPlayed.unique_id !== nowPlaying.unique_id))
    {
        $(".rm_status_wrap .ttl").text(nowPlaying.now_playing).append($("<a>").addClass("sListen").attr({'target': '_blank', 'href': active_stream.stream_link + "#play"}).text("Listen!"));
        $(".rm_streamview .rm_tracks_item.nowplaying").removeClass("nowplaying");
        $(".rm_streamview .rm_tracks_item[data-unique=\""+nowPlaying.unique_id+"\"]").addClass("nowplaying");
        prevPlayed = nowPlaying;
    }
}


