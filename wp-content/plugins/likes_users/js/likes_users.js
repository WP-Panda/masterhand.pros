(function($){
    $(document).ready(function(){
        $('.add-like-post').on('click', function(e){
            likes.add(this, 'post')
        });

        $('.add-like-comment').on('click', function(e){
            console.log(e)
            console.log(this)
            likes.add(this, 'comment')
        });
    });

    var likes = {
        blockUi: new AE.Views.BlockUi,
        url_send: '/wp-admin/admin-ajax.php',

        add : function (obj, typeLike) {
            if(!$(obj).hasClass('is-liked')) {
                likes.blockUi.block(obj);
                var id = $(obj).data('id');
                $.post(likes.url_send, 'action=handLike&type=' + typeLike + '&id=' + id, function (result) {
                    var data = likes.parseJsonString(result);
                    if (data.status == 'success') {
                        $(obj).html(data.count);
                        $(obj).addClass('is-liked');
                        if (typeLike == 'comment') {
                            $(obj).removeClass('add-like-comment')
                        } else {
                            $(obj).removeClass('add-like-post')
                        }
                    } else {
                        likes.showError(data.msg ? data.msg : 'Error!');
                    }
                }).fail(likes.failRequest).always(function () {
                    likes.blockUi.unblock();
                })
            }
        },
        failRequest : function(r){
            likes.blockUi.unblock();
            AE.pubsub.trigger('ae:notification', {
                msg: 'Error! Status code ' + r.status + ' ' + r.statusText
                + (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : ''),
                notice_type: 'error'
            });
        },
        showError : function(msg){
            AE.pubsub.trigger('ae:notification', {
                msg: (msg? msg : 'Error!'),
                notice_type: 'error'
            });
        },
        isJsonString : function (str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        },
        parseJsonString : function (str) {
            return this.isJsonString(str) ? JSON.parse(str) : (typeof str === 'object' ? str : {});
        }
    }

})(jQuery);