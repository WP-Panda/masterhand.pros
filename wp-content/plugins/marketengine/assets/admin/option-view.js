window.ME = window.ME || {};
(function(ME, $, Backbone) {
    ME.Models = ME.Models || {};
    ME.Collections = ME.Collections || {};
    ME.Views = ME.Views || {};
    ME.Routers = ME.Routers || {};
    // the pub/sub object for managing event throughout the app
    ME.pubsub = ME.pubsub || {};
    _.extend(ME.pubsub, Backbone.Events);
    ME.globals = me_globals;
    /**
     * override backbone sync function
     */
    Backbone.Model.prototype.sync = function(method, model, options) {
        var data = model.attributes;
        data.action = model.action || 'me-sync';
        switch (method) {
            case 'create':
                data.method = 'create';
                break;
            case 'update':
                data.method = 'update';
                break;
            case 'delete':
                data.method = 'remove';
                break;
            case 'read':
                data.method = 'read';
                break;
        }
        var ajaxParams = {
            type: 'POST',
            dataType: 'json',
            data: data,
            url: me_globals.ajaxurl,
            contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
        };
        ajaxParams = _.extend(ajaxParams, options);
        if (options.beforeSend !== 'undefined') ajaxParams.beforeSend = options.beforeSend;
        ajaxParams.success = function(result, status, jqXHR) {
            ME.pubsub.trigger('me:success', result, status, jqXHR);
            options.success(result, status, jqXHR);
        };
        ajaxParams.error = function(jqXHR, status, errorThrown) {
            ME.pubsub.trigger('me:error', jqXHR, status, errorThrown);
            options.error(jqXHR, status, errorThrown);
        };
        $.ajax(ajaxParams);
    };
    /**
     * override backbone collection sync
     */
    Backbone.Collection.prototype.sync = function(method, collection, options) {
        var ajaxParams = {
            type: 'POST',
            dataType: 'json',
            data: {},
            url: me_globals.ajaxurl,
            contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
        };
        ajaxParams.data = _.extend(ajaxParams.data, options.data);
        if (typeof collection.action !== 'undefined') {
            ajaxParams.data.action = collection.action;
        }
        /**
         * add beforsend function
         */
        if (options.beforeSend !== 'undefined') ajaxParams.beforeSend = options.beforeSend;
        /**
         * success function
         */
        ajaxParams.success = function(result, status, jqXHR) {
            ME.pubsub.trigger('me:success', result, status, jqXHR);
            options.success(result, status, jqXHR);
        };
        ajaxParams.error = function(jqXHR, status, errorThrown) {
            ME.pubsub.trigger('me:error', jqXHR, status, errorThrown);
            options.error(jqXHR, status, errorThrown);
        };
        $.ajax(ajaxParams);
    };
    /**
     * override backbone model parse function
     */
    Backbone.Model.prototype.parse = function(result) {
        if (_.isObject(result.data)) {
            return result.data;
        } else {
            return result;
        }
    };
    /**
     * override backbone model parse function
     */
    Backbone.Collection.prototype.parse = function(result) {
        if (_.isObject(result) && _.isObject(result.data)) {
            return result.data;
        } else {
            return [];
        }
    };
    // create a shorthand for our pubsub
})(window.ME, jQuery, Backbone);
// override underscore template tag
_.templateSettings = {
    evaluate: /\<\#(.+?)\#\>/g,
    interpolate: /\{\{=(.+?)\}\}/g,
    escape: /\{\{-(.+?)\}\}/g
};
(function(Collections, Models, Views, $, Backbone) {
    Models.Options = Backbone.Model.extend({
        action: 'me-option-sync',
        defaults: function() {
            return {
                name: "option_name",
                value: "option_value"
            };
        }
    });
    Views.Options = Backbone.View.extend({
        events: {
            'change input.me-input-field': 'syncOption',
            'change textarea.me-textarea-field': 'syncOption',
            'click input.me-radio-field': 'syncOption',
            'change select.select-field': 'syncOption',
            'change input[type=checkbox]': 'toggleSwitch',
            'keydown input.positive': 'preventNegativeNumber'
        },
        initialize: function() {
            var view = this;
            this.option = this.model;
        },
        // text input, textarea change update option
        syncOption: function(e) {
            var $target = $(e.currentTarget),
                view = this;

            $target.parent().find('.me-warning-icon').remove();
            if ($target.hasClass('no-zero')) {
                if ($target.val() === '') {
                    $target.parent().append('<span class="me-warning-icon"></span>');
                    return;
                }
                if ($target.val() == 0) {
                    $target.parent().append('<span class="me-warning-icon"></span>');
                    return ;
                }
            }

            view.option.set('name', $target.attr('name'));
            view.option.set('value', $target.val());
            view.option.set('type', $target.attr('type'));
            view.option.save('', '', {
                success: function(result, status, jqXHR) {
                    
                    if (status.success) {
                        $target.parent().append('<span class="me-success-icon"></span>');
                        setTimeout(function() {
                            $target.parent().find('.me-success-icon').remove();
                        }, 1000);
                    } else {
                        
                    }
                },
            });
        },
        toggleSwitch: function(e) {
            var $target = $(e.currentTarget),
                view = this;
            view.option.set('name', $target.attr('name'));
            view.option.set('value', $target.attr('checked'));
            view.option.save('', '', {
                success: function(result, status, jqXHR) {
                    if (status.success) {
                        // do something
                    } else {
                        // if (typeof status.msg != 'undefined') {
                        //     alert(status.msg);
                        // }
                    }
                },
            });
        },
        preventNegativeNumber: function(e) {
            var $target = $(e.currentTarget),
                view = this,
                allowedKey = [46, 8, 9, 27, 13, 110, 190],
                reg = /^0+/gi;
            if ($target.hasClass('no-zero')) {
                allowedKey = [46, 8, 9, 27, 13];
            }
            // delete leading zero
            if ($target.val().match(reg)) {
                $target.val($target.val().replace(reg, ''));
            }
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, allowedKey) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: Ctrl+C
                (e.keyCode == 67 && e.ctrlKey === true) ||
                // Allow: Ctrl+X
                (e.keyCode == 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            if (e.ctrlKey && e.keyCode == 86) {
                e.preventDefault();
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        }
    });
    $(document).ready(function() {
        var option_model = new Models.Options();
        if ($('.marketengine-tabs').length > 0 && $('.me-no-ajax').length == 0) {
            var option_view = new Views.Options({
                el: '.marketengine-tabs',
                model: option_model,
            });
        }
    });
})(window.ME.Collections, window.ME.Models, window.ME.Views, jQuery, Backbone);