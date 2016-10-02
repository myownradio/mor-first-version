$(document).ready(function()
{
    $(this).bind("click", function() {
        hideTracklistMenu();
    });
});



function showAddToStreamMenu()
{
    var submenu = [];

    $("ul.rm_streamlist > li").each(function()
    {
        (function(sid, name) {
            submenu.push({
                name: '<i class="icon-feed"></i>' + name,
                action: function() {
                    addSelectedTracksToStream(sid);
                }
            });
        })($(this).attr("data-stream-id"), $(this).attr("data-name"));
    });

    return submenu;
}

function showTrackInStreamMenu(e)
{
    var menu = [
        {
            name: $("<i>").addClass("icon-play").get(0).outerHTML + "Play on Radio",
            action: function() {
                var unique = $(".rm_tracks_item.active").attr("data-unique");
                var stream = active_stream.stream_id;
                
                $.post("/radiomanager/changeStreamState", {
                    'stream_id'  : stream,
                    'new_unique' : unique,
                    'authtoken'  : mor.user_token
                }, function(data) {
                    var json = JSON.parse(data);
                    if (json.code !== 'SET_SUCCESS') {
                        myMessageBox(data);
                    }
                    checkCurrentTrack();
                });
            }
        },
        {
            name: $("<i>").addClass("icon-pencil").get(0).outerHTML + "Metadata editor",
            enabled: $(".rm_tracks_item.active").length > 0,
            action: function() {
                var track_id = $(".rm_tracks_item.active").attr("track-id");
                showTagEditorBox(track_id);
            }
        },
        {
            name: $("<i>").addClass("icon-trash").get(0).outerHTML + "Remove from stream",
            action: function() {
                deleteSelectedTracksFromStream();
            }
        }
    ];

    var m = $("<div>")
            .addClass("rm_mouse_menu_wrap")
            .append(arrayToSubmenu(e, menu))
            .bind("click", function() { /* event.stopPropagation(); */
            });


    createMenu(e, m, "body");
}

function showTrackInTracklistMenu(e)
{

    var menu = [
        {
            name: $("<i>").addClass("icon-pencil").get(0).outerHTML + "Metadata editor",
            enabled: $(".rm_tracks_item.active").length > 0,
            action: function() {
                var track_id = $(".rm_tracks_item.active").attr("track-id");
                showTagEditorBox(track_id);
            }
        },
        {
            name: $("<i>").addClass("icon-trash").get(0).outerHTML + "Delete selected track(s)",
            action: function() {
                deleteSelectedTracks();
            }
        },
        {
            name: "Add selection to...",
            enabled: $(".rm_tracks_item.selected[low-state='1']").length > 0,
            submenu: showAddToStreamMenu()
        }
    ];

    var m = $("<div>")
            .addClass("rm_mouse_menu_wrap")
            .append(arrayToSubmenu(e, menu))
            .bind("click", function() { /* event.stopPropagation(); */
            });


    createMenu(e, m, "body");
}

function createMenu(e, m, dst) {

    var pageW = $(document).width();
    var pageH = $(document).height();
    var windH = $(window).height();

    leftSide = (e.pageX < pageW / 2);
    topSide = (e.clientY < windH / 2);

    $("div.rm_mouse_menu_wrap").remove();

    m.appendTo(dst);

    leftSide ? m.css("left", (e.pageX + 4) + "px") : m.css({"left": (e.pageX - 4 - m.get(0).scrollWidth) + "px"});
    topSide ? m.css("top", (e.pageY + 4) + "px") : m.css({"top": (e.pageY - 4 - m.get(0).scrollHeight) + "px"});

    return m;

}

function arrayToSubmenu(e, el)
{
    var pageW = $(document).width();
    var pageH = $(document).height();
    var windH = $(window).height();

    leftSide = (e.pageX < pageW / 2);
    topSide = (e.clientY < windH / 2);

    var m = $("<ul>").addClass("rm_mouse_menu");

    (leftSide === false) ? m.addClass("rm_menu_right") : null;
    (topSide === false) ? m.addClass("rm_menu_bottom") : null;

    el.forEach(function(item, i) {
        m.append(arrayToItem(e, item));
    });

    return m;
}

function arrayToItem(e, el)
{
    var subArrow = $("<i>").addClass("icon-arrow-right");
    var item = $("<li>");
    var span = $("<span>").html(el.name).addClass("rm_mouse_menu_title");

    item.addClass("rm_mouse_menu_item");

    if (el.enabled === false)
    {
        item.addClass("rm_mouse_menu_disabled");
        item.append(span);
        return item;
    }

    if (el.submenu !== undefined)
    {
        span.append(subArrow);
    }

    item.append(span);

    if (el.submenu !== undefined)
    {
        item.append(arrayToSubmenu(e, el.submenu));
        return item;
    }

    if (el.action !== undefined)
    {
        item.bind('click', el.action);
    }
    return item;
}

function hideTracklistMenu()
{
    $("div.rm_mouse_menu_wrap")
            .remove();
}