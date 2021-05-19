jQuery(function ($) {


    $(document).ready(function () {

        // $('#add_document').on('submit', doc.createDoc);
        $('#form_add_document').on('submit', doc.saveFiles);
        // $('#edit-advert').on('submit', advert.editAdv);
        // $('.advert-action.archive').on('click', advert.toArchive);
        // $('#add_document .delete-file').on('click', doc.delFile);

        $('#form_add_document #upfiles').on('change', doc.addImgPreview);
        $('a[href=#modal_show_file]').on('click', doc.openModalShowFile);
        $('a[href=#modal_delete_file]').on('click', doc.openModalDeleteFile);
        $('#form_delete_file').on('submit', doc.saveFiles);
        // $('#form_delete_file').on('submit', doc.deleteFile);
    });
    var width_modal = $('#modal_show_file .modal-dialog').width();
    var file_list = [];
    var isDeletedFile = false;
    var imglist = $('#list_ImgPreviews');
    var doc = {
        url: '/wp-admin/admin-ajax.php',
        blockUi: new AE.Views.BlockUi,
        maxFileSize: 5767168,
        imagesList: imglist,
        itemPreviewTemplate: $('#item_PreviewTemplate li').clone(),
        fileApi: (window.File && window.FileReader && window.FileList && window.Blob) ? true : false,
        addImgPreview: function () {
            var start_img = 0, now_count_img = 0;
            $('#list_ImgPreviews li[data-id]').each(function () {
                if ($(this)[0].dataset.index == undefined && $(this)[0].dataset.status != "delete") {
                    start_img = start_img + 1;
                }
            });
            var max_image = 10
            if ($('#form_add_document').length == 1) max_image = 5

            var files = this.files;
            console.log(files);
            if (doc.fileApi && (files.length > 0)) {
                for (var i = 0; i < files.length; i++) {
                    now_count_img = start_img + file_list.length
                    if (now_count_img < max_image) {
                        if (files[i].size > doc.maxFileSize) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: 'File size exceeded - must be no more than 5 MB',
                                notice_type: 'error',
                            });
                            return;
                        }

                        if (files[i].type.match(/image\/(jpeg|jpg|png)/)) {
                            doc.imgPreview(files[i], i);
                            file_list.push(files[i])
                        }
                        if (files[i].type.match(/application\/(msword|pdf|vnd.ms-excel|vnd.openxmlformats-officedocument.wordprocessingml.document|vnd.openxmlformats-officedocument.spreadsheetml.sheet)/)) {
                            doc.docPreview(files[i], i);
                            file_list.push(files[i])
                        }
                    }
                }
            }
        },
        imgPreview: function (file, ind) {
            var reader = new FileReader();
            reader.addEventListener('load', function (event) {
                var itemPreview = doc.itemPreviewTemplate.clone();
                itemPreview.find('.img-wrap img').attr('src', event.target.result);
                itemPreview.attr('data-index', ind);
                itemPreview.attr('data-id', file.name);
                itemPreview.attr('alt', file.name);
                itemPreview.attr('title', file.name);
                itemPreview.find('.delete-file').on('click', doc.delImgPreview);
                doc.imagesList.append(itemPreview);
            });
            reader.readAsDataURL(file);
        },
        docPreview: function (file, ind) {
            var reader = new FileReader();
            var typeIcon = '';

            switch (file.type) {
                case 'application/msword':
                    typeIcon = '/wp-content/uploads/2020/08/doc.svg';
                    break;
                case 'application/pdf':
                    typeIcon = '/wp-content/uploads/2020/08/pdf.svg';
                    break;
                case 'application/vnd.ms-excel':
                    typeIcon = '/wp-content/uploads/2020/08/xls.svg';
                    break;
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    typeIcon = '/wp-content/uploads/2020/08/docx.svg';
                    break;
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    typeIcon = '/wp-content/uploads/2020/08/xlsx.svg';
                    break;
            }

            reader.addEventListener('load', function (event) {
                var itemPreview = doc.itemPreviewTemplate.clone();
                itemPreview.find('.img-wrap img').attr('src', typeIcon);
                itemPreview.find('.img-wrap img').css({
                    'width': '42px',
                    'left': '33px',
                    'bottom': '-10px',
                    'top': 'auto'
                });
                itemPreview.find('.portfolio-thumbs_file-name').css('display', 'block');
                itemPreview.find('.portfolio-thumbs_file-name').html(file.name);
                itemPreview.attr('data-index', ind);
                itemPreview.attr('data-id', file.name);
                itemPreview.attr('alt', file.name);
                itemPreview.attr('title', file.name);
                itemPreview.find('.delete-file').on('click', doc.delImgPreview);
                doc.imagesList.append(itemPreview);
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
            console.log(file_list);

            console.log($('#upfiles').val());

            doc.blockUi.block($(this).find('button.btn_submit_document'));
            if (file_list.length > 0 || isDeletedFile) {
                let data = new FormData();

                for (var i in file_list) {
                    data.append('files[]', file_list[i])

                    console.log(file_list[i]);
                }

                console.log(data);


                if ($('input[name=ID]').val() !== undefined) {
                    data.append('delete_file', $('input[name=ID]').val())
                } else {
                    $.each($('#list_ImgPreviews .item'), function () {
                        if ($(this).data('status') !== undefined) {
                            if ($(this).data('status') == 'delete') {
                                data.append('delete_file[]', $(this).data('id'))
                            }
                        }
                    });
                }

                data.append('profile_id', $('input[name=profile_id]').val());
                data.append('action', 'doc_create');
                $.ajax({
                    url: doc.url,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (result) {
                        var data = doc.parseJsonString(result);
                        if (data.success) {
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
                    }
                }).always(function (response) {
                    doc.blockUi.finish();

                    window.location.href = '/profile#settings';
                });
            } else {
                AE.pubsub.trigger('ae:notification', {
                    msg: 'success',
                    notice_type: 'success',
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

            if (file[0].naturalWidth < width_modal) {
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
        deleteFile: function () {
            // doc.blockUi.block($(this).find('button.btn_submit_file'));
            if ($('input[name=ID]').val !== undefined) {
                $.ajax({
                    url: doc.url,
                    data: 'action=doc_create&delete_file=' + $('input[name=ID]').val + '&profile_id=' + $('input[name=profile_id]').val(),
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (result) {
                        var data = doc.parseJsonString(result);
                        if (data.success) {
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
                    }
                }).always(function () {
                    doc.blockUi.finish();
                });
            }
            window.location.href = '/profile#settings';
            return false;
        }
    };
});