(function ($) {
    $(document).ready(function () {

        $('.wpp-open-edit-skills').on('click', wpp_skill.openEdit);
        $('.mode-endorse').on('click', wpp_skill.endorse);
        $('.wpp-form-edit-skills').on('submit', wpp_skill.saveEdit);
        $('.wpp-add-new-skill').on('click', wpp_skill.addSkillToList);

        $('#add_new_skill').on('keydown', function (e) {
            if (e.keyCode == 13) {
                var user_skill = $('#user_select_skill');
                var new_skill = this.value;

                //console.log( user_skill.select2('data') );
                if (!user_skill.find("option[value='" + new_skill + "']").length) {
                    // Create a DOM Option and pre-select by default
                    var newOption = new Option(new_skill, new_skill, true, true);
                    // Append it to the select
                    user_skill.append(newOption).trigger('change');

                    this.value = '';
                }
            }
        });


    });

    var wpp_skill = {

        blockUi: new AE.Views.BlockUi,
        url_send: ae_globals.ajaxURL,

        addSkillToList: function (event) {
            let $new_skill_input = $('#add_new_skill'),
                $user_skill = $('#user_select_skill'),
                $new_skill = $new_skill_input.val();
            if (!$user_skill.find("option[value='" + $new_skill + "']").length) {
                let $newOption = new Option($new_skill, $new_skill, true, true);// Create a DOM Option and pre-select by default
                $user_skill.append($newOption).trigger('change');// Append it to the select
                $new_skill_input.val('')
            }
        },

        openEdit: function (event) {

            wpp_skill.blockUi.block(event.currentTarget);
            $('#user_select_skill').empty();

            //$.post(wpp_skill.url_send, 'action=getFEditESk', function (result) {
            $.post(wpp_skill.url_send, 'action=wpp_get_user_skills', function (result) {

                //let data = wpp_skill.parseJsonString(result);
                if (result.success) {

                    $('#user_select_skill').select2({
                        closeOnSelect: false,
                        allowClear: true,
                        multiple: true,
                        debug: true,
                        placeholder: "Put some text...",
                        data: wpp_skill.prepareEditSel(result.data.skills)
                    }).on('select2:selecting', function (e) {
                        var val = $(this).val();
                        val = (val == null) ? 0 : val.length;
                        if (val > 25) {
                            e.preventDefault()
                        }
                    });

                    $('#modal_edit_skills').modal();

                } else {
                    AE.pubsub.trigger('ae:notification', {
                        msg: (data.msg ? data.msg : 'Error!'),
                        notice_type: 'error'
                    });
                }
            }).fail(wpp_skill.failRequest).always(function () {
                wpp_skill.blockUi.unblock();
            })
        },

        prepareEditSel: function (items) {
            var data = [];
            for (var i in items) {
                var item = items[i];
                var row = {};
                row.id = item.id;
                row.text = item.title + ' (' + item.endorse + ')';
                row.endorse = parseInt(item.endorse);
                if (item.checked == 1) {
                    row.selected = true;
                }
                data.push(row);
            }
            console.log(data);
            return data;
        },

        saveEdit: function (event) {

            var pData = {};
            pData['action'] = 'wpp_save_user_skills';

            pData['skills'] = $('#user_select_skill').val();

            wpp_skill.blockUi.block(event.currentTarget);

            $.post(wpp_skill.url_send, pData, function (result) {
                if (result.success) {
                    //$('#list_skills_user').html(data.html);
                    $('#modal_edit_skills').modal('hide');
                    $('#user_select_skill').select2('destroy');
                    wpp_skill.showOk(result.data.msg)
                } else {
                    wpp_skill.showError(result.data.msg)
                }
            }).fail(wpp_skill.failRequest).always(function () {
                wpp_skill.blockUi.unblock();
            })

            return false;
        },

        endorse: function (event) {
            var pData = {};
            if (!$(this).hasClass('endorsed')) {
                pData.action = 'wpp_endorse';
            } else {
                pData.action = 'wpp_un_endorse';
            }

            pData.uid = $(this).data('uid');
            pData.skill = $(this).data('skill');

            wpp_skill.blockUi.block(event.currentTarget);

            $.post(wpp_skill.url_send, pData, function (result) {
                var $_ind = $(event.currentTarget).next('.endorse-skill'),
                    $_count = parseInt($_ind.html(), 10);

                console.log($_count);

                if (result.success) {
                    if ($(event.currentTarget).hasClass('endorsed')) {
                        $(event.currentTarget).removeClass('endorsed');
                        $_ind.html( $_count - 1);
                    } else {
                        $(event.currentTarget).addClass('endorsed');
                        $_ind.html( $_count + 1);
                    }

                   //$(event.currentTarget).parent().find('.endorse-skill').html(data.value)
                } else {
                    wpp_skill.showError(result.msg)
                }
            }).fail(wpp_skill.failRequest).always(function () {
                wpp_skill.blockUi.unblock();
            })
        },

        failRequest: function (r) {
            wpp_skill.blockUi.unblock();
            AE.pubsub.trigger('ae:notification', {
                msg: 'Error! Status code ' + r.status + ' ' + r.statusText
                + (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : ''),
                notice_type: 'error'
            });
        },

        showError: function (msg) {
            AE.pubsub.trigger('ae:notification', {
                msg: (msg ? msg : 'Error!'),
                notice_type: 'error'
            });
        },

        showOk: function (msg) {
            AE.pubsub.trigger('ae:notification', {
                msg: (msg ? msg : 'Success!'),
                notice_type: 'success'
            });
        },

        reload: function (sec) {
            sec = typeof parseInt(sec) != 'NaN' ? sec : 0;
            setTimeout(function () {
                document.location.reload();
            }, sec * 1000);
        },

        isJsonString: function (str) {
            console.log(str);
            try {
                console.log(JSON.parse(str));
                JSON.parse(str);

            } catch (e) {
                return false;
            }
            return true;
        },

        parseJsonString: function (str) {
            return this.isJsonString(str) ? JSON.parse(str) : (typeof str === 'object' ? str : {});
        }
    };


})(jQuery);