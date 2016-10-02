
$("#completeForm").livequery(function() {
    $(this).bind("submit", function() {
        var args = $(this).serialize();
        $.post("", args, function(data) {
            try
            {
                var json = JSON.parse(data);
            }
            catch (e)
            {
                return false;
            }

            if (json.code === "REG_COMPLETE")
            {
                myMessageBox(langMessages.user[json.code], function(){ window.location = "/login"; });
            }
            else
            {
                myMessageBox(langMessages.user[json.code]);
            }
        });
        return false;
    });
});

