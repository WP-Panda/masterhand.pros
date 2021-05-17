(function($, Views,Models) {
    Views.FieldForm = Views.Modal_Box.extend({    	        
    	initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            var date = $('.ae-date-picker');
            date.each(function(){
                $('input[name='+$(this).attr('name')+']').datepicker({
                    appendTo : '#'+$(this).attr('name'),
                    // format : ae_globals.date_format,
                }).data('datepicker');
            });

        },
    });
    $(document).ready(function() {
        new Views.FieldForm();
    });
    /*
    // Not Release
    $(document).ready(function() {
        new Views.FieldForm();
        var model =  new Models.Post();
        var $i = 1;
        $('.ae_gallery_container').each(function(){

            var upID = $(this).attr('data-upload-id'),
                name = $(this).attr('data-name'),
                carousel = new Views.Carousel({
                    el: $(this),
                    model: model,
                    name_item: name,
                    uploaderID : upID,
                    featured_image: 'featured_image',
                    template : '#ae_carousel_template',
                });
                console.log(upID);
        });
        
    });
    */
})(jQuery, AE.Views,AE.Models);
