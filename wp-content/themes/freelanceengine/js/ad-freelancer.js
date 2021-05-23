jQuery(function ($) {
    var file_list = [];
    var isDeletedFile = false;

    $(document).ready(function ($) {

        $('#create-ad').on('submit', advert.createAdv);
        $('#edit-advert').on('submit', advert.editAdv);
        $('.advert-action.archive').on('click', advert.toArchive);
        $('.delete-file').on('click', advert.delFile);

        $('#upfiles').on('change', advert.addImgPreview);

    });

    var imglist = $('#listImgPreviews');
    var advert = {
        url: '/wp-admin/admin-ajax.php',
        blockUi: new AE.Views.BlockUi,
        maxFileSize: 2100000,
        imagesList: imglist,
        itemPreviewTemplate: $('#itemPreviewTemplate li').clone(),
        fileApi: (window.File && window.FileReader && window.FileList && window.Blob) ? true : false,

        createAdv: function () {

            //console.log(this);
            //return false;

            if (this.post_title.value == '' || document.querySelector('#post_content').value == '') {
                AE.pubsub.trigger('ae:notification', {
                    msg: 'All fields are required',
                    notice_type: 'error'
                });
                return false;
            }

            //advert.uploadFiles(1216);
            advert.blockUi.block($(this).find('button.btn-crt-advert'));
            $.post('/wp-admin/admin-ajax.php', 'action=fre_create_ad&' + $(this).serialize(), function (result) {
                if (result.success) {
                    advert.uploadFiles(result.post_id, result.msg, '/my-adverts/');
                } else {
                    advert.blockUi.finish();
                    AE.pubsub.trigger('ae:notification', {
                        msg: data.msg,
                        notice_type: 'error',
                    });
                }
            }).error(function () {
                advert.blockUi.finish();
            });

            return false;
        },
        editAdv: function () {
            if (this.post_title.value == '' || document.querySelector('#post_content').value == '') {
                AE.pubsub.trigger('ae:notification', {
                    msg: 'All fields are required',
                    notice_type: 'error'
                });
                return false;
            }

            advert.blockUi.block($('.btn-grp-advert'));
            $.post('/wp-admin/admin-ajax.php', 'action=fre_edit_ad&' + $(this).serialize(), function (result) {
                if (result.success) {
                    advert.uploadFiles(result.post_id, result.msg, window.location.pathname);
                } else {
                    advert.blockUi.finish();
                    AE.pubsub.trigger('ae:notification', {
                        msg: data.msg,
                        notice_type: 'error',
                    });
                }
            });

            return false;
        },
        uploadFiles: function (postId, msg, link) {
            var self = this;
            if (file_list.length > 0 || isDeletedFile) {
                var data = new FormData();
                for (var i in file_list) {
                    data.append('files[]', file_list[i])
                }

                $.each($('#listImgPreviews .item'), function () {
                    if ($(this).data('status') !== undefined) {
                        if ($(this).data('status') == 'delete') {
                            data.append('delete_file[]', $(this).data('id'))
                        }
                    }
                });

                data.append('action', 'fre_ad_attach');
                data.append('post_id', postId);
                $.ajax({
                    url: self.url,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (result) {
                        var data = self.parseJsonString(result);
                        if (data.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: (msg ? msg : data.msg),
                                notice_type: 'success',
                            });

                            // window.location.href = '/my-adverts/';
                            // window.location.href = link ? link : '/my-adverts/';
                            if (link) window.location.href = link
                        }
                    },
                    error: function (result) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: 'Something wrong',
                            notice_type: 'error',
                        });
                    }
                }).always(function () {
                    advert.blockUi.finish();
                });
            } else {
                AE.pubsub.trigger('ae:notification', {
                    msg: msg,
                    notice_type: 'success',
                });

                //window.location.href = '/my-adverts/';

                if (link) window.location.href = link
            }
        },
        addImgPreview: function () {
            console.log('change file');
            var start_img = 0, now_count_img = 0;
            $('#listImgPreviews li[data-id]').each(function () {
                if ($(this)[0].dataset.index == undefined && $(this)[0].dataset.status != "delete") {
                    start_img = start_img + 1;
                }
            });
            var max_image = 10
            if ($('#editprofile').length == 1) max_image = 1

            var files = this.files;
            if (advert.fileApi && (files.length > 0)) {
                for (var i = 0; i < files.length; i++) {
                    now_count_img = start_img + file_list.length
                    if (now_count_img < max_image) {
                        if (files[i].size > advert.maxFileSize) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: 'File size exceeded - must be no more than 2 MB',
                                notice_type: 'error',
                            });
                            return;
                        }

                        if (files[i].type.match(/image\/(jpeg|jpg|png)/)) {
                            advert.imgPreview(files[i], i);
                            file_list.push(files[i])
                        }
                    }
                }
            }
        },
        imgPreview: function (file, ind) {
            var reader = new FileReader();
            reader.addEventListener('load', function (event) {
                var itemPreview = advert.itemPreviewTemplate.clone();

                itemPreview.find('.img-wrap img').attr('src', event.target.result);

                itemPreview.attr('data-index', ind);
                itemPreview.attr('data-id', file.name);
                itemPreview.attr('alt', file.name);
                itemPreview.attr('title', file.name);
                itemPreview.find('.delete-file').on('click', advert.delFile);
                advert.imagesList.append(itemPreview);
            });
            reader.readAsDataURL(file);
        },
        toArchive: function () {
            if (!confirm('Are your sure?')) {
                return;
            }
            var elm = this;

            var postId = $(elm).data('id');
            advert.blockUi.block(elm);
            $.post('/wp-admin/admin-ajax.php', 'action=fre_cancel_ad&post_id=' + postId, function (data) {
                if (data.success) {
                    $(elm).hide();
                    var col_title = $(elm).parent().find('.project-title-col');
                    $(col_title).html($(col_title).data('title'));
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
            }).always(function () {
                advert.blockUi.finish();
            });
        },
        delFile: function () {
            var item = $(this).closest('.item');
            //console.log('index  ' + $(item).data('index'))
            //console.log('id  ' + $(item).data('id'))
            var index = $(item).data('index');
            if (index !== undefined) {
                file_list.splice(index, 1);
                $(item).remove()
            } else {
                isDeletedFile = true
                $(item).attr('data-status', 'delete').css('display', 'none');
            }
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
        }
    }
})