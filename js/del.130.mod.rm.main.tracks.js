(function($) {

    $(document).scroll(function()
    {
        if($("body").hasClass("partial"))
        {
            ajaxPageScroll();
        }
    });

    // Hider
 
    
    function ajaxPageScroll()
    {
        var bottom = $(document).height() - $(window).height() - $(window).scrollTop();
        if(bottom < 400)
        {
            if(!$("body").hasClass("ajaxBusy"))
            {
                if($("body").hasClass("library"))
                {
                    ajaxGetLibraryTracks();
                }
                else if($("body").hasClass("stream"))
                {
                    ajaxGetStreamTracks();
                }
            }
        }
    }


    $(".rm_tracks_body.rm_streamview:visible").livequery(function(){
        console.log("Reload");
        $(this).hide().sortable({
            items: ".rm_tracks_item:visible",
            stop: function( event, ui ) {
                var this_element = ui.item.attr("data-unique");
                var this_index = $(ui.item).index();
                var stream_id = active_stream.stream_id;
                $.post("/radiomanager/rearrangeStream", {
                    stream_id : stream_id,
                    target : this_element,
                    index  : this_index + 1,
                    authtoken : mor.user_token
                }, function(data) {
                    console.log(filterAJAXResponce(data));
                    //tracklistNumbering();
                });
                tracklistNumbering();
            }
        }).show();
    }); 
    
    // Counters
    
    $(".rm_library .rm_tracks_item").livequery(null, function() {
    }, function() {
        $(this).removeClass("selected active");
        updateSelectedItems();
    });
    

    
    $(".rm_tracks_item").livequery(function()
    {
        $(this)
                .live('contextmenu', function()
                {
                    if ($(".rm_library .rm_tracks_item.selected").length > 0)
                    {
                        showTrackInTracklistMenu(event);
                    }
                    if ($(".rm_streamview .rm_tracks_item.selected").length > 0)
                    {
                        showTrackInStreamMenu(event);
                    }                    
                    event.preventDefault();
                    return false;
                })
                .live('mouseup', function()
                {
                    if (event.button === 2 && $(this).hasClass("selected"))
                    {
                        return;
                    }

                    var prevClicked = $(".rm_tracks_item.active").index();
                    var ctrlKey = event['ctrlKey'];
                    var shiftKey = event['shiftKey'];

                    if (ctrlKey === false)
                    {
                        $(".rm_tracks_item").removeClass("selected");
                    }

                    $(".rm_tracks_item").removeClass("active");

                    $(this).addClass('active');
                    

                    if (shiftKey === false || prevClicked === -1)
                    {
                        $(this).toggleClass('selected');
                    }
                    else
                    {
                        var newClicked = $(this).index();

                        if (newClicked > prevClicked)
                        {
                            $(".rm_tracks_item").slice(prevClicked, newClicked + 1).addClass('selected');
                        }
                        else
                        {
                            $(".rm_tracks_item").slice(newClicked, prevClicked + 1).addClass('selected');
                        }
                    }

                    updateSelectedItems();
                });

    });
    
    
    $(".rm_library .rm_tracks_item[low-state='1']").livequery(function()
    {
        $(this).draggable({
            cursor: "move",
            cursorAt: {top: 8, left: 8},
            helper: function() {
                if($(this).hasClass("selected") === false)
                {
                    $(".rm_tracks_item").removeClass("selected active");
                    $(this).addClass("selected active");
                }
                var selected_ids = [];
                var selected = $(".rm_tracks_item.selected");
                selected.each(function()
                {
                    selected_ids.push($(this).attr('track-id'));
                });
                var caption = (selected.length > 1) ? (selected.length + " track(s)") : ("<b>" + selected.find("div").eq(2).text() + "</b> - <b>" + selected.find("div").eq(1).text() + "</b>");

                return $("<div>")
                        .attr("track-id", selected_ids.join(","))
                        .addClass("rm_track_drag")
                        .html("Selected " + caption);
            }
        });
    });
    
// Processing

    $(".rm_library .rm_tracks_item[low-state='0']").livequery(null,
            function()
            {
                $("#proc_tracks_count").increment("data-count", 1).text($("#proc_tracks_count").attr("data-count"));
                $(".rm_status_processing_item").removeClass("hidden");
            },
            function()
            {
                $("#proc_tracks_count").increment("data-count", -1).text($("#proc_tracks_count").attr("data-count"));
                if($("#proc_tracks_count").attr("data-count").toInt() < 1)
                {
                    $(".rm_status_processing_item:not(.hidden)").addClass("hidden");
                }
            });




// Click outside the list unselects all
    $("html").bind('click', function(event)
    {
        if ($(event.target).parents().andSelf().filter(".rm_tracks_table, .rm_mouse_menu_wrap, .rm_popup_form_background, .rm_mbox_shader").length === 0)
        {
            unselectAllTracks();
        }
    });

// Hotkeys 
    $(document).bind('keydown', function()
    {
        if (event.ctrlKey && event.keyCode === 65) 
        {
            tracklistSelectAll();
        }
        else if (event.ctrlKey && event.keyCode === 73) 
        {
            tracklistInvert();
        }
        else
        {
            return;
        }

        event.preventDefault();
    });

    $(document).ready(function()
    {

        updateSelectedItems();

    });

})(jQuery);

function showRows()
{
    $(".rm_tracks_item").addClass("i");
    $(".rm_tracks_item.i:not(.filtered)").slice(0, 50).removeClass("i");
    tracklistNumbering();
}
    
// Helper functions
function tracklistSelectAll()
{
    $(".rm_tracks_item[low-state='1']")
            .addClass("selected")
            .removeClass("active");
    $(".rm_tracks_item[low-state='1']:last-child")
            .addClass("active");
    updateSelectedItems();
}

function tracklistInvert()
{
    $(".rm_tracks_item")
            .toggleClass("selected");
    updateSelectedItems();
}

function tracklistNumbering()
{
    $(".rm_tracks_item:not(.filtered)").each(function(i)
    {
        $(this).find("div").eq(1).html(i+1);
        if ( i % 2 === 0) 
        {
            $(this).removeClass("odd");
        }
        else
        {
            $(this).addClass("odd");
        }
    });
}

function unselectAllTracks()
{
    $(".rm_tracks_item")
            .removeClass("selected")
            .removeClass("active");
    updateSelectedItems();
}

function crearAllTracks()
{
    $(".rm_tracks_item").remove();
}