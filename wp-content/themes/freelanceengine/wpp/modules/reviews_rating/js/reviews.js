jQuery(function ($) {
// $(document).ready(function(){
    $(document).ready(function ($) {
        $('.review-star-vote').mouseover(function () {
            var currClass = this.className
            var run = true;
            var items = $(this).parent().find('.review-star-vote');
            $.each(items, function (it, elm) {
                if (run) {
                    if (elm.className == currClass) {
                        run = false;
                    }
                    $(this).addClass('fa-star');
                    $(this).removeClass('fa-star-o');
                } else {
                    $(this).removeClass('fa-star');
                    $(this).addClass('fa-star-o');
                }
            })
        }).mouseout(function () {
            if (!$(this).hasClass('vote-checked')) {
                $(this).removeClass('fa-star');
                $(this).addClass('fa-star-o');
            }
        }).on('click', function () {
            var vote = this.dataset['vote'];
            // $('#review_rating').val(vote);
            $(this).parent().parent().find('.val_review_rating').val(vote);
            var items = $(this).parent().find('.review-star-vote');
            $.each(items, function (it, elm) {
                if (elm.dataset['vote'] <= vote) {
                    $(this).addClass('vote-checked');
                    $(this).addClass('fa-star');
                    $(this).removeClass('fa-star-o');
                } else {
                    $(this).removeClass('vote-checked');
                    $(this).removeClass('fa-star');
                    $(this).addClass('fa-star-o');
                }
            })
        });

        $('.review-select-vote').mouseout(function () {
            var items = $(this).parent().find('.review-star-vote');
            $.each(items, function () {
                if ($(this).hasClass('vote-checked')) {
                    $(this).addClass('fa-star');
                    $(this).removeClass('fa-star-o');
                } else {
                    $(this).removeClass('fa-star');
                    $(this).addClass('fa-star-o');
                }
            })
        });

        $('.review-must-paid').on('click', rwRating.modalPayReview);
        $('.pay-for-show-review').on('click', rwRating.payReview);

    });

    var rwRating = {
        classFieldRequired: 'review-field-required validate-fld-error',
        blockUi: new AE.Views.BlockUi,
        url_send: ae_globals.ajaxURL,
        reviewId: 0,
        send: function ($target) {
            var data = $target.serializeObject() || [];
            //data.project_id = $('#project-id').val();
            $.ajax({
                url: rwRating.url_send,
                type: 'post',
                data: data,
                beforeSend: function () {
                    rwRating.blockUi.block($target);
                },
                success: function (res) {
                    rwRating.blockUi.unblock();
                    if (res.status == 'success') {
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'success'
                        });
                        window.location.reload();
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'error'
                        });
                    }
                },
                error: function (jqXHR, exception) {
                    console.log(jqXHR.responseText);
                }
            });
        },

        modalPayReview: function (event) {
            var $target = event.currentTarget;

            if (rwRating.reviewId != $($target).data('review_id')) {
                rwRating.reviewId = $($target).data('review_id');
                var data = {'action': 'previewPayRw', 'rwId': rwRating.reviewId};
                $.ajax({
                    url: rwRating.url_send,
                    type: 'post',
                    data: data,
                    beforeSend: function () {
                        rwRating.blockUi.block($target);
                    },
                    success: function (res) {
                        rwRating.blockUi.unblock();
                        if (res.status == 'success') {
                            $('#rwTotalPrice').html(res.total);
                            $('#rwCurrency').html(res.currency);
                            $('#modal_show_review').find('input[name="amount"]').val(res.total);
                            $('#modal_show_review').find('input[name="price"]').val(res.total);
                            $('#modal_show_review').find('input[name="currency_code"]').val(res.currency);
                            $('#modal_show_review').find('input[name="review_id"]').val(rwRating.reviewId);

                            $('#modal_show_review').modal('show');

                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                }).fail(rwRating.failRequest);
            } else {
                $('#modal_show_review').modal('show');
            }
        },
        payReview: function (event) {
            var data = {};
            data.action = 'payRw';
            data.rwId = rwRating.reviewId;
            $.ajax({
                url: rwRating.url_send,
                type: 'post',
                data: data,
                beforeSend: function () {
                    rwRating.blockUi.block(event.currentTarget);
                },
                success: function (res) {
                    if (res.status == 'success') {
                        window.location.assign(res.redirect_url);
                    } else {
                        rwRating.blockUi.unblock();
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'error'
                        });
                    }
                }
            }).fail(rwRating.failRequest);

            return false;
        },
        getDataRw: function (num) {
            var pData = {};
            var bodyList = $('#reviews .author-project-list')[0];
            pData['action'] = 'rwPaginate';
            pData['user_id'] = bodyList.dataset.userId;
            pData['rwn'] = num;
            rwRating.blockUi.block($('.pagination-reviews'));
            $.post(rwRating.url_send, pData, function (result) {
                var data = rwRating.parseJsonString(result)
                if (data.status == 'success') {
                    $(bodyList).html(data.list);
                    $('.pagination-reviews').html(data.pagination).after(function () {
                        $('.review-must-paid').on('click', rwRating.modalPayReview);
                        rwRating.reviewId = 0;
                    });
                } else {
                    AE.pubsub.trigger('ae:notification', {
                        msg: (data.msg ? data.msg : 'Error!'),
                        notice_type: 'error'
                    });
                }
            }).fail(rwRating.failRequest).always(function () {
                rwRating.blockUi.unblock();
            })
        },
        failRequest: function (r) {
            rwRating.blockUi.unblock();
            AE.pubsub.trigger('ae:notification', {
                msg: 'Error! Status code ' + r.status + ' ' + r.statusText
                + (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : ''),
                notice_type: 'error'
            });
        },
        reload: function (sec) {
            sec = typeof parseInt(sec) != 'NaN' ? sec : 0;
            setTimeout(function () {
                document.location.reload();
            }, sec * 1000);
        },
        isJsonString: function (str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        },
        parseJsonString: function (str) {
            return this.isJsonString(str) ? JSON.parse(str) : (typeof str === 'object' ? str : {});
        },
    }

})