$(document).ready(function(){
    $(".stream_sortable").sortable({
        stop: function( event, ui ) {
            $order = $("form.stream_sort").serialize();
            $.post("/radiomanager/rearrangeStream", $order, function(data) {
                console.log(data);
            });
        }
    });

    $(".shuffle_button").live("click", function(){
        $('ul.tracks_list').children().each(function(){
            $(this).insertAfter($('ul.tracks_list').children().eq(parseInt($('ul.tracks_list').children().length / 1 * Math.random())));
        });
        $order = $("form.stream_sort").serialize();
        $.post("/radiomanager/rearrangeStream", $order, function(data) {
            console.log(data);
        });
    });
 
    $(".stream_sortable").disableSelection();
    $(".setCurrent").click(function(){
        var unique = $(this).parents().filter('div.track_item_wrap').attr("data-unique");
        var stream = $(this).parents().filter('div.track_item_wrap').attr("data-stream");
        var new_li = $("div[data-unique=\""+unique+"\"]").parents().filter('li.stream_track');
        $.post("/radiomanager/changeStreamState", {
            'stream_id': stream,
            'new_unique': unique
        }, function(data){
            var json = JSON.parse(data);
            if(json.code === 'SET_SUCCESS') {
                $("li.stream_track").removeClass('active');
                new_li.addClass('active');
            } else {
                alert(data);
            }
        });
    });
    $('.deleteFromStream').click(function(){
        var stream_id = $("input[name='stream_id']").val();
        var track_unique = $(this).parent().attr('data-unique');
        var current_li = $(this).parents().filter('li.stream_track');
        if(confirm("Are you sure?"))
        {
            $.post("/radiomanager/removeTrackFromStream", {
                stream_id : stream_id,
                unique_id : track_unique
            }, function(data) {
                var json = JSON.parse(data);
                if(json.code === 'REMOVE_FROM_STREAM_SUCCESS')
                {
                    if(current_li.hasClass("active")) {
                        current_li.next().addClass("active");
                    }
                    current_li.remove();
                }
                else
                {
                    alert(data);
                }
            });
        }
    });
    $('.deleteFromProfile').click(function(){
        var track_li = $(this).parents().filter('li.profile_track');
        var track_id = track_li.attr('data-track-id')
        if(confirm("Are you sure?"))
        {
            $.post("/radiomanager/removeTrack", {
                track_id : track_id
            }, function(data) {
                if(data === 'DELETE_SUCCESS')
                {
                    track_li.remove();
                }
                else
                {
                    alert(data);
                }
            });
        }
    });
});
