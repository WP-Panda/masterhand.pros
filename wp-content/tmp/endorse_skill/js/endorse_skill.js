(function($){
    $(document).ready(function(){
        $('.mode-endorse').on('click', endosk.endorse);
        $('.open-edit-skills').on('click', endosk.openEdit);
        $('.form-edit-skills').on('submit', endosk.saveEdit);

        $('#add_new_skill').on('keydown', function(e){
            if(e.keyCode == 13){
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

            //console.log( e.keyCode );
            if (e.keyCode > 90 || e.keyCode < 60){//not alphabet
                //if (e.keyCode != 189 || e.keyCode != 46 || e.keyCode != 8) {// minus | del | del
                if (e.keyCode == 189){
                } else if (e.keyCode == 46){
                } else if (e.keyCode == 32){//space
                } else if (e.keyCode == 37 || e.keyCode == 39){//left arrow | right arrow
                } else if (e.keyCode == 8) {
                } else {
                    e.preventDefault();
                }
            }

        });

        $('.add-new-skill').on('click', function(){
            var user_skill = $('#user_select_skill');
            var new_skill = $('#add_new_skill').val();

            //console.log( user_skill.select2('data') );
            if (!user_skill.find("option[value='" + new_skill + "']").length) {
                // Create a DOM Option and pre-select by default
                var newOption = new Option(new_skill, new_skill, true, true);
                // Append it to the select
                user_skill.append(newOption).trigger('change');

                $('#add_new_skill').val('')
            }
        })
    });

    var endosk = {
        blockUi: new AE.Views.BlockUi,
        url_send: ae_globals.ajaxURL,

        openEdit : function(event){
            endosk.blockUi.block(event.currentTarget);
            $('#user_select_skill').empty();
            $.post(endosk.url_send, 'action=getFEditESk', function(result){
                var data = endosk.parseJsonString(result);
                if(data.status == 'success') {
                    $('#user_select_skill').select2({
                        closeOnSelect: false,
                        allowClear: false,
                        data: endosk.prepareEditSel(data.items),
                        placeholder: ''
                    }).on('select2:unselecting', function (e) {
                        if(e.params.args.data.endorse > 0){
                            e.preventDefault()
                        }
                    }).on('select2:selecting', function (e) {
                        var val = $(this).val();
                        val = (val == null)? 0 : val.length;
                        if(val > 25){
                            e.preventDefault()
                        }
                    });

                    $('#modal_edit_skills').modal()
                } else {
                    AE.pubsub.trigger('ae:notification', {
                        msg: (data.msg? data.msg : 'Error!'),
                        notice_type: 'error'
                    });
                }
            }).fail(endosk.failRequest).always(function(){endosk.blockUi.unblock();})
        },
        prepareEditSel : function(items){
            var data = [];
            for(var i in items){
                var item = items[i];
                var row = {};
                row.id = item.id;
                row.text = item.title + ' (' + item.endorse + ')';
                row.endorse = parseInt(item.endorse);
                if(item.checked == 1){
                    row.selected = true;
                }
                data.push(row);
            }
            return data;
        },
        saveEdit : function(event){
            var pData = {};
            pData['action'] = 'saveESk';
            pData['skills'] = $('#user_select_skill').val();

            endosk.blockUi.block(event.currentTarget);
            $.post(endosk.url_send, pData, function(result){
                var data = endosk.parseJsonString(result)
                if(data.status == 'success') {
                    $('#list_skills_user').html(data.html);
                    $('#modal_edit_skills').modal('hide');
                    $('#user_select_skill').select2('destroy');
                } else {
                    endosk.showError(data.msg)
                }
            }).fail(endosk.failRequest).always(function(){endosk.blockUi.unblock();})

            return false;
        },
        endorse : function (event) {
            var pData = {};
            if(!$(this).hasClass('endorsed')) {
                pData.action = 'endorseSk';
            } else {
                pData.action = 'unEndorseSk';
            }
            pData.uid = $(this).data('uid');
            pData.skill = $(this).data('skill');
            endosk.blockUi.block(event.currentTarget);
            $.post(endosk.url_send, pData, function (result) {
                var data = endosk.parseJsonString(result);
                if (data.status == 'success') {
                    if($(event.currentTarget).hasClass('endorsed')){
                        $(event.currentTarget).removeClass('endorsed');
                    } else {
                        $(event.currentTarget).addClass('endorsed');
                    }
                    $(event.currentTarget).parent().find('.endorse-skill').html(data.value)
                } else {
                    endosk.showError(data.msg)
                }
            }).fail(endosk.failRequest).always(function () {
                endosk.blockUi.unblock();
            })
        },
        failRequest : function(r){
            endosk.blockUi.unblock();
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
        }
    };


})(jQuery);