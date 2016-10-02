function showTagEditorBox(track_id)
{
    $.post("/radiomanager/api/getTrackItem?type=json", { track_id : track_id, authtoken : mor.user_token }, function (data) 
    {
        try {
            var json = JSON.parse(data);
            var item = $("#tagEditorTemplate").tmpl(json);
                item.find(".rm_mbox_btn_close").bind("click", function () { item.remove(); });
                item.find(".rm_mbox_close_btn").bind("click", function () { item.remove(); });
                item.find(".rm_mbox_btn_save").bind("click", function () { 
                    $.post("/radiomanager/changeTrackInfo", item.find('form').serialize() + "&authtoken=" + mor.user_token, function (data) {
                        try {
                            var json = JSON.parse(data);
                            if(json.code !== "UPDATE_SUCCESS")
                            {
                               myMessageBox(json.code);
                            }
                        }
                        catch(e)
                        {

                        }
                        item.remove();
                    });
                });
            item.appendTo("body");
        }
        catch(e)
        {
            
        }
    });
}