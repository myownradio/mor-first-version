(function() {

    var radioStatus = false;
    var radioSync = 0;
    var radioPosition = 0;
    var radioMicroSync = 0;
    var playerStarted = 0; 

    $(document).on("ready", function() {
        initRadioPlayer();
    });

    function initRadioPlayer()
    {
        $("#jplayer").jPlayer({
            ready: function(event) {
                if(window.location.hash === '#play')
                {
                    startStream();
                }
            },
            ended: function(event) {
                stopStream();
            },
            error: function(event) {
                errorStream();
            },
            timeupdate: function(event) {
                radioPosition = event.jPlayer.status.currentTime;
                radioMicroSync = (event.timeStamp - playerStarted) - (radioPosition * 1000);
                if(event.jPlayer.status.currentTime > 0 && ! connectEvent)
                {
                    connected();
                }
            },
            progress: function(event) {
            },
            swfPath: "/swf",
            supplied: "mp3",
            solution: "flash,html",
            volume: 1
        });
    }

    function startStream()
    {
        playerStarted = new Date().getTime();
        connectEvent = false;
        radioStatus = true;

        $("#jplayer").jPlayer("setMedia", {
            mp3: "http://myownradio.biz:7777/stream_" + myRadio.stream_id
        }).jPlayer("play");
        playerStarted = new Date().getTime();
        
        updateCurrentTrack();

        $(".rh_playbar").removeClass("waiting");
        $(".pl_status").text("Buffering...");
        $(".play_sw").removeClass("icon-play").addClass("icon-stop");
    }

    function stopStream()
    {
        connectEvent = false;
        radioStatus = false;
        $("#jplayer").jPlayer("stop").jPlayer("clearMedia");
        $(".rh_playbar").addClass("stopped waiting");
        $(".pl_status").text("Stopped");
        $(".play_sw").addClass("icon-play").removeClass("icon-stop");
    }
    
    function errorStream()
    {
        connectEvent = false;
        radioStatus = false;
        $("#jplayer").jPlayer("stop").jPlayer("clearMedia");
        $(".rh_playbar").addClass("stopped waiting");
        $(".pl_status").text("Stream Error");
        $(".play_sw").addClass("icon-play").removeClass("icon-stop");
        
    }
    
    var connectEvent = false;
    function connected()
    {
        connectEvent = true;
        $(".rh_playbar").removeClass("stopped");
        $(".pl_status").text("Now playing");
    }

    var refreshHandle = false;
    var iteratorCount = 0;
    function statusRefresh()
    {
        if(radioStatus === false) 
            return false;
        
        // Update interface
        var realPos = myRadio.position + (radioPosition - radioSync) * 1000;
        
        $(".pl_progress > .pr_bg > .fg").width((100 / myRadio.duration * realPos).toString() + "%");
        $(".tm_fore").text(secondsToHms(parseInt(realPos / 1000)) + " (delay "+parseInt(radioMicroSync)+"ms)");
        $(".tm_back").text(secondsToHms(parseInt((myRadio.duration - realPos) / 1000)));
        
        if (realPos > myRadio.duration || iteratorCount > 40)
        {
            updateCurrentTrack();
        }
        else
        {
            iteratorCount ++;
            refreshHandle = window.setTimeout(function() {
                if (myRadio.stream_status)
                    statusRefresh();
            }, 250);
        }
    }

    function updateCurrentTrack()
    {
        $.post("/streamStatus", {stream_id: myRadio.stream_id, radio_sync: radioMicroSync}, function(data) {
            try {
                iteratorCount = 0;
                var json = JSON.parse(data);
                radioSync = radioPosition;
                myRadio = json;
                if(myRadio.stream_status === 0 && radioStatus)
                {
                    stopStream();
                }
                
                if($(".pl_title").text() !== myRadio.now_playing)
                {
                    $(".pl_title").stop().animate({opacity:0}, 250, function(){
                        $(this).text(myRadio.now_playing)
                                .animate({opacity:1}, 250);
                    });
                }
                statusRefresh();
            }
            catch (e)
            {
                console.log(e);
            }

        });
    }

    $(".pb_border").live("click", function() {
        if (radioStatus)
            stopStream();
        else
            startStream();
    });

    function secondsToHms(sec)
    {
        if (sec < 0)
        {
            return "-";
        }

        var hours = Math.floor(sec / 3600);
        var minutes = Math.floor(sec / 60) % 60;
        var seconds = sec % 60;

        var out = "";

        if(hours > 0)
            out += (hours > 9) ? hours.toString() + ":" : "0" + hours.toString() + ":";
        
        out += (minutes > 9) ? minutes.toString() + ":" : "0" + minutes.toString() + ":";
        out += (seconds > 9) ? seconds.toString() : "0" + seconds.toString();

        return out;
    }


})();