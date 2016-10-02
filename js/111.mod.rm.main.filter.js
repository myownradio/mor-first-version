(function(){
    var timerHandle = false
    
    $("#filterBox").livequery(function(){
        if($("body").hasClass("library"))
        {
            $(this).bind('textchange', function(){
                if(timerHandle)
                {
                    window.clearTimeout(timerHandle);
                }
                timerHandle = window.setTimeout(function(){
                    timerHandle = false;
                    ajaxGetLibraryTracks(true);
                }, 200);
            });
        }
    });    
})();
