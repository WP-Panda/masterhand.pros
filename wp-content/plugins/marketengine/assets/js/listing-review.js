(function($) {
    $.fn.me_load_review = function(options) {

        var comment_page_counts = parseInt($(this).attr('data-page')),
            action = 'me_load_more_reviews';

        var page = 2;
        var element = $(this);

        $(this).click(function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'get',
                url: me_globals.ajaxurl,
                data: {
                    action: action,
                    page: page,
                    post_id: options.post_id
                },
                beforeSend: function() {
                    // loading
                    page++;
                },
                success: function(res) {
                    if (page > comment_page_counts) {
                        element.parent().remove();
                    }
                    if (res.data) {
                        $('.me-comment-list').append(res.data);
                        $('.result-rating').raty({
                            half: true,
                            readOnly: true,
                            score: function() {
                                return $(this).attr('data-score');
                            }
                        });
                    }
                }
            });
        });
    };
    $('.comment-pagination #read-more-review').me_load_review({
        post_id : $('#read-more-review').attr('data-post-id')
    });
})(jQuery);