(function(){
    $(window).ready(function(){
        $("#jplayer").jPlayer({
            ready: function(event) {
            },
            ended: function(event) {
                $("#jplayer").jPlayer("clearMedia");
            },
            error: function(event) {
                $("#jplayer").jPlayer("clearMedia");
            },
            timeupdate: function(event) {
            },
            progress: function(event) {
            },
            swfPath: "/swf",
            supplied: "mp3",
            solution: "flash,html",
            volume: 1
        });
    });
    
    $('.rm_tracks_item[low-state="1"] .rm_tracks_cell:nth-child(2)').live("mouseenter", function(event){
        stopPlayer();
        startPlayer($(this).parents(".rm_tracks_item").find("input").val());
    }).live("mouseleave", function(event){
        stopPlayer();
    });
    
    function startPlayer(file)
    {
        $("#jplayer").jPlayer("setMedia", {mp3:file}).jPlayer("play");
    }
    
    function stopPlayer()
    {
        $("#jplayer").jPlayer("stop").jPlayer("clearMedia");
    }
    
})();