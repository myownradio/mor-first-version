(function(){
    $.fn.extend({
        switch: function(args){
            var defs = {
                value: args.val || false,
                   sw: args.sw  || function () {}
            };
            
            $(this).addClass("mor_ui_switch");
            
            if(defs.value)
            {
                $(this).addClass("on");
            }
            
            $("<div>").addClass("rm_ui_switch_handle").appendTo($(this));
            
            $(this).bind("click", function(){
                $(this).toggleClass("on");
                var newValue = $(this).hasClass("on");
                defs.sw(newValue);
            });
        }
    });
})();