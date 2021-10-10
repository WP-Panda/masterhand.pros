(function ($, Models, Collections, Views) {
    var cache_portfolio = [];
    /*
     *
     * E D I T  P R O F I L E  V I E W S
     *
     */
    Views.Profile = Backbone.View.extend({
        el: '.list-profile-wrapper',
        events: {
            // user account details
            'submit form#account_form': 'saveAccountDetails',
            'submit form#account_form2': 'saveAccountDetails',
            'submit form#account_form_phone': 'saveAccountDetails',
            // user profile details
            'submit form#profile_form': 'saveProfileDetails',
            'click .save-prof-proj-cat': 'saveCategoryDetails',
            'click a.project-employer__reply': 'showReplyModal',

            //save work_experience
            'submit form.freelance-experience-form': 'saveExperience',
            //save certification
            'submit form.freelance-certification-form': 'saveCertification',
            //save education
            'submit form.freelance-education-form': 'saveEducation',
            // remove education, certification, work_experience
            'click a.remove_history_fre': 'openModalRemoveHistoryFre',
            // open modal add portfolio
            'click a.add-portfolio': 'openModalAddPortfolio',
            // open modal edit portfolio
            'click a.edit-portfolio': 'openModalEditPortfolio',
            // open modal view portfolio
            'click a.fre-view-portfolio-new': 'openModalViewPortfolio',
            // remove image in portfolio
            //'click a.remove_image_in_portfolio': 'removeImageInPortfolio',
            // remove portfolio
            'click a.remove_portfolio': 'openModalDeletePortfolio',

            //new start
            // open modal change phone
            // 'click a.change-phone': 'openModalChangePhone',
            //new end

            // open modal change password
            'click a.change-password': 'openModalChangePW',
            // request confirm mail
            'click a.request-confirm': 'requestConfirmMail',
            'click .dropbox': 'showlistvalue',
            'click #et_receive_mail': 'setReceiveMail',
            // show and hide box edit profile
            'click .profile-show-edit-tab-btn': 'showEditTab',
            'click #user_avatar_browse_button': 'openModalUploadAvatar',
            // currently working in experience
            //'click .currently-working' : 'currentlyWorkingEvent'

            'click a.showed_review': 'openModalReview',
            'submit form#form-showed-review': 'saveReviewDetails',
        },
        initialize: function () {

            $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function (e) {
                var $target = $(e.target);
                var $tabs = $target.closest('.nav-tabs-responsive');
                var $current = $target.closest('li');
                var $parent = $current.closest('li.dropdown');
                $current = $parent.length > 0 ? $parent : $current;
                var $next = $current.next();
                var $prev = $current.prev();
                var updateDropdownMenu = function ($el, position) {
                    $el
                        .find('.dropdown-menu')
                        .removeClass('pull-xs-left pull-xs-center pull-xs-right')
                        .addClass('pull-xs-' + position);
                };

                $tabs.find('>li').removeClass('next prev');
                $prev.addClass('prev');
                $next.addClass('next');

                updateDropdownMenu($prev, 'left');
                updateDropdownMenu($current, 'center');
                updateDropdownMenu($next, 'right');
            });
            //set current profile
            if ($('#current_profile').length > 0) {
                this.profile = new Models.Profile(JSON.parse($('#current_profile').html()));
            } else {
                this.profile = new Models.Profile();
            }
            var view = this;
            this.blockUi = new Views.BlockUi();
            this.user = AE.App.user;
            //get id from the url
            var hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').tab('show');

            // update value for post content editor
            console.log();
            if (typeof tinyMCE !== 'undefined' && !$('body').hasClass('author')) {
                setTimeout(function () {
                    tinymce.EditorManager.execCommand('mceAddEditor', true, "post_content");
                    if (view.profile.get('post_content')) {
                        tinymce.EditorManager.get('post_content').setContent(view.profile.get('post_content'));
                    }
                }, 1000);
            }

            if ($('.edit-portfolio-container').length > 0) {
                var $container = $('.edit-portfolio-container');
                //portfolio list control
                if ($('.edit-portfolio-container').find('.postdata').length > 0) {
                    var postdata = JSON.parse($container.find('.postdata').html());
                    this.portfolios_collection = new Collections.Portfolios(postdata);
                } else {
                    this.portfolios_collection = new Collections.Portfolios();
                }
                /**
                 * init list portfolio view
                 */
                new ListPortfolios({
                    itemView: PortfolioItem,
                    collection: this.portfolios_collection,
                    el: $container.find('.list-item-portfolio')
                });
                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: this.portfolios_collection,
                    el: $container
                });
            }
            //button available
            $('#fre-switch-user-available').change(function () {
                var obj = $(this);
                if (obj.is(':checked')) {
                    view.user.save('user_available', 'on', {
                        beforeSend: function () {
                            view.blockUi.block(obj.closest('label'));
                        },
                        success: function (res) {
                            view.blockUi.unblock();
                        }
                    });
                } else {
                    view.user.save('user_available', 'off', {
                        beforeSend: function () {
                            view.blockUi.block(obj.closest('label'));
                        },
                        success: function (res) {
                            view.blockUi.unblock();
                        }
                    });
                }
            });

            if (ae_globals.ae_is_mobile) {
                this.modalChangePW = new Views.Modal_Change_Pass({
                    el: '#tab_change_pw'
                });
            }

            this.$('.sw_skill').chosen({ // use for modal edit project
                max_selected_options: parseInt(ae_globals.max_skill),
                inherit_select_classes: true,
                width: '100%',
            });
            // about_content
            $('.edit-profile-skills').chosen({
                max_selected_options: parseInt(ae_globals.max_skill),
                inherit_select_classes: true,
                width: '100%',
            });

            // $('.edit-profile-skills').on('chosen:maxselected', function (evt, params) {
            //     console.log(evt);
            //     console.log(params);
            //
            // });

            $('select[name ="country"]').change(function () {
                var obj = $(this);
                if (obj.val()) {
                    obj.closest('.fre-input-field').removeClass('error');
                }
            });

            $('select[name ="skill"]').change(function () {
                var obj = $(this);
                if (obj.val()) {
                    obj.closest('.fre-input-field').removeClass('error');
                }
            });

            // remove image portfolio
            $("body").delegate(".remove_image_in_portfolio", "click", function (event) {
                event.preventDefault();
                var obj = $(this);
                var id_md = obj.attr('data-image_id');
                var c_form = obj.closest('form');

                obj.closest('li').remove();
                $('.input_thumbnail_' + id_md, c_form).remove();
            });

            if ($('body').hasClass('page-template-page-profile')) {
                this.getDataProjectCategory();
                console.log('getDataProjectCategory');
            }
        },
        setReceiveMail: function () {
            var value = $("#et_receive_mail_value");
            value.val(value.val() == 0 ? 1 : 0);
        },
        // request a confirm email
        requestConfirmMail: function (e) {
            e.preventDefault();
            var $target = $(e.currentTarget),
                view = this;
            this.user.confirmMail({
                beforeSend: function () {
                    view.blockUi.block($target);
                },
                success: function (result, res, xhr) {
                    view.blockUi.unblock();
                    AE.pubsub.trigger('ae:notification', {
                        msg: res.msg,
                        notice_type: (res.success) ? 'success' : 'error',
                    });
                }
            });
        },
        /**
         * init view setup Block Ui and Model User
         */
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function () {
            // login rule
            // this.account_validator = $("form#account_form").validate({
            //     rules: {
            //         user_email: {
            //             required: true,
            //             email: true
            //         }
            //     }
            // });
            // this.account_validator = $("form#account_form2").validate({
            //     rules: {
            //         paypal: {
            //             required: true,
            //             email: true
            //         }
            //     }
            // });
            /**
             * register rule
             */
            this.profile_validator = $("form#profile_form").validate({
                ignore: "",
                rules: {
                    display_name: "required",
                    et_professional_title: "required",
                    country: {
                        required: {
                            depends: function (element) {
                                var form_ = element.closest('form');
                                var validate_filed = $('#country', form_).attr('data-validate_filed');
                                if (validate_filed == '0') {
                                    $('.novalidate_if_current', form_).removeClass('error');
                                    return false;
                                } else {
                                    return true;
                                }
                            }
                        }
                    },
                    //post_content: "required",
                    hour_rate: {
                        //required: true,
                        number: true,
                        min: 0
                    },
                    et_experience: {
                        required: true,
                        number: true,
                        min: 0,
                        digits: true
                    },
                    user_email: {
                        required: true,
                        email: true
                    },
                    paypal: {
                        // required: true,
                        email: true
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    if ($(element).attr('type') == 'checkbox') {
                        var required_id = $(element).attr('id');
                        var $container = $(element).closest('div');
                        if (!$container.hasClass('error')) {
                            // $container.addClass('error').removeClass(validClass).append('<i class="fa fa-exclamation-triangle" ></i>');
                            $container.addClass('error').removeClass(validClass);
                            $container.find('i').wrap("<div class='errorCheckbox'></div>");
                            setTimeout(function () {
                                $container.find('.errorCheckbox').append($container.find('div.message'));
                            }, 200);
                        }
                    } else {
                        var required_id = $(element).attr('id');
                        var $container = $(element).closest('div');
                        if (!$container.hasClass('error')) {
                            // $container.addClass('error').removeClass(validClass).append('<i class="fa fa-exclamation-triangle" ></i>');
                            $container.addClass('error').removeClass(validClass);
                        }
                    }
                },
                unhighlight: function (element, errorClass, validClass) {
                    var $container = $(element).closest('div');
                    if ($container.hasClass('error')) {
                        $container.removeClass('error').addClass(validClass);
                    }
                    $container.find('div.message').remove().end().find('i.fa-exclamation-triangle').remove();
                    if ($(element).attr('type') == 'checkbox') {
                        $container.find('.errorCheckbox').remove();
                    }
                }
            });
            this.category_validator = $("form#category_form").validate({
                rules: {
                    project_category: {
                        required: true,
                    }
                }
            });
        },
        showlistvalue: function (event) {
            event.preventDefault();
            if ($('.list-value').hasClass('hide')) {
                $('.list-value').removeClass('hide');
            } else {
                $('.list-value').addClass('hide');
            }
        },
        /**
         * user profile, catch event when user submit profile form
         */
        saveAccountDetails: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('.fre-btn'),
                view = this;

            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                view.user.set($(this).attr('name'), $(this).val());
            });
            // check form validate and process sign-in
            if (!form.hasClass("processing")) {
                this.user.set('do', 'profile');
                this.user.request('update', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:account', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            /*setTimeout(function(){
                                window.location.reload();
                            },2500);*/
                            window.location.href = '/profile#settings';
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },
        /**
         * user profile, catch event when user submit profile form
         */
        saveProfileDetails: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('.btn-submit'),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                if ($(this).attr('name') != 'skill') {
                    view.profile.set($(this).attr('name'), $(this).val());
                } else {
                    view.profile.set($(this).attr('name'), $(this).val());
                }
            });

            /**
             * update input check box to model
             */
            form.find('input[type=checkbox]').each(function () {
                var name = $(this).attr('name');
                view.profile.set(name, null);
            });
            form.find('input[type=checkbox]:checked').each(function () {
                var name = $(this).attr('name');
                view.profile.set(name, $(this).val());
            });

            /**
             * update input radio to model
             */
            form.find('input[type=radio]:checked').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });

            // check form validate and process sign-in
            if (this.profile_validator.form() && !form.hasClass("processing")) {
                this.profile.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:profile', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            // cover
                            if (typeof advert !== 'undefined')
                                advert.uploadFiles(profile.id, '', '/profile/');

                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });

                            if ($('#listImgPreviews li[data-id]').length <= 1) {
                                location.href = '/profile#settings';
                            }
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },

        dataProjectCategory: {
            parentSelect: {},
            subSelect: {},
            btnAdd: {},
            maxSel: 0,
            data: {},
            selected: []
        },
        getDataProjectCategory: function () {
            this.dataProjectCategory.parentSelect = $('#list_parentProjectCat');
            this.dataProjectCategory.subSelect = $('#list_subProjectCat').select2();
            this.dataProjectCategory.maxSel = this.dataProjectCategory.parentSelect.data('max');
            this.dataProjectCategory.btnAdd = $('.add-proj-cat');
            var self = this,
                data = {action: 'pre_project_cat'};
            $.post(AE.ajaxParams.url, data, function (response) {

                self.initElmSelectProjCat(response.data);
            }).always(function () {
                // console.log(self.dataProjectCategory);
                var list = self.profile.attributes.tax_input.project_category;
                if (list.length > 0) {
                    for (var ind in list) {
                        self.addSelProjectCat(list[ind].term_id, list[ind].name);
                    }
                }
            });
        },
        initElmSelectProjCat: function (data) {
            var _self = this;
            var self = this.dataProjectCategory;
            self.data = data;

            self.parentSelect.select2({
                closeOnSelect: true,
                allowClear: false,
                data: self.data.parents,
                placeholder: 'Select category'
            }).on('select2:select', function (e) {
                var obj = $(e.target);
                var val = obj.val();
                if (val > 0) {
                    var list = self.data.children[val];
                    if (list !== undefined) {
                        self.subSelect.html('').select2({
                            placeholder: 'Select Subcategory',
                            closeOnSelect: true,
                            allowClear: false,
                            data: list
                        });
                    } else {
                        list = [{
                            id: val,
                            text: obj.find('option:selected').text()
                        }];
                        self.subSelect.html('').select2({
                            placeholder: 'Select Subcategory',
                            closeOnSelect: true,
                            allowClear: false,
                            data: list
                        });
                    }
                    self.subSelect.attr('disabled', false);
                }
            });

            self.btnAdd.on('click', function () {
                console.log(self.selected);
                var id = self.subSelect.val();
                if (id > 0) {
                    _self.addSelProjectCat(id, self.subSelect.find('option:selected').text());
                }
            })
        },
        addSelProjectCat: function (value, title) {
            var self = this.dataProjectCategory;
            var keys = Object.keys(self.selected);
            var id = value.toString();
            if (keys.length < self.maxSel) {
                var exist = self.selected.indexOf(id);
                if (exist === -1) {
                    self.selected.push(id);
                    var item = '<li class="item-profile-project-category">' +
                        '<div class="title-prof-pro-cat">' + title + '</div>' +
                        '<div class="delete-prof-pro-cat" data-id="' + id + '">X</div>' +
                        '</li>';
                    item = html2dom(item);
                    $(item).find('.delete-prof-pro-cat').on('click', function () {
                        var id = this.dataset['id'];
                        var exist = self.selected.indexOf(id);
                        console.log('id  ' + id);
                        console.log('exist  ' + exist);
                        console.log('hasOwnProperty  ' + self.selected.hasOwnProperty(id));
                        if (exist !== -1) {
                            delete self.selected[exist];

                            $(this).parent().remove()
                        }
                    });
                    $('#list_profProjectCat').append(item)
                }
            }

            function html2dom(html) {
                var container = document.createElement('div');
                container.innerHTML = html;
                return container.childNodes;
            }
        },
        saveCategoryDetails: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var button = $(event.currentTarget),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            console.log('saveCategoryDetails')
            console.log(view.dataProjectCategory.selected)

            var data = {
                action: 'prof_proj_cat',
                selected: view.dataProjectCategory.selected
            };

            view.blockUi.block(button);
            $.post(AE.ajaxParams.url, data, function (response) {
                if (response.success) {
                    AE.pubsub.trigger('ae:notification', {
                        msg: response.msg,
                        notice_type: 'success'
                    });
                    $('#editcategory').modal('hide');
                    view.blockUi.finish();
                    location.href = '/profile#settings';
                } else {
                    view.blockUi.finish();
                    AE.pubsub.trigger('ae:notification', {
                        msg: response.msg,
                        notice_type: 'error'
                    });
                }
            }).error(function (ob, st) {
                view.blockUi.finish();
                AE.pubsub.trigger('ae:notification', {
                    msg: st,
                    notice_type: 'error'
                });
            });
        },

        saveExperience: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var form = $(event.currentTarget),
                button = form.find('.btn-submit'),
                view = this,
                temp = new Array();

            /**
             * call validator init
             */
            //this.initValidator();
            this.experience_validator = form.validate({
                ignore: "",
                rules: {
                    "work_experience[title]": "required",
                    "work_experience[subtitle]": "required",
                    "work_experience[m_from]": "required",
                    "work_experience[y_from]": "required",
                    "work_experience[m_to]": {
                        required: {
                            depends: function (element) {
                                var form_ = element.closest('form');
                                var checked_currently_working = $('input[name="work_experience[currently_working]"]', form_).attr('checked');
                                if (checked_currently_working == 'checked') {
                                    $('.novalidate_if_current', form_).removeClass('error');
                                    return false;
                                } else {
                                    return true;
                                }
                            }
                        }
                    },
                    "work_experience[y_to]": {
                        required: {
                            depends: function (element) {
                                var form_ = element.closest('form');
                                var checked_currently_working = $('input[name="work_experience[currently_working]"]', form_).attr('checked');
                                if (checked_currently_working == 'checked') {
                                    $('.novalidate_if_current', form_).removeClass('error');
                                    return false;
                                } else {
                                    return true;
                                }
                            }
                        }
                    }
                    //"work_experience[content]": "required"
                }
            });

            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            /**
             * update input check box to model
             */
            form.find('input[type=checkbox]').each(function () {
                var name = $(this).attr('name');
                if (name !== "et_receive_mail_check") {
                    view.profile.set(name, null);
                }
            });
            form.find('input[type=checkbox]:checked').each(function () {
                var name = $(this).attr('name');
                if (typeof temp[name] !== 'object') {
                    temp[name] = new Array();
                }
                temp[name].push($(this).val());
                view.profile.set(name, temp[name]);
            });
            /**
             * update input radio to model
             */
            form.find('input[type=radio]:checked').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            // check form validate and process sign-in
            if (this.experience_validator.form() && !form.hasClass("processing")) {
                this.profile.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:profile', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            location.href = '/profile#settings';
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },

        saveCertification: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var form = $(event.currentTarget),
                button = form.find('.btn-submit'),
                view = this,
                temp = new Array();

            /**
             * call validator init
             */
            //this.initValidator();
            this.certification_validator = form.validate({
                ignore: "",
                rules: {
                    "certification[title]": "required",
                    "certification[subtitle]": "required",
                    "certification[y_from]": "required",
                    "certification[m_from]": "required",
                    "certification[m_from]": "required",
                    "certification[y_to]": "required",
                }
            });

            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            /**
             * update input check box to model
             */
            form.find('input[type=checkbox]').each(function () {
                var name = $(this).attr('name');
                if (name !== "et_receive_mail_check") {
                    view.profile.set(name, null);
                }
            });
            form.find('input[type=checkbox]:checked').each(function () {
                var name = $(this).attr('name');
                if (typeof temp[name] !== 'object') {
                    temp[name] = new Array();
                }
                temp[name].push($(this).val());
                view.profile.set(name, temp[name]);
            });
            /**
             * update input radio to model
             */
            form.find('input[type=radio]:checked').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });

            // check form validate and process sign-in
            if (this.certification_validator.form() && !form.hasClass("processing")) {
                this.profile.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:profile', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            location.href = '/profile#settings';
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },

        saveEducation: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var form = $(event.currentTarget),
                button = form.find('.btn-submit'),
                view = this,
                temp = new Array();

            /**
             * call validator init
             */
            //this.initValidator();
            this.education_validator = form.validate({
                ignore: "",
                rules: {
                    "education[title]": "required",
                    "education[subtitle]": "required",
                    "education[y_from]": "required",
                    "education[m_from]": "required",
                    "education[y_to]": "required",
                    "education[m_to]": "required",
                }
            });

            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            /**
             * update input check box to model
             */
            form.find('input[type=checkbox]').each(function () {
                var name = $(this).attr('name');
                if (name !== "et_receive_mail_check") {
                    view.profile.set(name, null);
                }
            });
            form.find('input[type=checkbox]:checked').each(function () {
                var name = $(this).attr('name');
                if (typeof temp[name] !== 'object') {
                    temp[name] = new Array();
                }
                temp[name].push($(this).val());
                view.profile.set(name, temp[name]);
            });
            /**
             * update input radio to model
             */
            form.find('input[type=radio]:checked').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });

            // check form validate and process sign-in
            if (this.education_validator.form() && !form.hasClass("processing")) {
                this.profile.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:profile', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            location.href = '/profile#settings';
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },

        getUrlParameter: function (sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        },
        openModalAddPortfolio: function (event) {
            event.preventDefault();
            var portfolio = new Models.Portfolio();
            this.modalPortfolio = new Views.Modal_Add_Portfolio({
                el: '#modal_add_portfolio',
                collection: this.portfolios_collection,
                // model: portfolio
            });
            this.modalPortfolio.setModel(portfolio, this.profile);
            this.modalPortfolio.openModal();
        },
        openModalEditPortfolio: function (event) {
            event.preventDefault();
            var id = $(event.currentTarget).attr('data-id');
            var portfolio = new Models.Portfolio();
            var view = this;
            var modal_id = '#modal_edit_portfolio';

            if (cache_portfolio[id]) {
                this.modalPortfolio = new Views.Modal_Add_Portfolio({
                    el: modal_id,
                    collection: cache_portfolio[id]
                });
                this.modalPortfolio.setModel(portfolio, this.profile);
                this.modalPortfolio.openModal();
            } else {
                $.ajax({
                    type: "post",
                    url: ae_globals.ajaxURL,
                    dataType: 'json',
                    data: {
                        action: 'ae-fetch-info-portfolio',
                        portfolio_id: id
                    },
                    beforeSend: function () {
                        $(event.currentTarget).attr('disabled', true).css('opacity', '0.5');
                        view.blockUi.block($(event.currentTarget).closest('li'));
                    },
                    success: function (data, statusText, xhr) {
                        $(event.currentTarget).attr('disabled', false).css('opacity', '1');

                        if (data.success) {
                            cache_portfolio[id] = data.data;
                            this.modalPortfolio = new Views.Modal_Add_Portfolio({
                                el: modal_id,
                                collection: data.data
                            });
                            this.modalPortfolio.setModel(portfolio, this.profile);
                            this.modalPortfolio.openModal();
                        }
                        view.blockUi.unblock();
                    }
                });
            }
        },

        openModalViewPortfolio: function (event) {
            event.preventDefault();
            var view = this;
            var id = $(event.currentTarget).attr('data-id');
            var portfolio = new Models.Portfolio();
            var modal_id = '#modal_view_portfolio';
            if (cache_portfolio[id]) {
                this.modalPortfolio = new Views.Modal_Add_Portfolio({
                    el: modal_id,
                    collection: cache_portfolio[id]
                });
                this.modalPortfolio.setModel(portfolio, this.profile);
                this.modalPortfolio.openModal();
            } else {
                $.ajax({
                    type: "post",
                    url: ae_globals.ajaxURL,
                    dataType: 'json',
                    data: {
                        action: 'ae-fetch-info-portfolio',
                        portfolio_id: id
                    },
                    beforeSend: function () {
                        $(event.currentTarget).attr('disabled', true).css('opacity', '0.5');
                        view.blockUi.block($(event.currentTarget).closest('li'));
                    },
                    success: function (data, statusText, xhr) {
                        $(event.currentTarget).attr('disabled', false).css('opacity', '1');

                        if (data.success) {
                            cache_portfolio[id] = data.data;

                            this.modalPortfolio = new Views.Modal_Add_Portfolio({
                                el: modal_id,
                                collection: data.data
                            });
                            this.modalPortfolio.setModel(portfolio, this.profile);
                            this.modalPortfolio.openModal();
                        }

                        view.blockUi.unblock();
                    }
                });
            }
        },
        openModalDeletePortfolio: function (event) {
            event.preventDefault();
            var id = $(event.currentTarget).attr('data-portfolio_id');
            var portfolio = new Models.Portfolio();
            $('#modal_delete_portfolio').find('form').attr('data-processing', 'no');
            this.modalPortfolio = new Views.Modal_Add_Portfolio({
                el: '#modal_delete_portfolio',
                collection: id
            });
            this.modalPortfolio.setModel(portfolio, this.profile);
            this.modalPortfolio.openModal();
        },

        openModalRemoveHistoryFre: function (event) {
            event.preventDefault();
            var id = $(event.currentTarget).attr('data-id');
            var last = $(event.currentTarget).closest('ul').find('li').length;
            $('#modal_delete_meta_history').find('form').attr('data-processing', 'no');
            this.modalPortfolio = new Views.Modal_Add_Portfolio({
                el: '#modal_delete_meta_history',
                collection: {
                    id: id,
                    last: last
                }
            });
            this.modalPortfolio.openModal();
        },
        //new start
        /*  openModalChangePhone: function (event) {
              event.preventDefault();
              this.modalChangePhone = new Views.Modal_Change_Phone({
                  el: '#modal_change_phone'
              });
              this.modalChangePhone.openModal();
          },*/
        //new end
        openModalChangePW: function (event) {
            event.preventDefault();
            this.modalChangePW = new Views.Modal_Change_Pass({
                el: '#modal_change_pass'
            });
            this.modalChangePW.openModal();
        },
        showEditTab: function (e) {
            e.preventDefault();
            var obj = $(e.currentTarget);
            var tab_id = obj.attr('data-ctn_edit');
            var tab_hide = obj.attr('data-ctn_hide');
            $('#' + tab_id).fadeIn();
            if (tab_hide) {
                $('#' + tab_hide).fadeOut();
            }
            obj.closest('.cnt-profile-hide').css('display', 'none');
        },
        openModalUploadAvatar: function (event) {
            event.preventDefault();
            var id = $(event.currentTarget).attr('data-id');
            $('#uploadAvatar').find('form').attr('data-processing', 'no');
            this.modalUploadAvatar = new Views.Modal_Upload_Avatar({
                el: '#uploadAvatar',
                collection: cache_portfolio[id]
            });
            this.modalUploadAvatar.openModal();
        },

        openModalReview: function (event) {
            event.preventDefault();
            var id = $(event.currentTarget).attr('data-review_id');
            $('#review-id').val(id)

            var showed = $(event.currentTarget).attr('data-is_showed');
            $('#showed').val(showed)

            var text = $(event.currentTarget).attr('data-text');
            $('#form-showed-review h2')[0].textContent = text + '?'
        },
        saveReviewDetails: function (event) {
            event.preventDefault();
            var id = $('#review-id').val();
            var showed = $('#showed').val();

            var view = this,
                $target = $(event.currentTarget),
                button = $target.find('button.btn-showed-review');
            $.ajax({
                url: ae_globals.ajaxURL,
                type: 'post',
                data: {
                    ID: id,
                    showed: showed,
                    action: 'ae-review-show',
                    // method: 'update'
                },
                beforeSend: function () {
                    view.blockUi.block(button);
                },
                success: function (res) {
                    view.blockUi.unblock();

                    if (res.success) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'success'
                        });
                        location.href = '/profile#reviews';
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                        view.closeModal();
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'error'
                        });
                    }
                }
            });
        },

        // open modal for reply to review
        showReplyModal: function (event) {
            var view = this;

            event.preventDefault();
            var $target = event.target;
            if (typeof view.modal_reply == 'undefined') {
                view.modal_reply = new AE.Views.Modal_Reply();
            }

            view.modal_reply.$('input[name="project_id"]').val($target.getAttribute('data-project-id'));
            view.modal_reply.$('input[name="reviewing_id"]').val($target.getAttribute('data-review_id'));

            view.modal_reply.$('#project-id').val($target.getAttribute('id'));
            view.modal_reply.openModal();
        },

        closeModal: function (time, callback) {
            var modal = this;
            modal.$el.modal('hide');
            return false;
        },
    });
    /*
     *
     * M O D A L  A D D  P O R T F O L I O  V I E W S
     *
     */

    AE.Views.Modal_Reply = AE.Views.Modal_Box.extend({
        el: '#modal_reply',
        events: {
            'submit form.review-form': 'submitReview',
        },
        initialize: function () {
            AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
            this.blockUi = new Views.BlockUi();
            $("form.review-form").validate({
                ignore: "",
                rules: {
                    comment_content: "required",
                }
            });
        },
        setProject: function (project_id) {
            this.project_id = project_id;
        },
        submitReview: function (event) {
            event.preventDefault();
            var view = this,
                $target = $(event.currentTarget),
                data = $target.serializeObject() || [];

            rwRating.send($target);

            return false;
        },
    });

    Views.Modal_Add_Portfolio = Views.Modal_Box.extend({
        events: {
            "click .fre-chosen-multi": "chosenClick",
            // user register
            'submit form.create_portfolio': 'createPortfolio',
            'submit form.update_portfolio': 'updatePortfolio',
            'submit form.form_delete_portfolio': 'deletePortfolio',
            'submit form.form_delete_meta_history': 'deleteMetaHistory'
        },
        /**
         * init view setup Block Ui and Model User
         */
        initialize: function () {
            this.user = AE.App.user;
            this.blockUi = new Views.BlockUi();
            this.initValidator();

            // upload file portfolio image
            var view = this;
            var author_id = view.user.get('ID');

            $('#modal_add_portfolio').on('shown.bs.modal', function () {
                this.uploaderID = 'portfolio_img';
                var upload_id = this.uploaderID;
                var $container = $("#portfolio_img_container");
                if (typeof this.portfolio_uploader === "undefined") {
                    this.portfolio_uploader = new AE.Views.File_Uploader({
                        el: $container,
                        uploaderID: upload_id,
                        drop_element: 'portfolio_img_container',
                        thumbsize: 'portfolio',
                        multi_selection: true,
                        multipart_params: {
                            _ajax_nonce: $container.find('.et_ajaxnonce').attr('data-id'),
                            data: {
                                method: 'add_portfolio',
                                author: author_id
                            },
                            imgType: upload_id
                        },
                        extensions: 'pdf,doc,docx,png,jpg,gif,zip',
                        cbUploaded: function (up, file, res) {
                            if (res.success) {
                                var html_img = html_imge_portfolio(res.data.portfolio[0], res.data.attach_id);

                                $(this.container).closest('.box_upload_img').find(".ctn_portfolio_img").append(html_img);
                                $(this.container).append('<input type="hidden" class="input_thumbnail_' + res.data.attach_id + '" name="post_thumbnail[' + res.data.attach_id + ']" value="' + res.data.attach_id + '">');

                                $(this.container).parents('.desc').find('.error').remove();
                            } else {
                                $(this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                            }
                        },
                        beforeSend: function (ele) {
                            var button = $(ele);
                            view.blockUi.block(button);
                        },
                        success: function (res) {
                            if (res.success === false) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error',
                                });
                            }
                            view.blockUi.unblock();
                        }
                    });

                    var sAgent = window.navigator.userAgent,
                        Ya = sAgent.indexOf("YaBrowser"),
                        iPad = sAgent.indexOf('iPad');
                    // Browser is Yandex
                    if (Ya > 0 || iPad > 0) {
                        this.portfolio_uploader.controller.init();
                        this.portfolio_uploader.controller.refresh();
                    }
                }
            });

            $('#modal_edit_portfolio').on('shown.bs.modal', function () {
                var obj = $(this);
                var data = view.collection;
                view.collection = false;
                if (data) {
                    $('input[name="post_title"]', obj).val(data.post_title);
                    $('input[name="ID"]', obj).val(data.ID);
                    $('textarea[name="post_content"]', obj).val(data.unfiltered_content);
                    $('.list_skill', obj).html(data.html_edit_select_skill);
                    $('.list_profile_category', obj).html(data.html_edit_select_profile_category);

                    if (data.html_edit_best_work == 1) {
                        $('.input_best_work', obj)[0].checked = true;
                        $('.input_best_work').siblings().addClass('active');
                    } else $('.input_best_work', obj)[0].checked = false;
                    if (data.html_edit_client == 1) {
                        $('.input_client', obj)[0].checked = true;
                        $('.input_client').siblings().addClass('active');
                    } else $('.input_client', obj)[0].checked = false;

                    var list_id_image_portfolio_edit = '';
                    var list_image_portfolio_edit = '';
                    if (data.list_image_portfolio && data.list_image_portfolio.length > 0) {
                        $(data.list_image_portfolio).each(function (kip, vip) {
                            list_id_image_portfolio_edit += '<input type="hidden" name="post_thumbnail[' + vip.id + ']"' +
                                'value="' + vip.id + '"' +
                                'class="input_thumbnail_' + vip.id + '">';

                            list_image_portfolio_edit += '<li class="col-sm-3 col-xs-12"> ' +
                                '<div class="portfolio-thumbs-wrap"> ' +
                                '<div class="portfolio-thumbs"> ' +
                                '<img src="' + vip.image + '"> ' +
                                '</div> ' +
                                '<div class="portfolio-thumbs-action"> ' +
                                '<a href="javascript:void(0)" class="remove_image_in_portfolio"' +
                                'data-image_id="' + vip.id + '"> ' +
                                '<i class="fa fa-trash-o"></i>Remove' +
                                '</a> ' +
                                '</div> ' +
                                '</div> ' +
                                '</li>';
                        });
                    }
                    $('.ctn_portfolio_img', obj).html(list_image_portfolio_edit);
                    $('.list_id_image', obj).html(list_id_image_portfolio_edit);
                    $('.fre-chosen-multi', obj).click();

                    this.uploaderID = 'edit_portfolio_img';
                    var upload_id = this.uploaderID;
                    var $container = $("#edit_portfolio_img_container");
                    if (typeof this.portfolio_uploader_edit === "undefined") {
                        this.portfolio_uploader_edit = new AE.Views.File_Uploader({
                            el: $container,
                            uploaderID: upload_id,
                            drop_element: 'edit_portfolio_img_container',
                            multi_selection: true,
                            thumbsize: 'portfolio',
                            multipart_params: {
                                _ajax_nonce: $container.find('.et_ajaxnonce').attr('data-id'),
                                data: {
                                    method: 'add_portfolio',
                                    author: author_id
                                },
                                imgType: upload_id
                            },
                            extensions: 'pdf,doc,docx,png,jpg,gif,zip',
                            cbUploaded: function (up, file, res) {
                                if (res.success) {
                                    var html_img = html_imge_portfolio(res.data.portfolio[0], res.data.attach_id);

                                    $(this.container).closest('.box_upload_img').find(".ctn_portfolio_img").append(html_img);
                                    $('.list_id_image', this.container).append('<input type="hidden" class="input_thumbnail_' + res.data.attach_id + '" name="post_thumbnail[' + res.data.attach_id + ']" value="' + res.data.attach_id + '">');

                                    $(this.container).parents('.desc').find('.error').remove();
                                } else {
                                    $(this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                                }
                            },
                            beforeSend: function (ele) {
                                var button = $(ele);
                                view.blockUi.block(button);
                            },
                            success: function (res) {
                                if (res.success === false) {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: res.msg,
                                        notice_type: 'error',
                                    });
                                }


                                view.blockUi.unblock();
                            }
                        });

                        var sAgent = window.navigator.userAgent,
                            Ya = sAgent.indexOf("YaBrowser"),
                            iPad = sAgent.indexOf('iPad');
                        // Browser is Yandex
                        if (Ya > 0 || iPad > 0) {
                            this.portfolio_uploader_edit.controller.init();
                            this.portfolio_uploader_edit.controller.refresh();
                        }
                    }
                }

            });

            $('#modal_view_portfolio').on('shown.bs.modal', function () {
                var obj = $(this);
                var data = view.collection;
                view.collection = false;
                if (data) {
                    $('.post_title', obj).text(data.post_title);
                    $('.post_content', obj).html(data.post_content);

                    var html_profile_category = '';
                    if (data.tax_input.profile_category && data.tax_input.profile_category.length > 0) {
                        $(data.tax_input.profile_category).each(function (ks, vs) {
                            if (vs.name) {
                                html_profile_category += '<span class="fre-label">' + vs.name + '</span>';
                            }
                        })
                    }
                    $('.list_profile_category', obj).html(html_profile_category);

                    var html_skil = '';
                    if (data.tax_input.skill && data.tax_input.skill.length > 0) {
                        $(data.tax_input.skill).each(function (ks, vs) {
                            if (vs.name) {
                                html_skil += '<span class="fre-label">' + vs.name + '</span>';
                            }
                        })
                    }
                    $('.list_skill', obj).html(html_skil);

                    var html_img = '';
                    if (data.list_image_portfolio) {
                        $(data.list_image_portfolio).each(function (ki, vi) {
                            html_img += '<img src="' + vi.image + '">';
                        })
                    }
                    $('.list_image', obj).html(html_img);
                }

            });

            $('#modal_delete_portfolio').on('shown.bs.modal', function () {
                var obj = $(this);
                var id = view.collection;
                $('input[name="ID"]', obj).val(id);
            });

            $('#modal_delete_meta_history').on('shown.bs.modal', function () {
                var obj = $(this);
                var id = view.collection.id;
                var last = view.collection.last;
                $('form', obj).attr('data-last', last);
                $('input[name="ID"]', obj).val(id);
            });

            function html_imge_portfolio($img_url, $img_id) {
                var html_img = '<li class="col-sm-3 col-xs-12">' +
                    '<div class="portfolio-thumbs-wrap">' +
                    '<div class="portfolio-thumbs">' +
                    '<img src="' + $img_url + '">' +
                    '</div>' +
                    '<div class="portfolio-thumbs-action">' +
                    '<a href="javascript:void(0)" class="remove_image_in_portfolio" data-image_id="' + $img_id + '"><i class="fa fa-trash-o"></i>Remove</a>' +
                    '</div>' +
                    '</div>' +
                    '</li>';

                return html_img;
            }

        },
        setModel: function (model, profile) {
            this.portfolio = model; //new Models.Portfolio();
            this.profile = profile;
            this.setupFields();
        },
        setupFields: function () {
            var view = this;
            this.$('.form-group').find('input').each(function () {
                $(this).val(view.portfolio.get($(this).attr('name')));
            });
        },
        resetUploader: function () {
            if (typeof this.portfolio_uploader === 'undefined') return;
            this.portfolio_uploader.controller.splice();
            this.portfolio_uploader.controller.refresh();
            this.portfolio_uploader.controller.destroy();


            if (typeof this.portfolio_uploader_edit === 'undefined') return;
            this.portfolio_uploader_edit.controller.splice();
            this.portfolio_uploader_edit.controller.refresh();
            this.portfolio_uploader_edit.controller.destroy();
        },
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function () {
            /**
             * register rule
             */
            this.portfolio_validator = $("form.create_portfolio").validate({
                rules: {
                    post_title: "required",
                    post_content: "required",
                    post_thumbnail: "required"
                }
            });
        },
        /**
         * user sign-up catch event when user submit form signup
         */
        createPortfolio: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('button.fre-submit-portfolio'),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                // view.portfolio.set($(this).attr('name'), $(this).val());
                // if ($(this)[0].name == 'best_work' && $(this)[0].checked == false) {
                if ($(this)[0].type == 'checkbox' && $(this)[0].checked == false) {
                    view.portfolio.set($(this).attr('name'), 0);
                } else {
                    view.portfolio.set($(this).attr('name'), $(this).val());
                }
            });
            // check if user has selected an image!
            if ($("#post_thumbnail").val() == "0") {
                AE.pubsub.trigger('ae:notification', {
                    msg: fre_fronts.portfolio_img,
                    notice_type: 'error'
                });
                return false;
            }
            // check form validate and process sign-up
            if (this.portfolio_validator.form() && !form.hasClass("processing")) {
                this.portfolio.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (portfolio, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:portfolio:create', portfolio, status, jqXHR);

                        if (status.success) {
                            // add to collection
                            /*view.collection.add(portfolio, {
                                at: 0
                            });*/
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success'
                            });
                            // close modal
                            view.closeModal();
                            window.location.href = '/profile#settings';

                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        },

        updatePortfolio: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var form = $(event.currentTarget),
                button = form.find('button.fre-submit-portfolio'),
                view = this;

            /**
             * call validator init
             */
            //this.initValidator();
            this.edit_portfolio_validator = form.validate({
                ignore: "",
                rules: {
                    "post_title": "required",
                    "post_content": "required"
                }
            });

            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                // view.portfolio.set($(this).attr('name'), $(this).val());
                // if ($(this)[0].name == 'best_work' && $(this)[0].checked == false) {
                if ($(this)[0].type == 'checkbox' && $(this)[0].checked == false) {
                    view.portfolio.set($(this).attr('name'), 0);
                } else {
                    view.portfolio.set($(this).attr('name'), $(this).val());
                }
            });
            // check if user has selected an image!
            if ($("#post_thumbnail").val() == "0") {
                AE.pubsub.trigger('ae:notification', {
                    msg: fre_fronts.portfolio_img,
                    notice_type: 'error'
                });
                return false;
            }
            // check form validate and process sign-up
            if (this.edit_portfolio_validator.form() && !form.hasClass("processing")) {
                //this.portfolio.set('method', 'update');
                this.portfolio.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function (portfolio, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:portfolio:update', portfolio, status, jqXHR);

                        if (status.success) {
                            // add to collection
                            /*view.collection.add(portfolio, {
                                at: 0
                            });*/
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success'
                            });
                            // close modal
                            view.closeModal();
                            window.location.href = '/profile#settings';
                            // reset form
                            // form.reset();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        },

        deletePortfolio: function (event) {
            event.preventDefault();
            var obj = $(event.currentTarget);
            var view = this;
            var id = $('input[name="ID"]', obj).val();
            var processing = obj.attr('data-processing');
            if (processing == 'no') {
                obj.attr('data-processing', 'yes');
                $.ajax({
                    type: "post",
                    url: ae_globals.ajaxURL,
                    dataType: 'json',
                    data: {
                        action: 'ae-portfolio-sync',
                        method: 'remove',
                        ID: id
                    },
                    beforeSend: function () {
                        obj.attr('disabled', true).css('opacity', '0.5');
                        view.blockUi.block(obj);
                    },
                    success: function (data, statusText, xhr) {
                        if (data.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'success'
                            });
                            $('#portfolio_item_' + id).closest('li').remove();

                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'error'
                            });
                        }
                        view.closeModal();
                        obj.attr('disabled', false).css('opacity', '1');
                        view.blockUi.unblock();
                    }
                });
            }
        },
        deleteMetaHistory: function (event) {
            event.preventDefault();
            var obj = $(event.currentTarget);
            var view = this;
            var id = $('input[name="ID"]', obj).val();
            var last = obj.attr('data-last');
            var processing = obj.attr('data-processing');
            if (processing == 'no') {
                obj.attr('data-processing', 'yes');
                $.ajax({
                    type: "post",
                    url: ae_globals.ajaxURL,
                    dataType: 'json',
                    data: {
                        action: 'ae-profile-delete-meta',
                        ID: id
                    },
                    beforeSend: function () {
                        obj.attr('disabled', true).css('opacity', '0.5');
                        view.blockUi.block(obj);
                    },
                    success: function (data, statusText, xhr) {
                        if (data.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'success'
                            });
                            if (last == 3) {
                                $('.meta_history_item_' + id).closest('.fre-profile-box').find('.fre-empty-optional-profile').fadeIn();
                            }
                            $('.meta_history_item_' + id).remove();

                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'error'
                            });
                        }
                        view.closeModal();
                        obj.attr('disabled', false).css('opacity', '1');
                        view.blockUi.unblock();
                    }
                });
            }
        },

        //reset js skill
        chosenClick: function (event) {
            if ($(event.currentTarget).attr('data-first_click') == 'true') {
                $(event.currentTarget).attr('data-first_click', 'false');
                $(event.currentTarget).chosen({
                    width: '100%',
                    inherit_select_classes: true
                });
            }
        }
    });

    Views.Modal_Upload_Avatar = Views.Modal_Box.extend({
        events: {
            "submit .form-save-avatar": "saveAvatar"
        },
        initialize: function () {
            this.user = AE.App.user;
            this.blockUi = new Views.BlockUi();
            var view = this;

            $('#uploadAvatar').on('shown.bs.modal', function () {
                var obj = $(this);
                this.uploaderID = 'md_user_avatar';
                var $container = $("#md_user_avatar_container");
                var $container_croper = $("#container_crop_avatar");
                $container_croper.find('.avatar-default').attr('height', '').attr('width', '');
                var is_crop = $container_croper.attr('data-is_crop');
                if (is_crop == 'false') {
                    $container_croper.attr('data-is_crop', 'true');
                    var html_img = $container_croper.html();
                    view.imgCropper = $container_croper.find('img');
                    view.imgCropper.show();
                    view.attach_id = $container.attr('data-avatar_id');
                    view.imgCropper.cropper({
                        aspectRatio: 1 / 1,
                        zoomable: false,
                        background: false,
                        modal: false,
                        scalable: false,
                        rotatable: false,
                        minCropBoxWidth: 150,
                        minCropBoxHeight: 150,
                        crop: function (e) {
                            // Output the result data for cropping image.
                            view.cropX = e.x;
                            view.cropY = e.y;
                            view.cropWidth = e.width;
                            view.cropHeight = e.height;
                        }
                    });
                }
                //init avatar upload
                if (typeof this.avatar_uploader === "undefined") {
                    this.avatar_uploader = new AE.Views.File_Uploader({
                        el: $container,
                        uploaderID: this.uploaderID,
                        thumbsize: 'thumbnail',
                        multipart_params: {
                            _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                            data: {
                                //method: 'change_avatar',
                                method: 'upload_image',
                                author: view.user.get('ID')
                            },
                            imgType: this.uploaderID,
                        },
                        cbUploaded: function (up, file, res) {
                            if (res.success) {
                                $(this.container).parents('.desc').find('.error').remove();
                                view.attach_id = res.data.attach_id;
                                var imgURL = res.data.full[0];

                                $container_croper.html(html_img);
                                view.imgCropper = $container_croper.find('img');
                                view.imgCropper.attr('src', imgURL);
                                view.imgCropper.show();
                                view.imgCropper.cropper({
                                    aspectRatio: 1 / 1,
                                    zoomable: false,
                                    background: false,
                                    modal: false,
                                    scalable: false,
                                    rotatable: false,
                                    minCropBoxWidth: 150,
                                    minCropBoxHeight: 150,
                                    crop: function (e) {
                                        // Output the result data for cropping image.
                                        view.cropX = e.x;
                                        view.cropY = e.y;
                                        view.cropWidth = e.width;
                                        view.cropHeight = e.height;
                                    }
                                });
                            } else {
                                $(this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                            }
                        },
                        beforeSend: function (ele) {
                            var button = $(ele).closest('form');
                            view.blockUi.block(button);
                        },
                        success: function (res) {
                            if (res.success === false) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error',
                                });
                            }
                            view.blockUi.unblock();
                        }
                    });
                }
            });
        },

        saveAvatar: function (envent) {
            envent.preventDefault();
            var obj = $(envent.currentTarget);
            var view = this;

            var processing = obj.attr('data-processing');
            if (processing == 'no') {
                obj.attr('data-processing', 'yes');
                $.ajax({
                    type: "post",
                    url: ae_globals.ajaxURL,
                    dataType: 'json',
                    data: {
                        action: 'fre_crop_avatar',
                        crop_x: view.cropX,
                        crop_y: view.cropY,
                        crop_width: view.cropWidth,
                        crop_height: view.cropHeight,
                        attach_id: view.attach_id,
                        user_id: view.user.get('ID')
                    },
                    beforeSend: function () {
                        obj.attr('disabled', true).css('opacity', '0.5');
                        view.blockUi.block(obj);
                    },
                    success: function (data, statusText, xhr) {
                        if (data.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'success'
                            });

                            $('.avatar').attr('src', data.data.thumbnail[0]);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: data.msg,
                                notice_type: 'error'
                            });
                        }

                        view.blockUi.unblock();
                        view.closeModal();
                        obj.attr('disabled', false).css('opacity', '1');
                    }
                });
            }

        }
    })

})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
