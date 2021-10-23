(function ($, Models, Collections, Views) {

    if ($('.btn-get-quotes').length) {
        var a = document.querySelector('.btn-get-quotes'), b = null, P = 90;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
        window.addEventListener('scroll', Ascroll, false);
        document.body.addEventListener('scroll', Ascroll, false);

        function Ascroll() {
            if (b == null) {
                var Sa = getComputedStyle(a, ''), s = '';
                for (var i = 0; i < Sa.length; i++) {
                    if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
                        s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
                    }
                }
                b = document.createElement('div');
                b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
                a.insertBefore(b, a.firstChild);
                var l = a.childNodes.length;
                for (var i = 1; i < l; i++) {
                    b.appendChild(a.childNodes[1]);
                }
                a.style.height = b.getBoundingClientRect().height + 'px';
                a.style.padding = '0';
                a.style.border = '0';
            }
            var Ra = a.getBoundingClientRect(),
                R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('footer').getBoundingClientRect().top + 95);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
            if ((Ra.top - P) <= 0) {
                if ((Ra.top - P) <= R) {
                    b.className = 'stop-bl';
                    b.style.top = -R + 'px';
                } else {
                    b.className = 'sticky-bl';
                    b.style.top = P + 'px';
                }
            } else {
                b.className = '';
                b.style.top = '';
            }
            window.addEventListener('resize', function () {
                a.children[0].style.width = getComputedStyle(a, '').width
            }, false);
        }
    }

    // Url params to object
    var $urlParams;
    (window.onpopstate = function () {
        var match,
            pl = /\+/g,  // Regex for replacing addition symbol with a space
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) {
                return decodeURIComponent(s.replace(pl, " "));
            },
            query = window.location.search.substring(1);

        $urlParams = {};
        while (match = search.exec(query))
            $urlParams[decode(match[1])] = decode(match[2]);
    })();

    // options
    var $_OPTIONS = {
        wrapper_acroll: $('.fre-page-wrapper'),
        overlay: $('.loading'),
        overlay_html: '<div class="loading-blur loading" style="display: block"><div class="loading-overlay"></div><div class="fre-loading-wrap"><div class="fre-loading"></div></div></div>',
    }


    $.fn.extend(
        {
            showLoader: function () {
                this.append($_OPTIONS.overlay_html);
            },

            hideLoader: function () {
                this.remove()
            }
        }
    )

    /**
     * Form Filter Data Serialize
     */

    function FilterSerialize() {
        let formData = $("#wpp-filter-form *")
            .filter(function (index, element) {
                return $(element).val() != '';
            })
            .serialize();

        if (typeof formData === 'undefined' || formData === false || formData === '') {
            formData = 'all=true'
        }

        window.history.pushState([], '', location.protocol + '//' + location.host + location.pathname + '?' + formData);

        $('.btn-get-quotes').removeClass('visible');
        return formData;

    }

    /**
     * Show Loader & Scroll Doc
     * @constructor
     */
    function LoaderScroll() {
        $('body').showLoader();

        $('html, body').animate({
            scrollTop: $_OPTIONS.wrapper_acroll.offset().top - 180
        }, 800);
    }

    /**
     * Paginate Ajax
     */
    $(document).on('click', '.wpp-paginate a.page-numbers, .wpp-paginate a.prev, .wpp-paginate a.next ', function (e) {
        e.preventDefault();

        LoaderScroll();

        let $el = $(this),
            $url = $el.attr('href'),
            $data = {
                action: 'wpp_paginate',
                page: $url,
                id: $('.wpp-data-div').data('id'),
                data: FilterSerialize()
            }

        $.post(ae_globals.plupload_config.url, $data, function (response) {
            if (response.success) {
                $('.project-list-container').html(response.data.posts);
                $('.fre-paginations').html(response.data.paginate);
                // history.pushState([], '', response.data.send_url)
                listControl();
            }

            $('.loading').remove()

        });
    });


    /**
     * json data select
     */
    $(document).on('change', '[data-target-select]', function (e) {
        e.preventDefault();

        let $el = $(e.target),
            $target_flag = $el.data('target-select'),
            $name = $el.val(),
            $target_element = $('[data-parent-select="' + $target_flag + '"]'),
            $options = '<option value="">' + $target_element.data('empty') + '</option>',
            $data_flag = $el.attr('data-flag'), //атрибут для перехода к более глубокому уровню вложекнности
            $group = $el.data('group'), //зависимая группа, надо для очистки при смене
            $index = $el.index('[data-group="' + $group + '"]')

        //очистка следующих элементов
        $('[data-group="' + $group + '"]').each(function (i) {
            if ($index < i) {
                $(this).attr('disabled', 'disabled').html('<option value="">' + $(this).data('empty') + '</option>').removeAttr('data-flag');
            }
        })


        if ($name == '') {
            CompanyLoad();
            return false;
        }

        //доступ к элементам массива
        if (typeof $data_flag !== 'undefined' && $data_flag !== false) {
            var $data = CompanyFilter[$data_flag]['sub_cat'][$name]['sub_cat'];
        } else {
            console.log($name);
            var $data = CompanyFilter[$name]['sub_cat'];
        }


        if (typeof $data !== 'undefined' && $data !== false) {

            $target_element.attr('data-flag', $name);

            $.each($data, function (k, val) {
                $options = $options + '<option value="' + k + '">' + val.name + '</option>';
            })

            $target_element.html($options).removeAttr('disabled');

        }

        CompanyLoad();

    })

    $(document).on('change', '[data-parent-select]', function (e) {

        e.preventDefault();
        let $el = $(e.target),
            $data_flag = $el.attr('data-target-select');

        if (typeof $data_flag !== 'undefined' && $data_flag !== false) {
            return false;
        } else {
            FilterSerialize();
            CompanyLoad();
        }

    })


    $(document).on('change', '.keyword.search', function (e) {
        e.preventDefault();

        let $el = $(e.target),
            $val = $el.val();

        if ($val.length > 3 || $val.length == 0) {
            CompanyLoad()
        }
    })


    /**
     * Filter Action
     */
    function CompanyLoad() {


        $('body').showLoader();

        $('html, body').animate({
            scrollTop: $_OPTIONS.wrapper_acroll.offset().top - 180
        }, 800);

        var $data = {
            action: 'wpp_filter_company',
            page: window.location.href,
            id: $('.wpp-data-div').data('id'),
            data: FilterSerialize()
        }

        $.post(ae_globals.plupload_config.url, $data, function (response) {
            if (response.success) {
                $('.project-list-container').html(response.data.posts);
                $('.fre-paginations').html(response.data.paginate);
                $('.fre-profile-result-sort').html(response.data.founds);
                //history.pushState([], '', response.data.send_url)
            }
            listControl();
            $('.loading').remove()

        });

    }

    $(document).on('click', '.project-filter-clear.clear-filter', function (e) {
        e.preventDefault()
        if (!$('body').hasClass('no-bb-paginate')) {
            return false;
        }

        $('#state,#country,#city,#cat,#sub,#string').val('');
        $('#cat,#country').change();

    });

    function listControl() {
        /**
         * Model company
         */
        Models.Company = Backbone.Model.extend({
            action: 'ae-company-sync',
            initialize: function () {
            }
        });

        /**
         * Company collection
         */
        Collections.Companies = Backbone.Collection.extend({
            model: Models.Company,
            action: 'ae-fetch-companies',
            initialize: function () {
                this.paged = 1;

                /** for modal Views.Modal_GetMultiQuote */
                this.listQuoteCompany = {};
            }
        });

        var modalGetQuote = new Views.Modal_GetQuote();

        /**
         * Define company item view
         */
        CompanyItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'company-item',
            //  template: _.template($('#ae-company-loop').html()),
            onItemBeforeRender: function () {
                //before render item
            },
            onItemRendered: function () {
                //after render view
            },
            events: {
                'click .btn-get-quote': 'showGetQuoteModal',
                'change .get-quote-company': 'checkedForQuoteCompany'
            },
            checkedForQuoteCompany: function (event) {
                console.log('checkedForQuoteCompany');
                var $elm = $(event.target);
                if ($elm.is(':checked')) {
                    this.addItemCom($elm.data('id'), $elm.data('name'));
                } else {
                    this.removeItemCom($elm.data('id'))
                }

                if (Object.keys(this.model.collection.listQuoteCompany).length === 0) {
                    $('.btn-get-quotes').removeClass('visible');
                } else {
                    $('.btn-get-quotes').addClass('visible');
                }

                this.animateChecked(event.target);
            },
            showGetQuoteModal: function (event) {
                var view = this;
                //console.log('showGetQuoteModal');
                event.preventDefault();
                var $elm = $(event.target).parent();
                if (typeof view.modal_get_quote == 'undefined') {
                    view.modal_get_quote = modalGetQuote;
                }
                view.modal_get_quote.setCompanyId($elm.data('id'));
                view.modal_get_quote.setCompanyName($elm.data('name'));
                view.modal_get_quote.openModal();
            },
            animateChecked: function (target) {
                if ($(target).parent().hasClass('is-ajax')) {
                    var sib = $(target).siblings();
                    if (sib.hasClass('active') === true) {
                        sib.removeClass('active');
                    } else {
                        sib.addClass('active')
                    }
                }
            },
            addItemCom: function (key, value) {
                this.model.collection.listQuoteCompany[key] = value;
            },
            removeItemCom: function (key) {
                if (!this.model.collection.listQuoteCompany.hasOwnProperty(key))
                    return;
                if (isNaN(parseInt(key)) || !(this.model.collection.listQuoteCompany instanceof Array))
                    delete this.model.collection.listQuoteCompany[key];
                else
                    this.model.collection.listQuoteCompany.splice(key, 1)
            }
        });

        /**
         * List view control companies list
         */
        ListCompanies = Views.ListPost.extend({
            tagName: 'ul',
            itemView: CompanyItem,
            itemClass: 'company-item'
        });


        $('.section-archive-company').each(function () {
            var collection;
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html());
                collection = new Collections.Companies(postdata);
            } else {
                collection = new Collections.Companies();
            }
            /**
             * init list blog view
             */
            new ListCompanies({
                itemView: CompanyItem,
                collection: collection,
                el: $(this).find('.company-list-container')
            }).on('collection:rendered', function (event) {
                console.log('collection:rendered')
                event.collection.listQuoteCompany = {};
                $('.btn-get-quotes').removeClass('visible');
            });

            $('.btn-get-quotes').on('click', function (event) {
                var view = this;
                event.preventDefault();
                var $target = event.target;
                if (typeof view.modal_get_quote == 'undefined') {
                    view.modal_get_quote = new Views.Modal_GetMultiQuote();
                }
                var keys = Object.keys(collection.listQuoteCompany);
                view.modal_get_quote.setCompanyId(keys);
                view.modal_get_quote.setCompanyName(collection.listQuoteCompany);
                view.modal_get_quote.openModal();
            });

            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: collection,
                el: $(this),
                onAfterInit: function () {
                    var view = this;

                    // Auto filter keyword
                    if (view.$el.find('#search_data').length > 0) {
                        var search_data = JSON.parse(view.$el.find('#search_data').html());
                        if (search_data.keyword) {
                            $('.fre-company-list-filter input[name="s"]').val(search_data.keyword).keyup();
                        }
                    }

                    if (view.getUrlParameter('category_project')) {
                        setTimeout(function () {
                            $('#project_category.fre-chosen-single').trigger("chosen:updated").change();

                        }, 1000);
                    }

                    $('.clear-filter').on('click', function (event) {
                        if (!$('body').hasClass('no-bb-paginate')) {
                            view.clearFilter(event);
                        }
                    });

                    if ($('body').hasClass('page-template-page-top_companies_in_country')) {
                        var view = this;
                        if ($('#country').data('selected_id')) {
                            view.query['country'] = $('#country').data('selected_id')
                            if ($('#state').data('selected_id')) {
                                view.query['state'] = $('#state').data('selected_id')
                                if ($('#city').data('selected_id')) {
                                    view.query['city'] = $('#city').data('selected_id')
                                }
                            }
                        }
                    }
                },
                getUrlParameter: function (name) {
                    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                    if (results == null) {
                        return null;
                    } else {
                        return results[1] || 0;
                    }
                },
                clearFilter: function (event) {
                    event.preventDefault();
                    var view = this,
                        $target = $(event.currentTarget);
                    // reset input, select
                    view.$el.find('form')[0].reset();
                    view.$el.find('form select').trigger('chosen:updated');

                    // view.$el.find('form select#country option:selected').removeAttr('selected');
                    view.$el.find('form select#state option:selected').removeAttr('selected');
                    view.$el.find('form select#city option:selected').removeAttr('selected');
                    view.$el.find('form select#state option, form select#city option').remove();
                    view.$el.find('form select#state').append(new Option("Select country first", ""));
                    view.$el.find('form select#city').append(new Option("Select state first", ""));

                    view.$el.find('form select#cat option:selected').removeAttr('selected');
                    view.$el.find('form select#sub option:selected').removeAttr('selected');
                    view.$el.find('form select#sub option').remove();
                    view.$el.find('form select#sub').append(new Option("Select category first", ""));

                    // reset query
                    view.query['project_category'] = '';
                    // view.query['country'] = '';
                    view.query['state'] = '';
                    view.query['city'] = '';
                    view.query['s'] = '';
                    view.query['cat'] = '';
                    view.query['sub'] = '';
                    // request
                    view.fetch($target);
                },
                onBeforeFetch: function () {
                    var view = this;
                    view.blockUi.unblock();
                    view.blockUi.block(view.$('.fre-company-list-box'));
                },
                onAfterFetch: function (result, resp) {
                    var view = this;
                    if (result.length > 0) {
                        $('.profile-no-result').hide();
                        $('.plural,.singular').removeClass('hide');
                        if (result.length == 1) {
                            $('.plural').hide();
                            $('.singular').show();
                        } else {
                            $('.plural').show();
                            $('.singular').hide();
                        }
                    } else {
                        $('.plural').show().removeClass('hide');
                        $('.singular').hide();
                        $('.profile-no-result').show();
                    }
                },
            });
        });

    }

    listControl();

    if ($('#media-uploader').length) {
        /***
         * SEND POST
         * @type {boolean}
         */
        Dropzone.autoDiscover = false;
        // in MegaBytes;
        let allowedMaxFileSize = 1;
//DROPZONE
        var droppp = new Dropzone("#media-uploader", {
            url: WppJsData.upload,
            autoProcessQueue: true,
            uploadMultiple: true,
            parallelUploads: 1,
            maxFiles: 5,
            maxFilesize: 10,
            createImageThumbnails: true,
            acceptedFiles: 'image/*',
            // max file size in Megabytes
            maxFilesize: allowedMaxFileSize,
            sending: function (file, xhr, formData) {
                //console.log(file);
            },
            success: function (file, response) {
                if (response.success) {
                    file.previewElement.classList.add("dz-success");
                    file['attachment_id'] = response.data.attachment_id; // push the id for future reference
                    var ids = $('#media-ids').val() + ',' + response.data.attachment_id;
                    $('#media-ids').val(ids);
                    //console.log(response.data.attachment_id);
                } else {
                    if (response.status && response.status == 'error') {
                        text = response.message;
                        AntonNotifications.getNotification(text, 'error');
                    }
                }
            },
            error: function (file, response) {
                //console.log(response);
                file.previewElement.classList.add("dz-error");

                if (file.size > (allowedMaxFileSize * 1024 * 1024)) {
                    let msg = WppJsData.file_too_big;
                    let type_notification = 'error';
                    AntonNotifications.getNotification(msg, type_notification);
                    return;
                }

                if (!Dropzone.isValidFile(file, this.options.acceptedFiles)) {
                    let msg = WppJsData.wrong_file;
                    let type_notification = 'error';
                    AntonNotifications.getNotification(msg, type_notification);
                    return;
                }
            },
            // update the following section is for removing image from library
            addRemoveLinks: true,
            removedfile: function (file) {
                var attachment_id = file.attachment_id;
                $.ajax({
                    type: 'POST',
                    url: WppJsData.delete,
                    data: {
                        media_id: attachment_id
                    }
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }
        });

//EDITOR
        if ($('#editor-container').length) {
            var quill = new Quill('#editor-container', {
                modules: {
                    toolbar: [
                        [{header: [2, 3, 4, 5, false]}],
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote'],
                        [{list: 'ordered'}, {list: 'bullet'}],
                    ]
                },
                placeholder: WppJsData.quill_text,
                theme: 'snow'  // or 'bubble'
            });
        }
    }
    $(document).on('submit', '#wpp-send-post-form', function (e) {
        e.preventDefault();
        $('body').showLoader();
        var $this = $(this),
            $msg = document.querySelector('.message_text');
        $msg.value = JSON.stringify(quill.getContents());

        var $data = {
            action: 'wpp_send_message',
            data: $this.serialize()
        }

        $.post(ae_globals.plupload_config.url, $data, function (response) {

            let type_notification = '';
            let num = 0;
            let text = '';

            if (response.success) {
                type_notification = 'success';
                num = response.data.data.msg;
                quill.deleteText(0, quill.getLength());
                //droppp.removeAllFiles();
                $this.find('input').val('');
                $this.find('select').prop('selectedIndex', 0);
            } else {
                type_notification = 'error';
                num = response.data.msg;
            }

            switch (num) {
                case 4: text = WppJsData.empty_data; break;
                case 6: text = WppJsData.empty_title; break;
                case 7: text = WppJsData.empty_message; break;
                case 8: text = WppJsData.success_submit; break;
            }

            AntonNotifications.getNotification(text, type_notification);

            $('.loading').remove();
        });

    });

    AntonNotifications = {
        prepareNotification: function (msg, type_notification) {
            return '<div class="notification autohide ' + type_notification + '-bg" style=""><div class="main-center">' +
                msg+'</div></div>';
        },
        getNotification: function (msg, type_notification) {
            let is_valid = msg != undefined && type_notification != undefined;

            if (is_valid && ['success', 'error'].indexOf(type_notification) >= 0 && msg.trim() != '') {
                let div_html = this.prepareNotification(msg, type_notification);
                let notification = $(div_html);

                if ($('#wpadminbar').length !== 0) {
                    notification.addClass('having-adminbar');
                }

                notification.hide().prependTo('body').fadeIn('fast').delay(1000).fadeOut(5000, function () {
                    $(this).remove();
                });
            }
        }
    }

    var targetDiv = $('body');

    $(window).scroll(function () {

        var windowpos = $(window).scrollTop();

        if (windowpos >= 100) {
            targetDiv.addClass('wpp-scroll');
        } else {
            targetDiv.removeClass('wpp-scroll');
        }

    });

    if ($('.wpp-post-slider').length) {
        $('.wpp-post-slider').slick({
            dots: true,
        });
    }


})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
