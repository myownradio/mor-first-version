(function() {
    $(".createStream").live("click", function() {
        createForm("/radiomanager/ajaxCreateStream", {}, 500, 320);
        return false;
    });
    $(".rm_create_stream_genrelist").live("click", function() {
        $(".genreInput").focus();
    });
    $(".genreInput").live("keydown", function() {
        if ($(this).html().length === 0 && event.keyCode === 8)
        {
            $(".rm_create_stream_genrelist > .el").last().remove();
            $(".genreInput").trigger("textchange");
        }
    });
    $(".genreInput").livequery("textchange", function() {
        //$(".genreInput").attr("size", $(".genreInput").val().length);
        if ($(".rm_create_stream_genrelist > .el").length === 0 && $(this).html().length === 0)
        {
            
            $(".placeholder").fadeIn(0);
        }
        else
        {
            $(".placeholder").fadeOut(0);
        }
    }).live("focus", function () {
        $(".rm_create_stream_genrelist").addClass("focus");
    }).live("focusout", function () {
        $(".rm_create_stream_genrelist").removeClass("focus");
    });
    $(".genreInput").live("keypress", function() {
        if ($(this).html().length === 0)
        {
            return true;
        }
        if (event.keyCode === 44)
        {
            if ($(this).html().length > 0) {
                var genre = $(this).html();
                $(this).html("");
                $("<div>").addClass("el").text(genre).after(" ").addClose().insertBefore(".genreInput");
                //$(".rm_create_stream_genrelist").append();
            }
            return false;
        }

    });
    $(".rm_create_stream").live("click", function() {
        if ($(".rm_create_stream_wrap [required]").validate() > 0)
        {
            return;
        }

        var s_name   = $(".rm_create_stream_name").val();
        var s_genres = $(".rm_create_stream_genrelist").serializeGenres();
        var s_info   = $(".rm_create_stream_info").val();
        
        $.post("/radiomanager/createStream", {
              stream_name: s_name,
            stream_genres: s_genres,
              stream_info: s_info
        }, function(data) {
            var json = JSON.parse(data);
            if(json.code === "CREATE_STREAM_SUCCESS")
            {
                formDestroy();
                myMessageBox("Stream created successfully!");
            }
            else
            {
                myMessageBox(data);
            }
            
        });
    });

    $.fn.extend({
        addClose: function() {
            var dst = this;
            $("<img>")
                    .attr("src", "/images/iconClose.png")
                    .addClass("rm_genre_close")
                    .bind("click", function() {
                        $(dst).remove();
                    })
                    .appendTo(dst);
            return this;
        }
    });

})();