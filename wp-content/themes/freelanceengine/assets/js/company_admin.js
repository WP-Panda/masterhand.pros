// ajax for getting states
jQuery(function($) {
    $('body').on('change', '.countries', function() {
        var countryid = $(this).val();
        if(countryid != '') {
            var data = {
                'action': 'get_states_by_ajax',
                'country': countryid,
                'security': blog.security
            }

            $.post(blog.ajaxurl, data, function(response) {
                $('.load-state').html(response);
                $('.load-city').html(
                    '<option selected="selected" class="standart">' + 'Select city' + '</option>'
                )
            });
        }
    });
});

// ajax for getting cities
jQuery(function($) {
    $('body').on('change', '.states', function() {
        var state_id = $(this).val();
        if(state_id != '') {
            var data = {
                'action': 'get_states_by_ajax',
                'state': state_id,
                'security': blog.security
            }

            $.post(blog.ajaxurl, data, function(response) {
                $('.load-city').html(response);
            });
        }
    });
});

// ajax for getting subcategories
jQuery(function($) {
    $('body').on('change', '.categories', function() {
        var category_name = $(this).val();
        if(category_name != '') {
            var data = {
                'action': 'get_states_by_ajax',
                'category_name': category_name,
                'security': blog.security
            }

            $.post(blog.ajaxurl, data, function(response) {
                $('.load-sub').html(response);
                $('.select2-selection__rendered').empty()

            });
        }
    });
});

jQuery(document).ready(function($) {
   const category_slug = $('._category').val();

    $('.categories option:first-child').attr('value', category_slug)
});

jQuery(document).ready(function() {
    jQuery('.js-example-basic-multiple').select2();
})