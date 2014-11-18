(function($){
    $.fn.limit  = function(options) {
        var defaults = {
        limit: 50,
        id_result: false
        }
        var options = $.extend(defaults,  options);
        return this.each(function() {
            var characters = options.limit;
            if(options.id_result != false)
            {
                $("#"+options.id_result).append(characters+" remaining characters.");
            }
            $(this).keyup(function(){
                if($(this).val().length > characters){
                    $(this).val($(this).val().substr(0, characters));
                }
                if(options.id_result != false)
                {
                    var remaining =  characters - $(this).val().length;
                    $("#"+options.id_result).html(remaining+" remaining characters.");
                }
            });
        });
    };
})(jQuery);