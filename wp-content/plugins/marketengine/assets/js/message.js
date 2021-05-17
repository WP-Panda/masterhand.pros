/* global me_globals.ajaxurl, wpAjax*/
(function($) {
    $.fn.MakertEngineMessageUploader = function(options) {
        var setting = {
            multi: false,
            runtimes: 'html5,flash,html4',
            multipart: true,
            name: 'file',
            max_file: 0,
            swf: null,
            extension: 'jpg,jpeg,gif,png',
            handler: '',
            upload_url: '',
            browse_button: 'me-message-send-btn',
            maxsize: "10mb",
            maxcount: 8,
            maxwidth: 0,
            maxheight: 0,
            removable: true
        };
        if (options) {
            options = $.extend(setting, options);
        } else {
            options = $.extend(setting);
        }
        return $(this).each(function() {
            var element = this;
            var upload_container = $(element).find('.upload-container');
            var action = options.multi ? "upload_multi_file" : "upload_single_file";
            var removable = options.removable ? '1' : '0';
            var preview = $(element).find('ul');
            var uploading = null;
            var uploader = null;
            var inquiry_id = options.inquiry_id;
            var listing_id = options.listing_id;
            var $message_container = $(this).find('.me-contact-messages');
            var upload_begin = function(files) {
                $.each(files, function(file) {
                    var progress = "<li class='uploading'><div class='uploading-progress'><span></span></div></li>";
                    $(preview).append(progress);
                });
                uploader.start();
            };
            uploader = new plupload.Uploader({
                container: upload_container.get(0),
                browse_button: options.browse_button,
                runtimes: options.runtimes,
                flash_swf_url: options.swf,
                file_data_name: options.name,
                multi_selection: false,
                url: options.upload_url + "&listing_id=" + listing_id + "&inquiry_id=" + inquiry_id + "&filename=" + options.name + "&action=" + action + "&removable=" + removable,
                filters: {
                    mime_types: [{
                        title: "extensions",
                        extensions: options.extension
                    }],
                    max_file_size: options.maxsize
                },
                init: {
                    FilesAdded: function(up, files) {
                        var current = $(preview).find('li').size();
                        var totalfile = files.length + current;
                        if (options.multi) {
                            if (totalfile > options.maxcount) {
                                alert(me_globals.limitFile + options.maxcount);
                            } else {
                                upload_begin(files);
                            }
                        } else {
                            upload_begin(files);
                        }
                    },
                    UploadProgress: function(up, file) {
                        console.log(file.percent);
                        $(uploading).find('.uploading-progress').css({
                            'width': file.percent + "%"
                        });
                        $message_container.scrollTop($message_container[0].scrollHeight);
                    },
                    Error: function(up, err) {
                        alert("Error : " + err.message);
                        $(uploading).remove();
                    },
                    UploadFile: function(up, file) {
                        uploading = $(preview).find('.uploading').last();
                    },
                    FileUploaded: function(up, file, response) {
                        $(uploading).replaceWith(response.response);
                        $message_container.scrollTop($message_container[0].scrollHeight);
                    }
                }
            });
            uploader.init();
        });
    };
})(jQuery);
(function($) {
    $.fn.MEmessage = function(options) {
        var defaults = {
            type: 'inquiry',
            parent: 0,
            listing: 0,
            paged: 2,
            nonce: '',
            upload_file_nonce: ''
        };
        var settings = $.extend({}, defaults, options);
        var full = false;
        return $(this).each(function(e) {
            var $elem = $(this);
            var $message_container = $elem.find('.me-contact-messages');
            var $load_more_button = $(this).find('.load-message-button');
            var $ul = $elem.find('ul');
            $elem.find('textarea').focus();
            // fetch message function
            var fetch_message = function() {
                    $.ajax({
                        url: me_globals.ajaxurl,
                        type: 'get',
                        data: {
                            action: 'get_messages',
                            type: settings.type,
                            parent: settings.parent,
                            paged: settings.paged,
                            _wpnonce: settings.nonce
                        },
                        beforeSend: function() {
                            settings.paged++;
                        },
                        success: function(res, xhr) {
                            if (res.data) {
                                $ul.prepend(res.data);
                                $message_container.scrollTop($message_container.find("li").eq(0).offset().top + 50);
                            } else {
                                full = true;
                                $load_more_button.remove();
                            }
                        }
                    });
                }
                // click to load more message
            $load_more_button.click(function() {
                fetch_message();
            });
            // scroll to load older messages
            $message_container.scroll(function(e) {
                var pos = $message_container.scrollTop(),
                    h = $message_container.height();
                // check scroll and ajax get messsages
                if (pos == 0 && !full) {
                    fetch_message();
                }
            });
            // send message
            $elem.find('.me-message-content').keydown(function(e) {
                // enter send message
                if (e.keyCode == '13' && !e.shiftKey) {
                    e.preventDefault();
                    $(this).parent('form').submit();
                }
            });
            // message form submit
            $elem.find('form').submit(function(e) {
                e.preventDefault();
                var $textarea = $(this).find('textarea'),
                    content = $textarea.val();
                // message content can not empty
                if (!content) return;
                // ajax send message
                $.ajax({
                    type: 'post',
                    url: me_globals.ajaxurl,
                    data: {
                        action: 'me_send_message',
                        type: settings.type,
                        inquiry_id: settings.parent,
                        content: content,
                        _wpnonce: settings.nonce
                    },
                    beforeSend: function() {
                        $textarea.val('');
                        $elem.find('.me-message-typing').append('<div class="marketengine-loading"><div class="marketengine-loader"><div class="me-ball-clip-rotate"><div></div></div></div></div>');
                    },
                    success: function(response, xhr) {
                        if (response.success) {
                            $ul.append(response.content);
                            $message_container.scrollTop($message_container[0].scrollHeight);
                            $elem.find('.me-inquires-no-conversation').remove();
                            $elem.find('.marketengine-loading').remove();
                        }
                    }
                });
            });
            // setup uploader
            $elem.MakertEngineMessageUploader({
                multi: false,
                removable: false,
                name: 'message_file',
                maxsize: "2mb",
                listing_id: settings.listing,
                inquiry_id: settings.parent,
                extension: 'jpg,jpeg,gif,png,pdf,doc,docx,xls,xlsx,txt',
                upload_url: me_globals.ajaxurl + '?nonce=' + settings.upload_file_nonce
            });
        });
    }
    window.addEventListener("load", function() {
        //Don't use keypress event. keypress event doesn't detect backspace and delete keys. 
        if ($('.me-message-content').length) {
            window.document.querySelector(".me-message-content").addEventListener("keydown", function() {
                var content = window.document.querySelector(".me-message-content").value;
                window.document.querySelector(".me-mc-container").innerHTML = content;
                window.document.querySelector(".me-message-content").style.height = window.document.querySelector(".me-mc-container").scrollHeight + "px";
            }, false);
        }
    }, false);
    /**
     * Inquiry contacts list
     */
    var contact_paged = 2;
    var loading = false;
    $('#contact-list').scroll(function() {
        var pos = $('#contact-list').scrollTop();
        var scroll_height = $(this)[0].scrollHeight;
        var inner_height = $(this).innerHeight();
        if (pos + inner_height >= scroll_height && !loading) {
            $.ajax({
                url: me_globals.ajaxurl,
                type: 'get',
                data: {
                    action: 'get_contact_list',
                    listing: $('#contact-list').attr('data-id'),
                    inquiry_id: $('input[name="inquiry_id"]').val(),
                    paged: contact_paged,
                    s: $('#s_buyer_name').val(),
                    _wpnonce: $('#_wpnonce').val()
                },
                beforeSend: function() {
                    contact_paged++;
                    loading = true
                },
                success: function(res, xhr) {
                    loading = false;
                    if (res.data) {
                        $('#contact-list').append(res.data);
                    }
                }
            });
        }
    });
    /**
     * Returns a function, that, as long as it continues to be invoked, will not
     * be triggered. The function will be called after it stops being called for
     * N milliseconds. If `immediate` is passed, trigger the function on the
     * leading edge, instead of the trailing.
     */
    function me_debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
    var me_ajax_search_buyer = me_debounce(function(event) {
        var $target = $(event.currentTarget),
            name = $target.val(),
            inquiry_id = $('input[name="inquiry_id"]').val(),
            listing_id = $('input[name="listing-contact-list"]').val();
        //if(name == '' || name.length > 3) {
            $.get({
                url: me_globals.ajaxurl,
                data: {
                    action: 'me-get-buyer-list',
                    s: name,
                    listing_id: listing_id,
                    inquiry_id : inquiry_id
                },
                beforeSend: function() {},
                success: function(res, xhr) {
                    if (res.data) {
                        contact_paged = 2;
                        $('#contact-list').html(res.data);
                        $('.me-contact-user-count').html(res.count_msg);
                    }
                }
            });
        //}
    }, 500);
    //  search buyer name
    $('#s_buyer_name').on('keyup', me_ajax_search_buyer);
})(jQuery);