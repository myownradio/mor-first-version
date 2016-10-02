$(document).ready(function(){
    updateLibraryCounters();
});

function updateSelectedItems()
{

        var select = $(".rm_tracks_item.selected");

    
    var selCount = select.length;
    var selTime = 0;
    
    select.each(function()
    {
        selTime += parseInt($(this).attr('track-duration'));
    });

    $("#sel_tracks_count").text(selCount);
    $("#sel_tracks_time").text(secondsToHms(selTime));
    
    if(selCount > 0)
    {
        $(".rm_status_wrap").addClass("selected");
    }
    else
    {
        $(".rm_status_wrap").removeClass("selected");
    }

}

