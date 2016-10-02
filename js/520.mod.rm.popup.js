var timerObj = false;
function showPopupInfo(message, type)
{
    popupDestroy();

    var div = $("<div>")
        .addClass("rm_popup_info");
    if (type === 1)
    {
        div.addClass("error");
    }
    div.html(message);
    div.appendTo('body');
    div.bind('click', function() {
        popupDestroy();
    });

    timerObj = window.setTimeout(function()
    {
        popupDestroy();
    }, 5000);
}

function popupDestroy()
{
    $("div.rm_popup_info").animate({opacity: 0}, 500, function()
    {
        $(this).remove();
    });
    if (timerObj !== false)
    {
        window.clearTimeout(timerObj);
        timerObj = false;
    }
}