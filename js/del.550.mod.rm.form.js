

function myMessageBox(message, callback) {
    var item = $("#messageBoxTemplate").tmpl({message:message});
    callback = callback || function() {};
    item.find(".rm_mbox_btn_close").bind("click", function () { item.remove(); callback(); });
    item.find(".rm_mbox_close_btn").bind("click", function () { item.remove(); callback(); });
    item.appendTo("body");
}

function myQuestionBox(message, callback) {
    var item = $("#questionBoxTemplate").tmpl({message:message});
    callback = callback || function() {};
    item.find(".rm_mbox_btn_close").bind("click", function () { item.remove(); });
    item.find(".rm_mbox_close_btn").bind("click", function () { item.remove(); });
    item.find(".rm_mbox_btn_action").bind("click", function () { callback(); item.remove();  });
    item.appendTo("body");
}


function formDestroy()
{
    $(".rm_popup_form_background").remove();
}