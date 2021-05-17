(function ($, Views) {
    jQuery(document).ready(function ($) {
        AE.Views.PayCodeProject = Backbone.View.extend({
            el: 'body.single',
            events: {
                'submit form.form-send-payment-code': 'getTransfer',
                'click .paycode.open-modal-review': 'openReview',
                'click .send-payment-code': 'emailNotice'
            },
            initialize: function () {
                var view = this;
                $("form.form-send-payment-code").validate({
                    ignore: "",
                    rules: {
                        code: "required"
                    }
                });
            },
            emailNotice: function (event) {
                event.preventDefault();
                var $target = $(event.currentTarget), data = {};
                data.action = 'payCodeNotice';
                data.project_id = $target.data('pid');
                $.ajax({
                    url: payCode.url_send,
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        payCode.blockUi.block($target);
                    },
                    success: function (result) {
                        payCode.blockUi.unblock();
                        var res = payCode.parseJsonString(result);
                        if (res.status == 'success') {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            getTransfer: function (event) {
                var view = this;
                event.preventDefault();
                var $target = $(event.currentTarget), data = $target.serializeObject() || [];
                data.project_id = $target.data('pid');
                $.ajax({
                    url: payCode.url_send,
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        payCode.blockUi.block($target);
                    },
                    success: function (result) {
                        payCode.blockUi.unblock();
                        var res = payCode.parseJsonString(result);
                        if (res.status == 'success') {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            view.showReviewModal($target);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            openReview : function(event){
                var view = this;
                event.preventDefault();
                var $target = $(event.currentTarget);
                view.showReviewModal($target);
            },
            showReviewModal: function ($target) {
                var view = this;
                if (typeof view.modal_review == 'undefined') {
                    view.modal_review = new AE.Views.Modal_Review_PayCode();
                }
                view.modal_review.setProject($target.data('pid'));
                view.modal_review.openModal();
            }
        });
        AE.Views.Modal_Review_PayCode = AE.Views.Modal_Box.extend({
            el: '#modal_review_paycode',
            events: {
                'submit form#review_after_pay': 'submitReview'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                $("#review_after_pay").validate({
                    ignore: "",
                    rules: {
                        vote: "required"
                    }
                });
            },
            setProject: function (project_id) {
                this.project_id = project_id;
            },
            submitReview: function (event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    data = $target.serializeObject() || [];
                data.project_id = view.project_id;
                $.ajax({
                    url: payCode.url_send,
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        payCode.blockUi.block($target);
                    },
                    success: function (result) {
                        payCode.blockUi.unblock();
                        var res = payCode.parseJsonString(result);
                        if (res.status == 'success') {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            payCode.reload(2);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
        });

        new AE.Views.PayCodeProject();
        // setTimeout(function () {
            $('.paycode.open-modal-review').click();
        // }, 500);
    });
    var payCode = {
        blockUi: new AE.Views.BlockUi,
        url_send: ae_globals.ajaxURL,
        transfer : function($target, data, callback){
            console.log($target);
            console.log(typeof callback);
            console.log($($target).data('pid'));

            if(typeof callback == 'object'){
                callback.showReviewModal($target);
            } else {
                window.location.reload();
            }
        },
        failRequest : function(r){
            payCode.blockUi.unblock();
            AE.pubsub.trigger('ae:notification', {
                msg: 'Error! Status code ' + r.status + ' ' + r.statusText
                    + (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : ''),
                notice_type: 'error'
            });
        },
        reload: function(sec){
            sec = typeof parseInt(sec) != 'NaN'? sec : 0;
            setTimeout(function(){
                document.location.reload();
            }, sec * 1000);
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
        },
    }
})(jQuery, AE.Views);