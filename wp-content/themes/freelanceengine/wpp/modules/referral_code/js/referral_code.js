if ($ == undefined)
    $ = jQuery;
var mod = {
    url_send: window.location.href,
    field: '',
    direction: 'ASC',
    page: 1,
    searchStr: '',
    templateAlertSucc: {
        wrapBegin: '<div class="alert alert-success" role="alert" data-dismiss="alert"><strong>',
        wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
    },
    templateAlertErr: {
        wrapBegin: '<div class="alert alert-danger" role="alert" data-dismiss="alert"><strong>',
        wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
    },
    templateAlertWarn: {
        wrapBegin: '<div class="alert alert-warning" role="alert" data-dismiss="alert"><strong>',
        wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
    },
    alert: function (msg, type) {
        if (msg == undefined || msg.length == 0) return;

        var wrap
        switch (type) {
            case 'success':
                wrap = this.templateAlertSucc;
                break;
            case 'warning':
                wrap = this.templateAlertWarn;
                break;
            case 'error':
            default:
                wrap = this.templateAlertErr;
                break;
        }

        var alerts = $('.body-alerts').prepend(wrap.wrapBegin + msg + wrap.wrapEnd)
        $(alerts).find('.alert').first().fadeOut({
            duration: 7000, always: function (result) {
                $(result.elem).remove()
            }
        })
    },
    setSort: function (elm, f) {
        var direction = ($(elm).data('sort') != undefined) ? $(elm).data('sort') : 'ASC';

        $(elm).parent().find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up').after(function () {
            console.log('after')
        })
        if (direction == 'ASC') {
            $(elm).find('i').addClass('fa-long-arrow-alt-down')
            $(elm).data('sort', 'DESC');
        } else {
            $(elm).find('i').addClass('fa-long-arrow-alt-up')
            $(elm).data('sort', 'ASC');
        }
        this.field = f;
        this.direction = direction;
        console.log(direction)
        this.getData();
    },
    getData: function (pg) {
        if (this.page != pg && pg != undefined)
            this.page = pg;

        var data = 'action=getList&page=' + this.page;

        if (this.field && this.direction) {
            data += '&orderBy=' + encodeURIComponent(this.field + ',' + this.direction);
        }

        mod.showLoad()
        $.post(mod.url_send, data, function (result) {
            var data = mod.parseJsonString(result)
            mod.hideLoad()
            if (data.status == 'success') {
                $('#referrals tbody').html(data.referrals);
                $('.pagination_referrals').html(data.pagination);
            } else {
                mod.alert(data.msg ? data.msg : 'Error!')
            }
        }).fail(mod.failRequest)
    },
    showLoad: function () {
        $('.blockLoader').show()
    },
    hideLoad: function () {
        $('.blockLoader').hide()
    },
    failRequest: function (r) {
        //console.log(this)
        //console.log(r)
        mod.hideLoad()
        mod.alert('Error! Status code ' + r.status + ' ' + r.statusText
            + (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : '')
        );
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

    show_user_referrals: function (elm, user_id) {
        // console.log(user_id)
        mod.showLoad()
        if ($(elm.parentNode).hasClass('active')) {
            mod.hideLoad()
            $(elm.parentNode).next("tr").slideToggle()
        } else {
            var data = 'action=getList&user_id=' + user_id;
            $.post(mod.url_send, data, function (result) {
                var data = mod.parseJsonString(result)
                mod.hideLoad()
                if (data.status == 'success') {
                    $(elm.parentNode).addClass("active");
                    var str = '<tr class="referrals_list" style="display: none;">' +
                        '<td colspan="4" style="border-top: 0;">' +
                        '<table class="table table-hover" style="">' +
                        '<tbody>' + data.referrals + '</tbody>' +
                        '</table></td></tr>'
                    $(elm.parentNode).after(str)
                    $(elm.parentNode).next("tr").slideToggle()
                } else {
                    mod.alert(data.msg ? data.msg : 'Error!')
                }
            }).fail(mod.failRequest)
        }
    }
}