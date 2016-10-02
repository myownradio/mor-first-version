(function($, w, j) {
    
    $(document).on("ready", function () {
        initStatus(0);
    });

    function initStatus(timeout) {
        
        w.setTimeout(function() {

            $.post("http://myownradio.biz/radiomanager/eventListen", {
                        s: mor.last_event,
                authtoken: mor.user_token
            }, function(data)
            {
                    var json = filterAJAXResponce(data);
                    // Code here
                    var eventData = json.data.EVENTS;
                    for (var i in eventData)
                    {
                        var ev = eventData[i];
                        if (ev.event_type === 'LORES_CHANGED')
                        {
                            eventChangeLR(ev.event_target, ev.event_value);
                        }
                        else if (ev.event_type === 'TRACK_INFO_CHANGED')
                        {
                            eventUpdateTrack(ev.event_target);
                        }
                        else if (ev.event_type === 'TRACK_DELETED')
                        {
                            eventDeleteTrack(ev.event_target);
                            mor.tracks_count --;
                        }
                        else if (ev.event_type === 'TRACK_ADDED')
                        {
                            eventAddNewTrack(ev.event_target);
                            mor.tracks_count ++;
                        }
                        else if (ev.event_type === 'STREAM_DELETED')
                        {
                            eventDeleteStream(ev.event_target);
                            mor.streams_count --;
                        }
                        else if (ev.event_type === 'STREAM_ADDED')
                        {
                            eventAddNewStream(ev.event_target);
                            mor.streams_count ++;
                        }
                        else if (ev.event_type === 'STREAM_TRACKS_CHANGED')
                        {
                            eventUpdateStream(ev.event_target);
                        }
                        else if (ev.event_type === 'STREAM_TRACK_ADDED')
                        {
                            //eventUpdateStream(ev.event_target);
                        }
                        else if (ev.event_type === 'STREAM_TRACK_DELETED')
                        {
                            eventDeleteFromStream(ev.event_target, ev.event_value);
                        }
                        else if (ev.event_type === 'STREAM_SET_CURRENT')
                        {
                            eventSetCurrentTrack(ev.event_target, ev.event_value);
                        }
                        else if (ev.event_type === 'STREAM_SORT')
                        {
                            eventSortStream(ev.event_target, ev.event_value);
                        }
                        else if (ev.event_type === 'TOKEN_REMOVE')
                        {
                            if(ev.event_value === mor.user_token)
                            {
                                redirectLogin();
                            }
                        }
                        else if (ev.event_type === 'LIB_DURATION_CHANGED')
                        {
                            mor.tracks_duration = ev.event_value;
                        }
                    }
                    updateRadioManagerInterface();
                    mor.last_event = json.data.LAST_EVENT_ID;
                    initStatus(0);
            })
                    .error(function()
                    {
                        console.log("Status ajax error!");
                        initStatus(1000);
                    });
        }, timeout);
    }



})(jQuery, window, JSON);

/* Track helper functions */
function eventDeleteTrack(track_id)
{
    $(".rm_tracks_item[track-id=\"" + track_id + "\"]").remove();
    tracklistNumbering();
}

function eventChangeLR(track_id, value)
{
    $(".rm_library .rm_tracks_item[track-id=\"" + track_id + "\"]").attr('low-state', value);
}

function eventAddNewTrack(track_id)
{
    if ($(".rm_library .rm_tracks_item[track-id=\"" + track_id + "\"]").length === 0)
    {
        $.post("/radiomanager/api/getTrackItem", {track_id: track_id, authtoken: mor.user_token}, function(data)
        {
            var temp = $(data).filter('.rm_tracks_item').prependTo(".rm_tracks_body.rm_library");
            tracklistNumbering();
        });
    }
}

function eventUpdateTrack(track_id)
{
    if ($(".rm_tracks_item[track-id=\"" + track_id + "\"]").length > 0)
    {
        $.post("/radiomanager/api/getTrackItem", {track_id: track_id, authtoken: mor.user_token}, function(data)
        {
            var elem = $(".rm_tracks_item[track-id=\"" + track_id + "\"]");
            //console.log(elem);
            var selected = elem.hasClass("selected");
            var active = elem.hasClass("active");
            
            elem.replaceWith($(data).filter('.rm_tracks_item'));
            
            selected ? elem.addClass("selected") : null;
            active   ? elem.addClass("active") : null;
            
            tracklistNumbering();
        });
    }
}

/* Stream helper functions */
function eventAddNewStream(stream_id)
{
    if ($(".rm_streamlist > li[data-stream-id=\"" + stream_id + "\"]").length === 0)
    {
        $.post("/radiomanager/api/getStreamItem", {stream_id: stream_id, authtoken: mor.user_token}, function(data)
        {
            $(data).filter('li').appendTo(".rm_streamlist");
        });
    }
}

function eventDeleteStream(stream_id)
{
    $(".rm_streamlist > li[data-stream-id=\"" + stream_id + "\"]").remove();
}

function eventUpdateStream(stream_id)
{
    if ($(".rm_streamlist > li[data-stream-id=\"" + stream_id + "\"]").length > 0)
    {
        $.post("/radiomanager/api/getStreamItem", {stream_id: stream_id, authtoken: mor.user_token}, function(data)
        {
            $(".rm_streamlist > li[data-stream-id=\"" + stream_id + "\"]").html($(data).filter('li').html());
        });
    }
}

// Stream track section
function eventDeleteFromStream(target, value)
{
    $(".rm_streamview .rm_tracks_item[data-unique=\""+value+"\"]").remove();
    tracklistNumbering();
}

function eventSetCurrentTrack(target, value)
{
    if(typeof checkCurrentTrack !== "undefined")
    {
        checkCurrentTrack();
    }
}

function eventAddTrackToStream(target, value)
{
    
}

function eventSortStream(unique, index)
{
    var element = $(".rm_streamview .rm_tracks_item[data-unique=\""+unique+"\"]");
   
    if(element.index() !== index-1)
    {
        var badge = element.appendTo("<div>");
        var e = $(".rm_streamview .rm_tracks_item").eq(index-1);
        badge.insertBefore(e);
        tracklistNumbering();
    }
    
}