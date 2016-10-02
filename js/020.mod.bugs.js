$(".dynTop").livequery(function(){
    var w = $(this).width();
    var h = $(this).height();
    $(this).width(w+w%2).height(h+h%2);
});