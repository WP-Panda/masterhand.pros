(function ($) {
    // Random String Generator
    function randomStringGenerate(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;

        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }

        return result;
    }

    // Display Button
    function displayButton(type, icon, lang, id, button_class, options, defaults) {
        var type = type;
        var icon = icon;
        var lang = lang;
        var id = id;
        var button_class = button_class;
        var options = options;
        var defaults = defaults;

        var value;

        switch (type) {
            case 'bold':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-bold-' + id + '" title="' + lang['font']['bold'] + '" type="button">' + icon['bold'] + '</button>';
                break;
            case 'italic':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-italic-' + id + '" title="' + lang['font']['italic'] + '" type="button">' + icon['italic'] + '</button>';
                break;
            case 'underline':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-underline-' + id + '" title="' + lang['font']['underline'] + '" type="button">' + icon['underline'] + '</button>';
                break;
            case 'strikethrough':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-strikethrough-' + id + '" title="' + lang['font']['strikethrough'] + '" type="button">' + icon['strikethrough'] + '</button>';
                break;
            case 'supperscript':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-supperscript-' + id + '" title="' + lang['font']['supperscript'] + '" type="button">' + icon['superscript'] + '</button>';
                break;
            case 'subscript':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-subscript-' + id + '" title="' + lang['font']['subscript'] + '" type="button">' + icon['subscript'] + '</button>';
                break;
            case 'font-name':
                value = '<div class="dropdown" title="' + lang['font']['font_name'] + '">';

                value += '<button class="' + button_class + ' dropdown-toggle" id="bbcodeditor-head-btn-font-name-' + id + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">' + icon['font_name'] + '</button>';

                value += '<div class="dropdown-menu dropdown-menu-right">';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-arial-' + id + '" href="javascript:;" style="font-family: Arial">Arial</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-arial-black-' + id + '" href="javascript:;" style="font-family: Arial Black">Arial Black</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-comic-sans-ms-' + id + '" href="javascript:;" style="font-family: Comic Sans MS">Comic Sans MS</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-helvetica-' + id + '" href="javascript:;" style="font-family: Helvetica">Helvetica</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-impact-' + id + '" href="javascript:;" style="font-family: Impact">Impact</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-tahoma-' + id + '" href="javascript:;" style="font-family: Tahoma">Tahoma</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-times-new-roman-' + id + '" href="javascript:;" style="font-family: Times New Roman">Times New Roman</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-name-verdana-' + id + '" href="javascript:;" style="font-family: Verdana">Verdana</a>';
                value += '</div>';

                value += '</div>';
                break;
            case 'font-size':
                value = '<div class="dropdown" title="' + lang['font']['font_size'] + '">';

                value += '<button class="' + button_class + ' dropdown-toggle" id="bbcodeditor-head-btn-font-size-' + id + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">' + icon['font_size'] + '</button>';

                value += '<div class="dropdown-menu dropdown-menu-right">';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-8-' + id + '" href="javascript:;" style="font-size: 8px">8</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-10-' + id + '" href="javascript:;" style="font-size: 10px">10</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-12-' + id + '" href="javascript:;" style="font-size: 12px">12</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-14-' + id + '" href="javascript:;" style="font-size: 14px">14</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-16-' + id + '" href="javascript:;" style="font-size: 16px">16</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-18-' + id + '" href="javascript:;" style="font-size: 18px">18</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-font-size-22-' + id + '" href="javascript:;" style="font-size: 22px">22</a>';
                value += '</div>';

                value += '</div>';
                break;
            case 'color':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-color-' + id + '" title="' + lang['font']['color']['button'] + '" type="button">' + icon['color'] + '</button>';
                break;
            case 'unordered-list':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-unordered-list-' + id + '" title="' + lang['text']['unordered_list'] + '" type="button">' + icon['unordered_list'] + '</button>';
                break;
            case 'ordered-list':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-ordered-list-' + id + '" title="' + lang['text']['ordered_list'] + '" type="button">' + icon['ordered_list'] + '</button>';
                break;
            case 'align':
                value = '<div class="dropdown" title="' + lang['text']['align']['button'] + '">';

                value += '<button class="' + button_class + ' dropdown-toggle" id="bbcodeditor-head-btn-align-' + id + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">' + icon['align'] + '</button>';

                value += '<div class="dropdown-menu dropdown-menu-right">';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-align-left-' + id + '" href="javascript:;" style="text-align: left">' + lang.text.align.left + '</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-align-center-' + id + '" href="javascript:;" style="text-align: center">' + lang.text.align.center + '</a>';
                value += '<a class="dropdown-item" id="bbcodeditor-drp-align-right-' + id + '" href="javascript:;" style="text-align: right">' + lang.text.align.right + '</a>';
                value += '</div>';

                value += '</div>';
                break;
            case 'link':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-link-' + id + '" title="' + lang['inserts']['link']['button'] + '" type="button">' + icon['link'] + '</button>';
                break;
            case 'image':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-image-' + id + '" title="' + lang['inserts']['image']['button'] + '" type="button">' + icon['image'] + '</button>';
                break;
            case 'media':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-media-' + id + '" title="' + lang['inserts']['media']['button'] + '" type="button">' + icon['media'] + '</button>';
                break;
            case 'misc':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-misc-' + id + '" title="' + lang['inserts']['misc']['button'] + '" type="button">' + icon['misc'] + '</button>';
                break;
            case 'advcode':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-advcode-' + id + '" title="' + lang['inserts']['advcode']['button'] + '" type="button">' + icon['advcode'] + '</button>';
                break;
            case 'table':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-table-' + id + '" title="' + lang['inserts']['table']['button'] + '" type="button">' + icon['table'] + '</button>';
                break;
            case 'preview':
                value = '<button class="' + button_class + '" id="bbcodeditor-head-btn-preview-' + id + '" title="' + lang['preview']['button'] + '" type="button">' + icon['preview'] + '</button>';
                break;
        }

        return value;
    }

    // Display Misc Item
    function displayMiscItem(type, id, options, defaults) {
        var type = type;
        var id = id;
        var options = options;
        var defaults = defaults;

        var value;

        switch (type) {
            case 'h1':
                value = '&lt;h1&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h1&gt;';
                break;
            case 'h2':
                value = '&lt;h2&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h2&gt;';
                break;
            case 'h3':
                value = '&lt;h3&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h3&gt;';
                break;
            case 'h4':
                value = '&lt;h4&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h4&gt;';
                break;
            case 'h5':
                value = '&lt;h5&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h5&gt;';
                break;
            case 'h6':
                value = '&lt;h6&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.&lt;/h6&gt;';
                break;
            case 'blockquote':
                value = '<blockquote class="blockquote m-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.</blockquote>';
                break;
            case 'code':
                value = '<code class="m-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec nisl nunc.</code>';
                break;
            case 'linebreak':
                value = '<hr class="border-top border-white"/>';
                break;
        }

        return value;
    }

    // Display Modal
    function displayModal(type, lang, id, options, defaults) {
        var type = type;
        var lang = lang;
        var id = id;
        var options = options;
        var defaults = defaults;

        var value;

        var modal_pre_head = '<div class="modal fade" id="{id}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
        modal_pre_head += '<div class="modal-dialog" role="document">';
        modal_pre_head += '<div class="modal-content">';
        modal_pre_head += '<div class="modal-header">';

        var modal_head = '<h5 class="modal-title">{title}</h5>';
        modal_head += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        modal_head += '</div>'
        modal_head += '<div class="modal-body">';

        var modal_pre_footer = '</div>';
        modal_pre_footer += '<div class="modal-footer">';
        modal_pre_footer += '<button class="btn btn-success" id="bbcodeditor-btn-insert-{type}-' + id + '" type="button">' + lang.insert + '</button>';

        var modal_footer = '</div></div></div></div>';

        switch (type) {
            case 'color':
                if (typeof options.colorPickerDefaultColor == "undefined" || options.colorPickerDefaultColor == "") {
                    var colorPickerDefaultColor = defaults.colorPickerDefaultColor;
                }
                else {
                    var colorPickerDefaultColor = options.colorPickerDefaultColor;
                }

                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-color-' + id);
                value += modal_head.replace('{title}', lang.font.color.modal);

                value += '<div id="bbcodeditor-modal-color-picker-' + id + '"></div>';
                value += '<hr class="mt-2 mb-2"/>';
                value += '<input type="text" class="form-control" id="bbcodeditor-modal-color-picker-output-' + id + '" value="' + colorPickerDefaultColor + '" placeholder="' + lang.font.color.input + '" readonly>';

                value += modal_pre_footer.replace('{type}', 'color');
                value += modal_footer;
                break;
            case 'link':
                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-link-' + id);
                value += modal_head.replace('{title}', lang.inserts.link.modal);

                value += '<input type="text" class="form-control mb-2" id="bbcodeditor-modal-link-text-input-' + id + '" placeholder="' + lang.inserts.link.text + '">';
                value += '<input type="text" class="form-control" id="bbcodeditor-modal-link-input-' + id + '" placeholder="' + lang.inserts.link.insert + '">';

                value += modal_pre_footer.replace('{type}', 'link');
                value += modal_footer;
                break;
            case 'image':
                if (typeof options.enableImageUpload == "undefined" || options.enableImageUpload == "") {
                    var enableImageUpload = defaults.enableImageUpload;
                }
                else {
                    var enableImageUpload = options.enableImageUpload;
                }

                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-image-' + id);
                value += modal_head.replace('{title}', lang.inserts.image.modal);

                value += '<div class="alert alert-info">' + lang.inserts.image.site + '</div>';

                if (enableImageUpload == true) {
                    value += '<ul class="nav nav-tabs">';
                    value += '<li class="nav-item">';
                    value += '<a class="nav-link active" id="image-url-upload-tab-' + id + '" data-toggle="tab" href="#image-url-upload-tabs-' + id + '" role="tab" aria-controls="image-url-upload-tabs-' + id + '" aria-selected="true">' + lang.inserts.image.url + '</a>';
                    value += '</li>';
                    value += '<li class="nav-item">';
                    value += '<a class="nav-link" id="image-upload-tab-' + id + '" data-toggle="tab" href="#image-upload-tabs-' + id + '" role="tab" aria-controls="image-upload-tabs-' + id + '" aria-selected="false">' + lang.inserts.image.upload + '</a>';
                    value += '</li>';
                    value += '</ul>';

                    value += '<div class="tab-content" id="image-url-upload-tabs">';
                    value += '<div class="tab-pane fade show active" id="image-url-upload-tabs-' + id + '" role="tabpanel" aria-labelledby="image-url-upload-tab-' + id + '">';
                    value += '<input type="text" class="form-control mt-2" id="bbcodeditor-modal-image-url-upload-input-' + id + '" placeholder="' + lang.inserts.image.insert_url + '">';
                    value += '</div>';
                    value += '<div class="tab-pane fade" id="image-upload-tabs-' + id + '" role="tabpanel" aria-labelledby="image-upload-tab-' + id + '">';
                    value += '<div class="custom-file mt-2">';
                    value += '<input type="file" class="custom-file-input" id="bbcodeditor-modal-image-upload-' + id + '">';
                    value += '<label class="custom-file-label" for="bbcodeditor-modal-image-upload-' + id + '">' + lang.inserts.image.choose_file + '</label>';
                    value += '</div>';
                    value += '<div id="bbcodeditor-modal-image-upload-output-' + id + '"></div>';
                    value += '</div>';
                    value += '</div>';
                }
                else {
                    value += '<input type="text" class="form-control mt-2" id="bbcodeditor-modal-image-url-upload-input-' + id + '" placeholder="' + lang.inserts.image.insert_url + '">';
                }

                value += '<input type="hidden" id="bbcodeditor-modal-image-upload-type-' + id + '" value="url">';
                value += '<input type="hidden" id="bbcodeditor-modal-image-upload-value-' + id + '" value="">';

                value += modal_pre_footer.replace('{type}', 'image');
                value += modal_footer;
                break;
            case 'media':
                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-media-' + id);
                value += modal_head.replace('{title}', lang.inserts.media.modal);

                value += '<input type="text" class="form-control" id="bbcodeditor-modal-media-input-' + id + '" placeholder="' + lang.inserts.media.insert_url + '">';

                value += modal_pre_footer.replace('{type}', 'media');
                value += modal_footer;
                break;
            case 'misc':
                if (typeof options.includedMiscItems == "undefined" || options.includedMiscItems == "") {
                    var includedMiscItems = defaults.includedMiscItems;
                }
                else {
                    var includedMiscItems = options.includedMiscItems;
                }

                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-misc-' + id);
                value += modal_head.replace('{title}', lang.inserts.misc.modal);

                value += '<ul class="bbcodeditor-modal-misc-items">';

                for (var x = 0; x < includedMiscItems.length; x++) {
                    value += '<li class="bbcodeditor-modal-misc-item rounded bg-primary text-white" id="bbcodeditor-modal-misc-item-' + includedMiscItems[x] + '-' + id + '">';
                    value += displayMiscItem(includedMiscItems[x], id, options, defaults);
                    value += '</li>';
                }

                value += '</ul>';

                value += modal_pre_footer.replace('{type}', 'misc');
                value += modal_footer;
                break;
            case 'advcode':
                if (typeof options.advcodeLanguages == "undefined" || options.advcodeLanguages == "") {
                    var advcodeLanguages = defaults.advcodeLanguages;
                }
                else {
                    var advcodeLanguages = options.advcodeLanguages;
                }

                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-advcode-' + id);
                value += modal_head.replace('{title}', lang.inserts.advcode.modal);

                value += '<select class="custom-select mb-2" id="bbcodeditor-modal-advcode-lang-select-' + id + '">';

                var advcodeCustomLanguages = ['General Code', 'C', 'C#', 'C++'];

                for (var x = 0; x < advcodeLanguages.length; x++) {
                    var val;

                    if (advcodeCustomLanguages.includes(advcodeLanguages[x])) {
                        switch (advcodeLanguages[x]) {
                            case 'General Code':
                                val = 'none';
                                break
                            case 'C':
                                val = 'c';
                                break
                            case 'C#':
                                val = 'csharp';
                                break
                            case 'C++':
                                val = 'cpp';
                                break
                        }
                    }
                    else {
                        val = advcodeLanguages[x].toLowerCase();
                    }

                    value += '<option value="' + val + '">' + advcodeLanguages[x] + '</option>';
                }

                value += '</select>';

                value += '<textarea class="form-control bbcodeditor-modal-advcode-input" id="bbcodeditor-modal-advcode-input-' + id + '" resize="none"></textarea>';

                value += modal_pre_footer.replace('{type}', 'advcode');
                value += modal_footer;
                break;
            case 'table':
                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-table-' + id);
                value += modal_head.replace('{title}', lang.inserts.table.modal);

                value += '<input type="text" class="form-control mb-2" id="bbcodeditor-modal-table-column-value-' + id + '" placeholder="' + lang.inserts.table.cols + '">';
                value += '<input type="text" class="form-control" id="bbcodeditor-modal-table-row-value-' + id + '" placeholder="' + lang.inserts.table.rows + '">';

                value += modal_pre_footer.replace('{type}', 'table');
                value += modal_footer;
                break;
            case 'preview':
                value = modal_pre_head.replace('{id}', 'bbcodeditor-modal-preview-' + id);
                value += modal_head.replace('{title}', lang.preview.modal);

                value += '<div id="bbcodeditor-modal-preview-output-' + id + '"></div>';

                value += modal_footer;
                break;
        }

        return value;
    }

    // Insert Function
    function insertText(selector, text, clear, options, defaults, wrap = false) {
        var selector = selector;
        var text = text;
        var clear = clear;
        var options = options;
        var defaults = defaults;
        var mouse = getMouse(selector);

        if (clear == false) {
            var element = $('#' + selector);
            var start = mouse.start;
            var end = mouse.end;
            var content = $('#' + selector).val();
            var content1 = content.substring(0, start);
            var content2 = content.substring(start, end);
            var content3 = content.substring(end, content.length);
            var textSplit = text.split("][");

            textSplit[0] += "]";
            textSplit[1] = "[" + textSplit[1];

            var total;

            if (wrap) {
                total = content1;

                if (start == end) {
                    total += text;
                }
                else {
                    total += textSplit[0] + content2 + textSplit[1];
                }

                total += content3;
            }
            else {
                total = content1 + text;

                if (start != end) {
                    total += content2;
                }
                else {
                    total += content3;
                }
            }

            element.val(total);

            if (start == end) {
                element.selectRange(start + text.length, start + text.length);
            }
            else {
                element.selectRange(start, (end + text.length));
            }
        }
        else if (clear == true) {
            element.val(text);
            element.focus();
        }
    }

    // Get the mouse positon / selection
    function getMouse(selector) {
        var selector = selector;
        var input = document.getElementById(selector);
        var startPosition = input.selectionStart;
        var endPosition = input.selectionEnd;
        return {start: startPosition, end: endPosition};
    }

    // Main Function
    $.fn.bbcodeditor = function (options) {
        var selector = this;

        var options = options;

        var defaults = {
            lang: 'en-EN',
            icons: 'font-awesome-5',
            height: 200,
            minHeight: 100,
            maxHeight: 400,
            button_class: 'btn btn-primary',
            content_class: '',
            includedButtons: [
                ['bold', 'italic', 'underline'], ['strikethrough', 'supperscript', 'subscript'], ['font-name', 'font-size', 'color'], ['unordered-list', 'ordered-list', 'align'], ['link', 'image', 'media'], ['misc', 'advcode', 'table']
            ],
            advcodeLanguages: ['General Code', 'HTML', 'CSS', 'Javascript', 'PHP', 'XML', 'JSON', 'SQL', 'Ruby', 'Python', 'Java', 'C', 'C#', 'C++', 'Lua', 'Markdown', 'Yaml'],
            enableTextareaResize: true,
            colorPickerDefaultColor: '#3498DB',
            colorPickerSwatches: [
                'rgba(52, 152, 219, 1)',
                'rgba(46, 204, 113, 1)',
                'rgba(26, 188, 156, 1)',
                'rgba(234, 76, 136, 1)',
                'rgba(155, 89, 182, 1)',
                'rgba(241, 196, 15, 1)',
                'rgba(243, 156, 18, 1)',
                'rgba(231, 76, 60, 1)',
                'rgba(236, 240, 241, 1)',
                'rgba(189, 195, 199, 1)',
                'rgba(149, 165, 166, 1)',
                'rgba(127, 140, 141, 1)',
                'rgba(52, 73, 94, 1)',
                'rgba(44, 62, 80, 1)'
            ],
            enableImageUpload: false,
            imageUploadUrl: "",
            imageUploadType: "POST",
            uploadFileName: "filename",
            uploadFile: "bbcodeditor-image-upload",
            uploadFileTokenName: "_token",
            uploadFileToken: "",
            includedMiscItems: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'code', 'linebreak'],
            previewRequestUrl: "",
            previewRequestType: "GET",
            customRequestToken: false,
            previewRequestTokenName: "_token",
            previewRequestToken: ""
        };

        var editor_id = randomStringGenerate(10);

        var footer_text_left = '';

        // Options
        if (typeof options.lang == "undefined" || options.lang == "") {
            var lang = $.editor_lang[defaults.lang];
        }
        else {
            var lang = $.editor_lang[options.lang];
        }

        if (typeof options.icons == "undefined" || options.icons == "") {
            var icons = $.editor_icon[defaults.icons];
        }
        else {
            var icons = $.editor_icon[options.icons];
        }

        if (typeof options.button_class == "undefined" || options.button_class == "") {
            var button_class = defaults.button_class;
        }
        else {
            var button_class = options.button_class;
        }

        if (typeof options.height == "undefined") {
            var height = defaults.height;
        }
        else {
            var height = options.height;
        }

        if (typeof options.minHeight == "undefined") {
            var minHeight = defaults.minHeight;
        }
        else {
            var minHeight = options.minHeight;
        }

        if (typeof options.maxHeight == "undefined") {
            var maxHeight = defaults.maxHeight;
        }
        else {
            var maxHeight = options.maxHeight;
        }

        if (typeof options.includedButtons == "undefined" || options.includedButtons == "") {
            var includedButtons = defaults.includedButtons;
        }
        else {
            var includedButtons = options.includedButtons;
        }

        if (typeof options.defaultValue == "undefined" || options.defaultValue == "") {
            var defaultValue = "";
        }
        else {
            var defaultValue = options.defaultValue;
        }

        if (typeof options.enableTextareaResize == "undefined" || options.enableTextareaResize == "") {
            var enableTextareaResize = defaults.enableTextareaResize;
        }
        else {
            var enableTextareaResize = options.enableTextareaResize;
        }

        if (typeof options.colorPickerDefaultColor == "undefined" || options.colorPickerDefaultColor == "") {
            var colorPickerDefaultColor = defaults.colorPickerDefaultColor;
        }
        else {
            var colorPickerDefaultColor = options.colorPickerDefaultColor;
        }

        if (typeof options.colorPickerSwatches == "undefined" || options.colorPickerSwatches == "") {
            var colorPickerSwatches = defaults.colorPickerSwatches;
        }
        else {
            var colorPickerSwatches = options.colorPickerSwatches;
        }

        if (typeof options.enableImageUpload == "undefined" || options.enableImageUpload == "") {
            var enableImageUpload = defaults.enableImageUpload;
        }
        else {
            var enableImageUpload = options.enableImageUpload;
        }

        if (typeof options.imageUploadUrl == "undefined" || options.imageUploadUrl == "") {
            var imageUploadUrl = defaults.imageUploadUrl;
        }
        else {
            var imageUploadUrl = options.imageUploadUrl;
        }

        if (typeof options.imageUploadType == "undefined" || options.imageUploadType == "") {
            var imageUploadType = defaults.imageUploadType;
        }
        else {
            var imageUploadType = options.imageUploadType;
        }

        if (typeof options.uploadFileName == "undefined" || options.uploadFileName == "") {
            var uploadFileName = defaults.uploadFileName;
        }
        else {
            var uploadFileName = options.uploadFileName;
        }

        if (typeof options.uploadFile == "undefined" || options.uploadFile == "") {
            var uploadFile = defaults.uploadFile;
        }
        else {
            var uploadFile = options.uploadFile;
        }

        if (typeof options.uploadFileTokenName == "undefined" || options.uploadFileTokenName == "") {
            var uploadFileTokenName = defaults.uploadFileTokenName;
        }
        else {
            var uploadFileTokenName = options.uploadFileTokenName;
        }

        if (typeof options.uploadFileToken == "undefined" || options.uploadFileToken == "") {
            var uploadFileToken = defaults.uploadFileToken;
        }
        else {
            var uploadFileToken = options.uploadFileToken;
        }

        if (typeof options.includedMiscItems == "undefined" || options.includedMiscItems == "") {
            var includedMiscItems = defaults.includedMiscItems;
        }
        else {
            var includedMiscItems = options.includedMiscItems;
        }

        if (typeof options.previewRequestUrl == "undefined" || options.previewRequestUrl == "") {
            var previewRequestUrl = defaults.previewRequestUrl;
        }
        else {
            var previewRequestUrl = options.previewRequestUrl;
        }

        if (typeof options.previewRequestType == "undefined" || options.previewRequestType == "") {
            var previewRequestType = defaults.previewRequestType;
        }
        else {
            var previewRequestType = options.previewRequestType;
        }

        if (typeof options.customRequestToken == "undefined" || options.customRequestToken == "") {
            var customRequestToken = defaults.customRequestToken;
        }
        else {
            var customRequestToken = options.customRequestToken;
        }

        if (typeof options.previewRequestTokenName == "undefined" || options.previewRequestTokenName == "") {
            var previewRequestTokenName = defaults.previewRequestTokenName;
        }
        else {
            var previewRequestTokenName = options.previewRequestTokenName;
        }

        if (typeof options.previewRequestToken == "undefined" || options.previewRequestToken == "") {
            var previewRequestToken = defaults.previewRequestToken;
        }
        else {
            var previewRequestToken = options.previewRequestToken;
        }

        // Check Options
        if (typeof options.defaultValue == "undefined") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'defaultValue'));
        }

        if (typeof options.lang != "undefined" && options.lang == "") {
            jQuery.error(lang.errors.lang);
        }

        if (typeof options.icons != "undefined" && options.icons == "") {
            jQuery.error(lang.errors.icons);
        }

        if (typeof options.height != "undefined" && options.height == "") {
            jQuery.error(lang.errors.height);
        }

        if (typeof options.minHeight != "undefined" && options.minHeight == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'minHeight'));
        }

        if (typeof options.maxHeight != "undefined" && options.maxHeight == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'maxHeight'));
        }

        if (typeof options.button_class != "undefined" && options.button_class == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'button_class'));
        }

        if (typeof options.includedButtons != "undefined" && options.includedButtons == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'includedButtons'));
        }

        if (typeof options.advcodeLanguages != "undefined" && options.advcodeLanguages == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'advcodeLanguages'));
        }

        if (typeof options.enableTextareaResize != "undefined" && options.enableTextareaResize == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'enableTextareaResize'));
        }

        if (typeof options.colorPickerDefaultColor != "undefined" && options.colorPickerDefaultColor == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'colorPickerDefaultColor'));
        }

        if (typeof options.colorPickerSwatches != "undefined" && options.colorPickerSwatches == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'colorPickerSwatches'));
        }

        if (typeof options.enableImageUpload != "undefined" && options.enableImageUpload == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'enableImageUpload'));
        }

        if (typeof options.imageUploadUrl != "undefined" && options.imageUploadUrl == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'imageUploadUrl'));
        }

        if (typeof options.imageUploadType != "undefined" && options.imageUploadType == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'imageUploadType'));
        }

        if (typeof options.uploadFileName != "undefined" && options.uploadFileName == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'uploadFileName'));
        }

        if (typeof options.uploadFile != "undefined" && options.uploadFile == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'uploadFile'));
        }

        if (typeof options.uploadFileTokenName != "undefined" && options.uploadFileTokenName == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'uploadFileTokenName'));
        }

        if (typeof options.uploadFileToken != "undefined" && options.uploadFileToken == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'uploadFileToken'));
        }

        if (typeof options.includedMiscItems != "undefined" && options.includedMiscItems == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'includedMiscItems'));
        }

        if (typeof options.previewRequestUrl != "undefined" && options.previewRequestUrl == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'previewRequestUrl'));
        }

        if (typeof options.previewRequestType != "undefined" && options.previewRequestType == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'previewRequestType'));
        }

        if (typeof options.customRequestToken != "undefined" && options.customRequestToken == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'customRequestToken'));
        }

        if (typeof options.previewRequestTokenName != "undefined" && options.previewRequestTokenName == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'previewRequestTokenName'));
        }

        if (typeof options.previewRequestToken != "undefined" && options.previewRequestToken == "") {
            jQuery.error(lang.errors.invalid_value.replace('{options}', 'previewRequestToken'));
        }

        if (typeof options.content_class != "undefined" && options.content_class == "") {
            jQuery.error(lang.errors.selector);
        }

        // Build Basic Template
        var container_class = 'bbcodeditor-container-' + editor_id;

        var head_class = 'bbcodeditor-head-' + editor_id;
        var body_class = 'bbcodeditor-body-' + editor_id;
        var footer_class = 'bbcodeditor-footer-' + editor_id;
        var modals_class = 'bbcodeditor-modals-' + editor_id;

        basic_template = '<div class="bbcodeditor-container" id="' + container_class + '">';
        basic_template += '<div class="bbcodeditor-head btn-toolbar" id="' + head_class + '"></div>';
        basic_template += '<div class="bbcodeditor-body" id="' + body_class + '"></div>';
        basic_template += '<div class="bbcodeditor-footer" id="' + footer_class + '"></div>';
        basic_template += '<div class="bbcodeditor-modals" id="' + modals_class + '"></div>';
        basic_template += '</div>';

        selector.append(basic_template);

        // Build Head
        var editor_head = $('#' + head_class);

        for (var x = 0; x < includedButtons.length; x++) {
            var button_group_id = 'bbcodeditor-head-btn-toolbar-' + x + '-' + editor_id;

            var btn_group_template = '<div class="btn-group" id="' + button_group_id + '"></div>';

            editor_head.append(btn_group_template);

            for (var y = 0; y < includedButtons[x].length; y++) {
                $('#' + button_group_id).append(displayButton(includedButtons[x][y], icons, lang, editor_id, button_class, options, defaults));
            }
        }

        // Build Body
        var editor_body = $('#' + body_class);

        if (typeof options.content_class == "undefined" || options.content_class == "") {
            var body_content_class = 'bbcodeditor-body-content-' + editor_id;
        }
        else {
            var body_content_class = options.content_class;
        }

        var body_template = '<textarea class="bbcodeditor-body-content" id="' + body_content_class + '">' + defaultValue + '</textarea>';

        editor_body.append(body_template);

        $('#' + body_content_class).css('height', height + 'px');

        if (enableTextareaResize == false) {
            $('#' + body_content_class).css('resize', 'none');
        }
        else {
            $('#' + body_content_class).css('min-height', minHeight + 'px');
            $('#' + body_content_class).css('max-height', maxHeight + 'px');
        }

        // Build Footer
        var editor_footer = $('#' + footer_class);

        var footer_template = '<div class="box-left">';
        footer_template += '<span class="text">' + footer_text_left + '</span>';
        footer_template += '</div>';
        footer_template += '<div class="box-right">';
        footer_template += '</div>';

        editor_footer.append(footer_template);

        // Modals
        var includedButtons_list = [].concat.apply([], includedButtons);

        for (var x = 0; x < includedButtons_list.length; x++) {
            $('#' + modals_class).append(displayModal(includedButtons_list[x], lang, editor_id, options, defaults));
        }

        $('#bbcodeditor-head-btn-color-' + editor_id).click(function () {
            $('#bbcodeditor-modal-color-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-head-btn-link-' + editor_id).click(function () {
            $('#bbcodeditor-modal-link-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-head-btn-image-' + editor_id).click(function () {
            $('#bbcodeditor-modal-image-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-modal-image-url-upload-input-' + editor_id).keyup(function () {
            $('#bbcodeditor-modal-image-upload-value-' + editor_id).val($(this).val());
        });

        $('#image-url-upload-tab-' + editor_id).click(function () {
            $('#bbcodeditor-modal-image-upload-type-' + editor_id).val('url');
        });

        $('#image-upload-tab-' + editor_id).click(function () {
            $('#bbcodeditor-modal-image-upload-type-' + editor_id).val('upload');
        });

        $('#bbcodeditor-head-btn-media-' + editor_id).click(function () {
            $('#bbcodeditor-modal-media-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-head-btn-misc-' + editor_id).click(function () {
            $('#bbcodeditor-modal-misc-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-head-btn-advcode-' + editor_id).click(function () {
            $('#bbcodeditor-modal-advcode-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#bbcodeditor-modal-advcode-input-' + editor_id).keydown(function (e) {
            var input_value = $(this).val();

            if (e.which == 9) {
                e.preventDefault();

                $(this).val(input_value + '  ');
            }
        });

        $('#bbcodeditor-head-btn-table-' + editor_id).click(function () {
            $('#bbcodeditor-modal-table-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        });


        $('#bbcodeditor-head-btn-preview-' + editor_id).click(function () {
            // Show Modal
            $('#bbcodeditor-modal-preview-' + editor_id).modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });

            // Send Request
            var val = $('#' + body_content_class).val();
            var previewTokenName;
            var previewToken;

            if (customRequestToken == false) {
                previewTokenName = uploadFileTokenName;
                previewToken = uploadFileToken;
            }
            else {
                previewTokenName = previewRequestTokenName;
                previewToken = previewRequestToken;
            }

            $.ajax({
                url: previewRequestUrl,
                type: previewRequestType,
                dataType: "JSON",
                data: {
                    previewToken: previewTokenName,
                    bbcode: window.btoa(val)
                },
                success: function (data) {
                    var error = data.error;

                    if (typeof error == "undefined" || error == "") {
                        $('#bbcodeditor-modal-preview-output-' + editor_id).html(data.parsed);
                    }
                    else {
                        $('#bbcodeditor-modal-preview-output-' + editor_id).html('<div class="alert alert-danger">' + error + '</div>');
                    }
                }
            });
        });

        $('#bbcodeditor-modal-preview-' + editor_id + ' > .modal-dialog').addClass('modal-lg');

        /*
          >> Modal Actions
        */

        $('#bbcodeditor-btn-insert-color-' + editor_id).click(function () {
            var selectedColor = $('#bbcodeditor-modal-color-picker-output-' + editor_id).val();

            insertText(body_content_class, '[color=' + selectedColor + '][/color]', false, options, defaults, true);

            $('#bbcodeditor-modal-color-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-btn-insert-link-' + editor_id).click(function () {
            var link_text = $('#bbcodeditor-modal-link-text-input-' + editor_id).val();
            var link_val = $('#bbcodeditor-modal-link-input-' + editor_id).val();

            if (link_text += "" && link_val != "") {
                insertText(body_content_class, '[url=' + link_val + ']' + link_text + '[/url]', false, options, defaults, true);
            }
            else {
                if (link_text == "" && link_val != "") {
                    insertText(body_content_class, '[url]' + link_val + '[/url]', false, options, defaults, true);
                }
                else {
                    insertText(body_content_class, '[url][/url]', false, options, defaults, true);
                }
            }

            $('#bbcodeditor-modal-link-text-input-' + editor_id).val('');
            $('#bbcodeditor-modal-link-input-' + editor_id).val('');

            $('#bbcodeditor-modal-link-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-btn-insert-image-' + editor_id).click(function () {
            var image_val = $('#bbcodeditor-modal-image-upload-value-' + editor_id).val();

            if (enableImageUpload == true) {
                var image_type = $('#bbcodeditor-modal-image-upload-type-' + editor_id).val();

                if (image_type == "url") {
                    insertText(body_content_class, '[img]' + image_val + '[/img]', false, options, defaults, true);

                    $('#bbcodeditor-modal-image-' + editor_id).modal('hide');
                }
                else if (image_type == "upload") {
                    const files = new FormData();

                    files.append(uploadFile, $('#bbcodeditor-modal-image-upload-' + editor_id)[0].files[0]);
                    files.append(uploadFileName, uploadFile);
                    files.append(uploadFileTokenName, uploadFileToken);

                    $.ajax({
                        url: imageUploadUrl,
                        type: imageUploadType,
                        processData: false,
                        contentType: false,
                        data: files,
                        success: function (data) {
                            var error = data.error;
                            var message = data.message;

                            if (typeof error == "undefined" || error == "") {
                                if (message != "") {
                                    $('#bbcodeditor-modal-image-upload-output-' + editor_id).html('<div class="alert alert-success mt-2">' + data.message + '</div>');

                                    insertText(body_content_class, '[img]' + data.preview + '[/img]', false, options, defaults, true);

                                    setTimeout(function () {
                                        $('#bbcodeditor-modal-image-upload-output-' + editor_id).empty();

                                        $('#bbcodeditor-modal-image-' + editor_id).modal('hide');
                                    }, 2000);
                                }
                                else {
                                    insertText(body_content_class, '[img]' + data.preview + '[/img]', false, options, defaults, true);

                                    $('#bbcodeditor-modal-image-' + editor_id).modal('hide');
                                }
                            }
                            else {
                                $('#bbcodeditor-modal-image-upload-output-' + editor_id).html('<div class="alert alert-danger mt-2">' + error + '</div>');
                            }
                        },
                        error: function (data) {
                            $('#bbcodeditor-modal-image-upload-output-' + editor_id).html('<pre>' + data + '</pre>');
                        }
                    });
                }
            }
            else {
                insertText(body_content_class, '[img]' + image_val + '[/img]', false, options, defaults, true);

                $('#bbcodeditor-modal-image-' + editor_id).modal('hide');
            }
        });

        $('#bbcodeditor-btn-insert-media-' + editor_id).click(function () {
            var media_url = $('#bbcodeditor-modal-media-input-' + editor_id).val();

            insertText(body_content_class, '[media]' + media_url + '[/media]', false, options, defaults, true);

            $('#bbcodeditor-modal-media-' + editor_id).modal('hide');

            $('#bbcodeditor-modal-media-input-' + editor_id).val('');
        });

        // Misc Items
        $('#bbcodeditor-modal-misc-item-h1-' + editor_id).click(function () {
            insertText(body_content_class, '[h1][/h1]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-h2-' + editor_id).click(function () {
            insertText(body_content_class, '[h2][/h2]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-h3-' + editor_id).click(function () {
            insertText(body_content_class, '[h3][/h3]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-h4-' + editor_id).click(function () {
            insertText(body_content_class, '[h4][/h4]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-h5-' + editor_id).click(function () {
            insertText(body_content_class, '[h5][/h5]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-h6-' + editor_id).click(function () {
            insertText(body_content_class, '[h6][/h6]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-blockquote-' + editor_id).click(function () {
            insertText(body_content_class, '[blockquote][/blockquote]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-code-' + editor_id).click(function () {
            insertText(body_content_class, '[code][/code]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-modal-misc-item-linebreak-' + editor_id).click(function () {
            insertText(body_content_class, '[linebreak]', false, options, defaults, true);

            $('#bbcodeditor-modal-misc-' + editor_id).modal('hide');
        });

        $('#bbcodeditor-btn-insert-advcode-' + editor_id).click(function () {
            var advcode_lang = $('#bbcodeditor-modal-advcode-lang-select-' + editor_id).val();
            var advcode_input = $('#bbcodeditor-modal-advcode-input-' + editor_id).val();

            insertText(body_content_class, '\n[advcode=' + advcode_lang + ']\n' + advcode_input + '\n[/advcode]', false, options, defaults, true);

            $('#bbcodeditor-modal-advcode-' + editor_id).modal('hide');
            $('#bbcodeditor-modal-advcode-input-' + editor_id).val('');
        });

        $('#bbcodeditor-btn-insert-table-' + editor_id).click(function () {
            var table_cols = $('#bbcodeditor-modal-table-column-value-' + editor_id).val();
            var table_rows = $('#bbcodeditor-modal-table-row-value-' + editor_id).val();

            var insertValue = '[table]\n  [tbody]';

            for (var x = 0; x < table_cols; x++) {
                insertValue += '\n    [tr]';

                for (var y = 0; y < table_rows; y++) {
                    insertValue += '\n    [td][/td]';
                }

                insertValue += '\n    [/tr]';
            }

            insertValue += '\n  [/tbody]\n[/table]';

            insertText(body_content_class, insertValue, false, options, defaults, true);

            $('#bbcodeditor-modal-table-' + editor_id).modal('hide');

            $('#bbcodeditor-modal-table-column-value-' + editor_id).val('');
            $('#bbcodeditor-modal-table-row-value-' + editor_id).val('');
        });

        /*
          >> Button Actions
        */

        $('#bbcodeditor-head-btn-bold-' + editor_id).click(function () {
            insertText(body_content_class, '[b][/b]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-italic-' + editor_id).click(function () {
            insertText(body_content_class, '[i][/i]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-underline-' + editor_id).click(function () {
            insertText(body_content_class, '[u][/u]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-strikethrough-' + editor_id).click(function () {
            insertText(body_content_class, '[s][/s]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-supperscript-' + editor_id).click(function () {
            insertText(body_content_class, '[sup][/sup]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-subscript-' + editor_id).click(function () {
            insertText(body_content_class, '[sub][/sub]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-arial-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Arial][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-arial-black-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Arial Black][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-comic-sans-ms-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Comic Sans MS][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-helvetica-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Helvetica][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-impact-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Impact][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-Tahoma-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Tahoma][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-times-new-roman-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Times New Roman][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-name-verdana-' + editor_id).click(function () {
            insertText(body_content_class, '[font=Verdana][/font]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-8-' + editor_id).click(function () {
            insertText(body_content_class, '[size=8][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-10-' + editor_id).click(function () {
            insertText(body_content_class, '[size=10][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-12-' + editor_id).click(function () {
            insertText(body_content_class, '[size=12][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-14-' + editor_id).click(function () {
            insertText(body_content_class, '[size=14][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-16-' + editor_id).click(function () {
            insertText(body_content_class, '[size=16][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-18-' + editor_id).click(function () {
            insertText(body_content_class, '[size=18][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-font-size-22-' + editor_id).click(function () {
            insertText(body_content_class, '[size=22][/size]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-unordered-list-' + editor_id).click(function () {
            insertText(body_content_class, '\n[list]\n[li][/li]\n[/list]', false, options, defaults, true);
        });

        $('#bbcodeditor-head-btn-ordered-list-' + editor_id).click(function () {
            insertText(body_content_class, '\n[list=1]\n[li][/li]\n[/list]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-align-left-' + editor_id).click(function () {
            insertText(body_content_class, '[align=left][/align]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-align-center-' + editor_id).click(function () {
            insertText(body_content_class, '[align=center][/align]', false, options, defaults, true);
        });

        $('#bbcodeditor-drp-align-right-' + editor_id).click(function () {
            insertText(body_content_class, '[align=right][/align]', false, options, defaults, true);
        });
    };
}(jQuery));

// Credit to Sam Deering (https://www.sitepoint.com/jqueryhtml5-input-focus-cursor-positions/)
$.fn.selectRange = function (start, end) {
    return this.each(function () {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};
