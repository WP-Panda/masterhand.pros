jQuery(document).ready(function ($) {
    $('.usp-input').on('change', doc.addItemPreview);
    $('.value__block .closed').on('click', doc.deleteFile);

    $('.file__label').click(function(){
        if ($('.value__block .visible').length === 3){
            $('.usp-input').prop('disabled', true);

            AE.pubsub.trigger('ae:notification', {
                msg: 'Reached the limit of number of files',
                notice_type: 'error',
            });
        }
    });

    /*
    $('.input-block').hover(
        function(){
            $(this).find('.value').css('background', '#0a0a0ab0');

            if ($(this).find('.input-block__preview-file').length > 0) {
                $(this).find('.input-block__preview-file').css('z-index', '0');
            }
        },
        function(){
            $(this).find('.value').css('background', '#f4f4f4b5');

            if ($(this).find('.input-block__preview-file').length > 0) {
                $(this).find('.input-block__preview-file').css('z-index', '1');
            }
        }
    );
    */

    $('.input-block__add-to-text').click(function(){
        let parent = $(this).closest('.input-block');
        let link = $(this).attr('data-filepath');
        let filename = parent.find('.value').text();
        let attachedType = parent.find('.input-block__preview-image').length > 0 ? 'image' : 'file';
        let $textarea = $('#user-submitted-content');
        let textareaContent = $textarea.val();

        if (attachedType === 'image'){
            textareaContent += "\r\n"+'<img src="'+link+'" width="" height="" />';
        } else {
            textareaContent += "\r\n"+'<a href="'+link+'">'+filename+'</a>';
        }

        $textarea.val(textareaContent);
    });

});

let width_modal = $('#modal_show_file .modal-dialog').width();
let file_list = [];
let isDeletedFile = false;
let imglist = $('#list_ImgPreviews');

let doc = {
    url: '/wp-admin/admin-ajax.php',
    blockUi: new AE.Views.BlockUi,
    maxFileSize: 5100000,
    imagesList: imglist,
    itemPreviewTemplate: $('#item_PreviewTemplate li').clone(),
    fileApi: (window.File && window.FileReader && window.FileList && window.Blob) ? true : false,

    addItemPreview: function () {
        let start_img = 0;
        let files = this.files;

        if ($('.value__block .visible').length === 3){
            $('.usp-input').prop('disabled', true);
        }

        $('.value__block .visible').each(function () {
            if ($(this)[0].dataset.index == undefined && $(this)[0].dataset.status != "delete") {
                start_img = start_img + 1;
            }
        });

        if (doc.fileApi && (files.length > 0)) {
            for (let i = 0; i < files.length; i++) {

                if (files[i].type.match(/image\/(jpeg|jpg|png)/)) {
                    doc.imgPreview(files[i], i);
                    file_list.push(files[i])
                }

                if (files[i].type.match(/application\/(msword|pdf|vnd.ms-excel|vnd.openxmlformats-officedocument.wordprocessingml.document|vnd.openxmlformats-officedocument.spreadsheetml.sheet)/)) {
                    doc.docPreview(files[i], i);
                    file_list.push(files[i])
                }

                doc.saveFiles();

            }
        }
    },
    
    imgPreview: function (file, ind) {
        let reader = new FileReader();

        reader.addEventListener('load', function (event) {
            setTimeout(function() {
                $('.value__block .visible:last').find('.input-block__preview').html("<img src='"+event.target.result+"' class='input-block__preview-image'>");
            }, 100);
        });

        reader.readAsDataURL(file);
    },
    
    docPreview: function (file, ind) {
        let reader = new FileReader();
        let typeIcon = '';

        switch (file.type) {
            case 'application/msword': typeIcon = '/wp-content/uploads/2020/08/doc.svg'; break;
            case 'application/pdf': typeIcon = '/wp-content/uploads/2020/08/pdf.svg'; break;
            case 'application/vnd.ms-excel': typeIcon = '/wp-content/uploads/2020/08/xls.svg'; break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': typeIcon = '/wp-content/uploads/2020/08/docx.svg'; break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': typeIcon = '/wp-content/uploads/2020/08/xlsx.svg'; break;
        }

        reader.addEventListener('load', function (event) {
            setTimeout(function() {
                $('.value__block .visible:last').addClass('visible--file').find('.input-block__preview').html("<img src='"+typeIcon+"' class='input-block__preview-file'>");
            }, 100);
        });
        reader.readAsDataURL(file);
    },


    delImgPreview: function () {
        var item = $(this).closest('.item');
        var index = $(item).data('index');
        if (index !== undefined) {
            file_list.splice(index, 1);
            $(item).remove()
        } else {
            isDeletedFile = true;
            $(item).attr('data-status', 'delete').css('display', 'none');
        }
    },

    saveFiles: function () {
        let $attachesInput = $('#usp_form').find('input[name="usp_attaches"]');

        doc.blockUi.block($('.value__block .visible:last'));

        if (file_list.length > 0 || isDeletedFile) {
            let data = new FormData();

            for (var i in file_list) {
                data.append('files[]', file_list[i])
            }

            if ($('input[name=ID]').val() !== undefined){
                data.append('delete_file', $('input[name=ID]').val())
            } else {
                $.each($('.value__block .visible'), function () {
                    if ($(this).data('status') !== undefined) {
                        if ($(this).data('status') == 'delete') {
                            data.append('delete_file[]', $(this).data('id'))
                        }
                    }
                });
            }

            data.append('profile_id', $('input[name=profile_id]').val());
            data.append('action', 'doc_create');
            data.append('freelancer_post', true);


            $.ajax({
                url: doc.url,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function (result) {
                    let data = doc.parseJsonString(result);
                    if (data.success) {
                        $('.value__block .visible:last').find('.input-block__add-to-text').attr('data-filepath', data.filepath);
                        $('.value__block .visible:last').find('.closed').attr('data-file-id', data.file_id);

                        if ($attachesInput.val() === ''){
                            $attachesInput.val(data.file_id);
                        } else {
                            $attachesInput.val( $attachesInput.val() + ',' +  data.file_id);
                        }

                        AE.pubsub.trigger('ae:notification', {
                            msg: data.msg,
                            notice_type: 'success',
                        });
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: data.msg,
                            notice_type: 'error',
                        });
                    }
                },
                error: function () {
                    AE.pubsub.trigger('ae:notification', {
                        msg: 'Something wrong',
                        notice_type: 'error',
                    });
                },
            }).always(function () {
                doc.blockUi.finish();
            });
        } else {
            AE.pubsub.trigger('ae:notification', {
                msg: 'success',
                notice_type: 'success',
            });
        }

        return false;
    },

    deleteFile: function () {
        let fileID = $(this).attr('data-file-id');
        let profileID = $('input[name=profile_id]').val();
        let data = new FormData();

        data.append('action', 'doc_create');
        data.append('delete_file', fileID);
        data.append('profile_id', profileID);

        if (fileID !== undefined) {
            $.ajax({
                url: doc.url,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',

                success: function (result) {
                    let data = doc.parseJsonString(result);
                    if (!data.success) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: data.msg,
                            notice_type: 'error',
                        });
                    }
                },
                error: function (jqXHR, exception) {
                    AE.pubsub.trigger('ae:notification', {
                        msg: 'Something wrong',
                        notice_type: 'error',
                    });
                }
            }).always(function (response) {
                doc.blockUi.finish();
            });
        }

        return false;
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

    openModalShowFile: function () {
        var file = $(this).parent().prev().children('img');
        $('#modal_show_file img.show_file')[0].src = file[0].src;

        if(file[0].naturalWidth < width_modal) {
            $('#modal_show_file .modal-dialog').width(file[0].naturalWidth)
        }
        else {
            $('#modal_show_file .modal-dialog').width(width_modal)
        }
    },

    openModalDeleteFile: function () {
        $('input[name=ID]').val($(this).attr('data-file_id'));
        isDeletedFile = true;
        // file_list.length=1;
    },

};