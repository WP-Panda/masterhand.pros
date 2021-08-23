jQuery(function ($) {

    //remove profile skill list placeholder
    if ($('#list_skills_user .item-list-skills').is(':visible')) {
        $('.skill-list__placeholder').remove();
    }
    ;
    $('#fre-post-project #country').change(function () {
        var user_default_country_id = $(this).attr('data_user_country_id');
        $(this).siblings('.message').remove();
        if ($(this).val() != user_default_country_id) {
            $(this).parent().append('<div for="sub" class="message">Country warning message</div>')
        }

    })

    $("#user_answer_list input[type='radio']").change(function () {

        if ($(this).val().toLowerCase() == 'other') {
            $('#user_answer_commit').removeAttr('disabled');

        } else {
            $('#user_answer_commit').attr('disabled', 'disabled');
        }
    })


    $('#unsubscribe_form_submit').click(function () {
        var recaptcha = $("#g-recaptcha-response").val();
        var user_email = $('#unsubscribe_form input[type="email"]').val();
        var user_answer = $("#user_answer_list input[type='radio']:checked").val();

        if (user_answer.toLowerCase() == 'other') {
            $('#user_answer_commit').removeAttr('disabled');
            user_answer = $('#user_answer_commit').val();

        }
        $('.page-template .notification').remove();
        $.ajax({
            type: 'POST',
            url: '/wp-content/themes/freelanceengine/unsubscribe_users.php',
            data: {
                'user_email': user_email,
                'user_answer': user_answer,
                'g-recaptcha-response': recaptcha
            },
            success: function (data) {

                if (data.response == 'error' && data.error_type == 'email') {
                    $('.page-template').append('<div class="notification autohide error-bg having-adminbar">' + data.text + '</div>');
                } else if (data.response == 'success') {

                    $('.page-template').append('<div class="notification autohide success-bg">' + data.text + '</div>');

                } else {
                    $('.page-template').append('<div class="notification autohide error-bg having-adminbar">' + data.text + '</div>');

                }

                setTimeout(function () {
                    $('.page-template .notification').remove();
                    $('#unsubscribe_form input[type="email"]').val('');
                    grecaptcha.reset();
                }, 3000)

            }
        });

    })

    var project_category_chosen = $('select[name="project_category"]'),
        limit = project_category_chosen.data('limit');

    if ($('.chosen-search-input').length !== 0) {
        /**
         * Select categories on the profile page
         */
        var $chosen = project_category_chosen.chosen({
            max_selected_options: limit,
        });

        $chosen.change(function () {
            $('.chosen-search-input').attr('style', 'display:none;');
            var $this = $(this);
            var chosen = $this.data('chosen');
            var search = chosen.search_container.find('input[type="text"]');
            search.prop('disabled', $this.val() !== null);
            if (chosen.active_field) {
                search.focus();
            }
        });
    } else { // for mobile
        var select_col = $('#category_form select[name=project_category] option:selected').length;
        if (select_col > limit) {
            $('#category_form select[name=project_category] option:not(:selected)').prop('disabled', true)
            $('form[id=category_form] .btn-submit').prop('disabled', true);
            $('form[id=category_form] .btn-submit').css('opacity', 0.5);
        }
        if (select_col === limit) {
            $('#category_form select[name=project_category] option:not(:selected)').prop('disabled', true)
        }

        $('#category_form select[name=project_category]').change(function () {
            select_col = $('#category_form select[name=project_category] option:selected').length;
            if (select_col === limit) {
                $('.err').remove();
                $('form[id=category_form] .btn-submit').prop('disabled', false);
                $('form[id=category_form] .btn-submit').css('opacity', 1);
                $('#category_form select[name=project_category] option:not(:selected)').prop('disabled', true)
            } else {
                if (select_col > limit) {
                    $('<span class="err" style="color:red">You select - ' + select_col + ', max - ' + limit + ' specializations</span>').insertBefore('#category_form select[name=project_category]');
                    $('#category_form select[name=project_category] option:not(:selected)').prop('disabled', true);
                    $('form[id=category_form] .btn-submit').prop('disabled', true);
                    $('form[id=category_form] .btn-submit').css('opacity', 0.5);
                } else {
                    $('.err').remove();
                    $('form[id=category_form] .btn-submit').prop('disabled', false);
                    $('form[id=category_form] .btn-submit').css('opacity', 1);
                    $('#category_form select[name=project_category] option:not(:selected)').prop('disabled', false)
                }
            }
        })
    }


    /**
     * Get the location on the profile filter
     */
    function getAllUrlParams(url) {
        var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
        var obj = {};
        if (queryString) {
            queryString = queryString.split('#')[0];
            var arr = queryString.split('&');
            for (var i = 0; i < arr.length; i++) {
                var a = arr[i].split('=');
                var paramName = a[0];
                var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

                paramName = paramName.toLowerCase();
                if (typeof paramValue === 'string') paramValue = paramValue.toLowerCase();
                if (paramName.match(/\[(\d+)?\]$/)) {
                    var key = paramName.replace(/\[(\d+)?\]/, '');
                    if (!obj[key]) obj[key] = [];

                    if (paramName.match(/\[\d+\]$/)) {
                        var index = /\[(\d+)\]/.exec(paramName)[1];
                        obj[key][index] = paramValue;
                    } else {
                        obj[key].push(paramValue);
                    }
                } else {
                    if (!obj[paramName]) {
                        obj[paramName] = paramValue;
                    } else if (obj[paramName] && typeof obj[paramName] === 'string') {
                        obj[paramName] = [obj[paramName]];
                        obj[paramName].push(paramValue);
                    } else {
                        obj[paramName].push(paramValue);
                    }
                }
            }
        }
        return obj;
    }

    if (getAllUrlParams().country) {
        if (!$('body').hasClass('no-bb-paginate')) {
            var cc = getAllUrlParams().country;
            $('#country option[value="' + cc + '"]').attr('selected', 'selected');
            $.ajax({
                type: 'POST',
                url: '/ajaxData.php',
                data: {
                    'country_id': cc
                },
                beforeSend: function () {
                    $('.loading-blur.loading.hidden').removeClass('hidden');
                },
                success: function (html) {
                    $('#state').html(html);
                    $('#city').html('<option value="">Select state first</option>');
                    if (getAllUrlParams().state) {
                        var cc2 = getAllUrlParams().state;
                        $('#state option[value="' + cc2 + '"]').attr('selected', 'selected');
                        $.ajax({
                            type: 'POST',
                            url: '/ajaxData.php',
                            data: 'state_id=' + cc2,
                            success: function (html) {
                                $('#city').html(html);
                                if (getAllUrlParams().city) {
                                    var cc3 = getAllUrlParams().city;
                                    $('#city option[value="' + cc3 + '"]').attr('selected', 'selected');
                                    $('#city').change();
                                    $('body').click();
                                    $('.fre-profile-list-box .loading-blur.loading').addClass('hidden');
                                } else {
                                    $('#state').change();
                                    $('body').click();
                                    $('.fre-profile-list-box .loading-blur.loading').addClass('hidden');
                                } //if city
                            }
                        });
                    } else {
                        $('#country').change();
                        $('body').click();
                        $('.fre-profile-list-box .loading-blur.loading').addClass('hidden');
                    } // if state
                }
            });
        }
    } // if country url


    $('document').ready(function () {


        var status_save_city = true;

        function isJsonString(str) {
            try {
                return JSON.parse(str);
            } catch (e) {
                return false;
            }
        }

        // Get location on the profile page
        if ($('body').hasClass('page-template-page-profile-php') ||
            $('body').hasClass('post-type-archive') ||
            $('body').hasClass('page-template-page-register') ||
            $('body').hasClass('page-template-page-proffessionals') ||
            $('body').hasClass('page-template-page-projects') ||
            $('body').hasClass('page-template-page-submit-project') ||
            $('body').hasClass('page-template-special_offers') ||
            $('body').hasClass('advert-template-default') ||
            $('body').hasClass('page-template-page-create-advert')) {

            var selected_countryID = '', selected_stateID = '', selected_cityID = '';

            if ($('input[name=change_country]').val() && $('input[name=change_country]').val() != '' && $('input[name=change_country]').val() != $('#country').data('selected_id')) {
                selected_countryID = $('input[name=change_country]').val()
                if ($('input[name=change_state]').val() != '' && $('input[name=change_state]').val() != $('#state').data('selected_id')) {
                    selected_stateID = $('input[name=change_state]').val()
                    if ($('input[name=change_city]').val() != '' && $('input[name=change_city]').val() != $('#city').data('selected_id')) {
                        selected_cityID = $('input[name=change_state]').val()
                    }
                }
            } else {
                if ($('#country').data('selected_id')) {
                    selected_countryID = $('#country').data('selected_id')
                    if ($('#state').data('selected_id')) {
                        selected_stateID = $('#state').data('selected_id')
                        if ($('#city').data('selected_id')) {
                            selected_cityID = $('#city').data('selected_id')
                        }
                    }
                }
            }

            // var view = this;

            if (selected_countryID) {
                str = 'country_id=' + selected_countryID
                if (selected_stateID) {
                    str = str + '&state_id=' + selected_stateID
                    if (selected_cityID) {
                        str = str + '&city_id=' + selected_cityID
                    }
                }

                $.ajax({
                    type: 'POST',
                    url: ajax_var.url,
                    data: str + '&action=user_location',
                    success: function (list) {
                        $('#country').val(selected_countryID);
                        var select = isJsonString(list);
                        if (!select) {
                            $('#state').html(list);
                            $('#city').html('<option value="">Select state first</option>');
                        } else {
                            $('#state').html(select.state);
                            $('#city').html(select.city);
                        }
                        status_save_city = false;
                        $('#city').change();
                    }
                });


            } else {
                $('#state').html('<option value="">Select country first</option>');
                $('#city').html('<option value="">Select state first</option>');
            }

            $('input[type=radio][name=type_prof]').change(function () {
                if (this.value === 'profession') {
                    $('input[name=company_name]').parent().hide();
                    $('input[name=first_name]').parent().show();
                    $('input[name=last_name]').parent().show()
                } else if (this.value === 'company') {
                    $('input[name=company_name]').parent().show();
                    $('input[name=first_name]').parent().hide();
                    $('input[name=last_name]').parent().hide()
                }
            })
        }

        //////////////////////////////////////////////////////////////////////REMOVE
        $('#country').on('change', function () {
            if (!$('body').hasClass('no-bb-paginate')) {

                var countryID = $(this).val();
                if (countryID) {
                    $('input[name=change_country]').val(countryID);
                    $.ajax({
                        type: 'POST',
                        url: ajax_var.url,
                        data: {
                            'action': 'user_location',
                            'country_id': countryID,
                            // 'nonce': ajax_var.nonce
                        },
                        success: function (html) {
                            $('#state').html(html);
                            $('#state').prop('disabled', '')
                                .parent().removeClass('disabled');
                            $('#city').html('<option value="">Select state first</option>');
                        }
                    });
                } else {
                    $('#state').html('<option value="">Select country first</option>');
                    $('#city').html('<option value="">Select state first</option>');
                }
            }
        });


        //////////////////////////////////////////////////////////////////////REMOVE
        $('#state').on('change', function () {
            if (!$('body').hasClass('no-bb-paginate')) {

                var stateID = $(this).val();
                if (stateID) {
                    $('input[name=change_state]').val(stateID);
                    $.ajax({
                        type: 'POST',
                        url: ajax_var.url,
                        data: {
                            'action': 'user_location',
                            'state_id': stateID,
                            // 'nonce': ajax_var.nonce,
                        },
                        success: function (html) {
                            $('#city').html(html);
                            $('#city').prop('disabled', '')
                                .parent().removeClass('disabled');
                        }
                    });
                } else {
                    $('#city').html('<option value="">Select state first</option>');
                }
                status_save_city = true
            }
        });


        //////////////////////////////////////////////////////////////////////REMOVE
        $('#city').on('change', function () {
            if (!$('body').hasClass('no-bb-paginate')) {
                if (status_save_city) {
                    var cityID = $(this).val();
                    if (cityID) {
                        if ($('#city').data('selected_id') != cityID) {
                            $('input[name=change_city]').val(cityID)
                        }
                    }
                }
            }
        });

        //////////////////////////////////////////////////////////////////////REMOVE
        function getCatSub(data) {
            if (!$('body').hasClass('no-bb-paginate')) {
                if (data) {
                    $.ajax({
                        type: 'POST',
                        url: ajax_var.url,
                        data: data,
                        success: function (html) {
                            var list = $.parseHTML(html)

                            if (html == '0') {
                                $('#sub').html('<option value="">Select category first</option>');
                            } else if (list[0].length == 0) {
                                $('select#sub option').remove()
                                $('select#sub').append(new Option("No subcategories", ""));
                            } else {
                                $('#sub').html(html);
                                if ($('body').hasClass('page-template-page-submit-project')) {
                                    $("#sub option[value='']").remove();
                                    $('#sub').trigger('chosen:updated')
                                }
                            }
                        }
                    })
                } else {
                    $('#sub').html('<option value="">Select category first</option>');
                }
            }
        }

        //////////////////////////////////////////////////////////////////////REMOVE
        $('#cat').on('change', function () {
            if (!$('body').hasClass('no-bb-paginate')) {
                var str
                if ($(this).val() !== '') {
                    str = 'cat_slug=' + $(this).val();

                    var type_filter = '&type_filter=' + $('input[name=type_filter]').val();

                    if ($('input[name=crete_project]').val() == 1)
                        str = str + '&crete_project=1&step_valid=1';

                    str = str + type_filter + '&action=get_sub_cat'
                } else str = '';

                getCatSub(str)
            }
        });

        if ($('body').hasClass('page-template-page-projects') ||
            $('body').hasClass('page-template-page-proffessionals') ||
            $('body').hasClass('page-template-page-submit-project')) {
            var selected_cat_slug = $('#cat').data('selected_slug');
            var selected_sub_slug = $('#sub').data('selected_slug');
            var type_filter = '&type_filter=' + $('input[name=type_filter]').val();

            if (selected_cat_slug) {
                $('#cat option[value=' + selected_cat_slug + ']').prop('selected', true)
                var str = 'cat_slug=' + selected_cat_slug
                if (selected_sub_slug) {
                    str = str + '&sub_slug=' + selected_sub_slug
                }
                str = str + type_filter + '&action=get_sub_cat'

                getCatSub(str)
            }
        }

        // определение какой тарифный план выбран, на сколько дней
        if ($('body').hasClass('page-template-page-submit-project')) {
            $('.chose-plan').click(function () {
                getMaxDays();

                if ($('#create_project_for_all')[0].checked == true) {
                    change_days_for_create_project_for_all()
                }
            });

            function getMaxDays() {
                var max_days = 0;
                var arr = $.parseJSON($('script#package_plans')[0].innerHTML);
                var id = $('input[name=post-package]:checked')[0].id.replace('package-', '');

                $.each(arr, function (key, value) {
                    if (value.ID == id) {
                        max_days = value.et_duration
                    }
                });

                $("input[name=days_active_project]")[0].dataset.max_days = max_days
            }
        }

        if ($('body').hasClass('page-template-page-options-project')) {
            var postdata = $.parseJSON($('script#opt_on')[0].innerHTML);
            console.info(postdata);
            $.each(postdata, function (key1, item1) {
                console.info(key1,item1);
                var arr = $("#pro_functions input[type='checkbox']")
                $.each(arr, function (key, item) {
                    if (item.id == item1.name) {
                        this.checked = true
                        var str = "<div><span><b>No change! Option active until " + item1.et_date + "</b></span></div>";
                        $(this).parent().after(str)
                        $(this).val('on')
                        $(this).attr('disabled', 'disabled')
                        $(this).parent().addClass('active')
                        key1++
                    }
                })
            })
        }

        $('#create_project_for_all').change(function () {
            if (this.checked) {
                change_days_for_create_project_for_all()
            } else
                $("#_create_project_for_all").remove()
        });
        $('#priority_in_list_project').change(function () {
            if (this.checked) {
                change_days_for_active_pro_func('priority_in_list_project')
            } else
                $("#_priority_in_list_project").remove()
        });
        $('#highlight_project').change(function () {
            if (this.checked) {
                change_days_for_active_pro_func('highlight_project')
            } else
                $("#_highlight_project").remove()
        });
        $('#urgent_project').change(function () {
            if (this.checked) {
                change_days_for_active_pro_func('urgent_project')
            } else
                $("#_urgent_project").remove()
        });
        $('#hidden_project').change(function () {
            if (this.checked) {
                change_days_for_active_pro_func('hidden_project')
            } else
                $("#_hidden_project").remove()
        });

        function change_days_for_create_project_for_all() {
            var flag = false,
                str = '';
            var arr = $.parseJSON($('script#pro_em_functions')[0].innerHTML);
            var name_func = 'create_project_for_all';
            if (arr.length !== 0) {
                $.each(arr, function (key, value) {
                    if (value.sku == name_func) {
                        flag = true;

                        if ($('#price_' + name_func).val() == '') // for page edit options
                            $('#price_' + name_func).val($('#price_' + name_func)[0].dataset.price_option)

                        var max_days = $("input[name=days_active_project]")[0].dataset.max_days
                        var last_day = false

                        if (max_days == 0) {
                            last_day = true;
                            max_days = 1;
                        }

                        var price = 'free';
                        $('#' + name_func).val(max_days);
                        if ($('#price_' + name_func).val() !== 'free') {
                            price = max_days * $('#price_' + name_func).val() + " $";
                        }

                        str = "<div id='_" + name_func + "'>" +
                            "<input id='days_" + name_func + "' name='days_" + name_func + "' type='hidden' value=" + 1 + ">" +
                            "<span id='total_price_" + name_func + "'>";

                        if (last_day)
                            str = str + "Today is the last day of the active project. Price - ";
                        else
                            str = str + "Price for " + max_days + " days - ";
                        str = str + "<b>" + price + "</b></span></div>";
                        $('#' + name_func).parent().after(str)

                    }
                });
            }
            if (!flag) {
                $('#' + name_func).parent().attr('title', 'Option not active');

                console.error('Need to create a plan. Engine Settings - Settings - Payment where sku=' + name_func)
                //$('#' + name_func).removeAttr("checked");
            }
        }

        function change_days_for_active_pro_func(name_func) {
            var flag = false,
                str = '';
            var arr = $.parseJSON($('script#pro_em_functions')[0].innerHTML);

            if (arr.length !== 0) {
                $.each(arr, function (key, value) {
                    if (value.sku == name_func) {
                        flag = true;

                        if ($('#price_' + name_func).val() == '') // for page edit options
                            $('#price_' + name_func).val($('#price_' + name_func)[0].dataset.price_option)

                        var max_days = parseInt($("input[name=days_active_project]")[0].dataset.max_days)
                        var last_day = false

                        if (max_days == 0) {
                            last_day = true;
                            max_days = 1;
                        }
                        // if (name_func == 'create_project_for_all') {
                        //     var price = 'free';
                        //     $('#' + name_func).val(max_days);
                        //     if ($('#price_' + name_func).val() !== 'free') {
                        //         price = max_days * $('#price_' + name_func).val() + " $";
                        //     }
                        //
                        //     str = "<div id='_" + name_func + "'>" +
                        //         "<input id='days_" + name_func + "' name='days_" + name_func + "' type='hidden' value=" + 1 + ">" +
                        //         "<span id='total_price_" + name_func + "'>";
                        //
                        //     if (last_day)
                        //         str = str + "Today is the last day of the active project. Price - ";
                        //     else
                        //         str = str + "Price for " + max_days + " days - ";
                        //     str = str + "<b>" + price + "</b></span></div>";
                        //     $('#' + name_func).parent().after(str)
                        // } else {
                        $('#' + name_func).val(1);
                        str = "<div id='_" + name_func + "'>";

                        if (last_day)
                            str = str + "<label from='days_" + name_func + "'>Today is the last day of the active project</label>";
                        else
                            str = str + "<label from='days_" + name_func + "'>Enter the number of days. Max - " + max_days + " day(s)</label>";

                        str = str + "<div class='quantity'><input id='days_" + name_func + "' name='days_" + name_func + "' class='is_number numberVal'  type='number' pattern='-?(\d+|\d+.\d+|.\d+)([eE][-+]?\d+)?' placeholder='1' value='' max=" + max_days + " min='1'>" +
                            "</div>" +
                            "<span id='total_price_" + name_func + "'>Price for 1 day - <b>free</b></span></div>";
                        $('#' + name_func).parent().after(str)

                        $('#days_' + name_func).change(function () {
                            var days_select = parseInt($('#days_' + name_func).val()),
                                price_day = parseInt($('#price_' + name_func).val());
                            if (days_select == 1) {
                                $('#total_price_' + name_func).html("<span>Price for 1 day - <b>free</b></span>")
                            }
                            if (days_select > 1 && days_select <= max_days) {
                                var price = days_select * price_day;
                                $('#total_price_' + name_func).html("<span>Price for " + days_select + " day(s) - <b>" + price + " $</b></span>")
                            }
                            $('#' + name_func).val(days_select);

                            /*number-validation*/
                            var invalidChars = [
                                "-",
                                "+",
                                "e",
                            ];

                            $('#days_' + name_func).on("keydown", function (e) {
                                if (invalidChars.includes(e.key)) {
                                    e.preventDefault();
                                }
                            });

                            $('.quantity-down').each(function () {
                                $(this).click(function () {
                                    var min = $(this).siblings('input').attr('min'),
                                        oldValue = parseInt($(this).siblings('input').val()) || 1;
                                    if (oldValue <= min) {
                                        var newVal = oldValue;
                                    } else {
                                        var newVal = oldValue - 1;
                                        if (newVal <= 0) {
                                            newVal = 1;
                                        }
                                    }
                                    $(this).siblings("input").val(newVal);
                                    $(this).siblings("input").trigger("change");
                                });
                            });

                            $('.quantity-up').each(function () {
                                $(this).click(function () {
                                    var max = $(this).siblings('input').attr('max'),
                                        oldValue = parseInt($(this).siblings('input').val()) || 1;
                                    if (oldValue >= max) {
                                        var newVal = oldValue;
                                    } else {
                                        var newVal = oldValue + 1;
                                    }
                                    $(this).siblings("input").val(newVal);
                                    $(this).siblings("input").trigger("change");
                                });
                            });
                            /*number-validation-end*/

                        });
                        // }
                    }
                });
            }
            if (!flag) {
                $('#' + name_func).parent().attr('title', 'Option not active');

                console.error('Need to create a plan. Engine Settings - Settings - Payment where sku=' + name_func)
                //$('#' + name_func).removeAttr("checked");
            }
        }

        $(".fre-post-project-next-btn").click(function () {
            var arr = $("#pro_functions input[type='checkbox']:checked")
            var options_name = '',
                options_days = '',
                str = [],
                price = 0,
                price_options = 0;
            $("#options_name").val(options_name);
            $("#options_days").val(options_days);

            $.each(arr, function (key, item) {
                if (!item.disabled) {
                    if (item.value != 1 && item.value != 'on') {
                        if (options_name == '' && options_days == '') {
                            options_name = item.id;
                            options_days = item.value
                        } else {
                            options_name = [options_name, item.id];
                            options_days = [options_days, item.value]
                        }
                    }

                    if ($('#price_' + item.id)[0].value == 'free' || item.value == 1) {
                        str.push($('#' + item.id).next()[0].innerHTML + ' - free * ' + item.value + 'day(s) = free')
                    } else {
                        price = $('#price_' + item.id)[0].value * item.value;
                        price_options = price_options + price;
                        str.push($('#' + item.id).next()[0].innerHTML + ' - $' + $('#price_' + item.id)[0].value + ' * ' + item.value + 'day(s) = $' + price)
                    }
                }
            });
            //str.push('Total: $' + price_options);

            $("#options_name").val(options_name);
            $("#options_days").val(options_days);

            $('.option_title strong').html(str.join(';<br/>'));
            $('.option_title .total span').html('$' + price_options);
        });

        // });

        /* Post Project Step 3 - number-validation*/
        $('.quantity-down').each(function () {
            $(this).click(function () {
                var min = $(this).siblings('input').attr('min'),
                    oldValue = parseInt($(this).siblings('input').val()) || 1;

                if (oldValue <= min) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue - 1;
                    if (newVal <= 0) {
                        newVal = 1;
                    }
                }

                $(this).siblings("input").val(newVal);
                $(this).siblings("input").trigger("change");
            });
        });

        $('.quantity-up').each(function () {
            $(this).click(function () {
                var max = $(this).siblings('input').attr('max'),
                    oldValue = parseInt($(this).siblings('input').val()) || 1;

                if (oldValue >= max) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue + 1;
                }

                $(this).siblings("input").val(newVal);
                $(this).siblings("input").trigger("change");
            });
        });

        $('.quantity input[type=number]').each(function () {
            var invalidChars = [
                "-",
                "+",
                "e",
            ];

            /*this.addEventListener("input", function () {
                this.value = this.value.replace(/[e\+\-]/gi, "");
                if (this.value <= 0) {
                    this.value = 1;
                }
            });*/

            this.addEventListener("keydown", function (e) {
                if (invalidChars.includes(e.key)) {
                    e.preventDefault();
                }
            });

        });
        /*number-validation-end*/
    });

    //$('#pro_functions .checkline>span').click(function () {
    //    if ($(this).parent().hasClass('active') == false) {
    //        if ($(this).hasClass('active') == true) {
    //            $(this).removeClass('active')
    //                .siblings('input[type=checkbox]').removeAttr('checked');
    //        } else {
    //            $(this).addClass('active')
    //                .siblings('input[type=checkbox]').attr('checked', 'checked');
    //        }
    //        $(this).siblings('input[type=checkbox]').change();
    //    } else {
    //        return;
    //    }
    //});


    $(document).on('change', 'input[type=checkbox]', function () {
        if ($(this).parent().hasClass('checkline')) {
            var sib = $(this).siblings();
            if (sib.hasClass('active') == true) {
                sib.removeClass('active');
            } else {
                sib.addClass('active')
            }
        }
    });

    /*select all - remove all invite - to project modal*/
    $('#submit_invite .select-all').click(function () {
        $('#submit_invite .checkline').each(function () {
            if ($(this).hasClass('active') == false) {
                if ($(this).children('span').hasClass('active') == false) {
                    $(this).children('span').addClass('active');
                    $(this).children('span').siblings('input[type=checkbox]').attr('checked', 'checked');
                }
            } else {
                return;
            }
        });
    });
    $('#submit_invite .remove-all').click(function () {
        $('#submit_invite .checkline').each(function () {
            $(this).removeClass('active');
            if ($(this).children('span').hasClass('active') == true) {
                $(this).children('span').removeClass('active');
                $(this).children('span').siblings('input[type=checkbox]').removeAttr('checked');
            }
        });
    });
    /*field validation in form*/
    $('.need_valid').each(function () {
        $(this).change(function () {
            if ($(this).val() != '') {
                $(this).addClass('valid');
            } else {
                $(this).removeClass('valid');
            }
        });
    });
    /*home carousels*/
    $(".fre-stories .owl-carousel").owlCarousel({
        loop: true,
        items: 1,
        margin: 15,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650,
        responsive: {
            0: {
                items: 1,
                slideBy: 1
            },
            768: {
                items: 2,
                slideBy: 2
            }
        }
    });

    $(".fre-jobs-list").owlCarousel({
        loop: true,
        items: 1,
        margin: 15,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650,
        responsive: {
            0: {
                items: 1,
                slideBy: 1
            },
            768: {
                items: 2,
                slideBy: 2
            },
            1025: {
                items: 3,
                slideBy: 3
            }
        }
    });

    $(".home .fre-blog-list").owlCarousel({
        loop: true,
        items: 3,
        margin: 30,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            }
        }
    });

    $(".perfect-freelancer .owl-carousel").owlCarousel({
        loop: true,
        items: 1,
        margin: 15,
        animate: 'slideInRight',
        navText: '',
        autoplay: false,
        nav: true,
        smartSpeed: 650,
        responsive: {
            0: {
                items: 1,
                slideBy: 1
            },
            768: {
                items: 2,
                slideBy: 2
            },
            1025: {
                items: 3,
                slideBy: 3
            }
        }
    });
    /*blog carousels*/
    $(".fre-blog-fst_bl .owl-carousel").owlCarousel({
        loop: true,
        items: 3,
        margin: 30,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            }
        }
    });

    $(".fre-blog-snd_bl .owl-carousel").owlCarousel({
        loop: true,
        items: 1,
        margin: 0,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650
    });

    $(".fre-blog-thd_bl .owl-carousel").owlCarousel({
        loop: true,
        items: 1,
        margin: 0,
        animate: 'slideInRight',
        navText: '',
        nav: true,
        smartSpeed: 650
    });

    /*post project*/
    $(".step-post-package .chose-plan").click(function () {
        $(this).children("input").attr("checked", "checked");
        var packsleft = $(this).siblings('.desc-hidden').text();
        $(this).siblings('.desc-hidden').addClass('active');
        $(this).parent().parent().siblings().children().children('.desc-hidden.active').removeClass('active');
        $(".select-plan").trigger('click');
        $('#packinfo .pack-left').each(function () {
            $(this).children('span').text(+packsleft + ' post(s)');
        });
        $('#packinfo3 .pack-left').children('span').text(+packsleft + ' post(s)');
        $('.fre-post-project-step.step-post').addClass('active').removeClass('hidden');
        $('.fre-post-project-step.step-plan').removeClass('active').addClass('complete');
    });

    $(".fre-post-project-step.step-post .fre-submit-btn").click(function () {

        var packsleft2 = $('.step-post-package .desc-hidden.active').text();
        $('#packinfo .pack-left').each(function () {
            $(this).children('span').text(+packsleft2 + ' post(s)');
        });
        $('#packinfo3 .pack-left').children('span').text(+packsleft2 + ' post(s)');
    });

    $(".fre-post-project-step.step-payment .step-edit-project .fre-submit-btn").click(function () {
        var packsleft3 = $('.step-post-package .desc-hidden.active').text();
        $('#packinfo .pack-left').each(function () {
            $(this).children('span').text(+packsleft3 + ' post(s)');
        });
        $('#packinfo3 .pack-left').children('span').text(+packsleft3 + ' post(s)');
    });

    $('.step-change-package .fre-btn-previous').click(function () {
        $('#fre-post-project-1').addClass('active')
            .removeClass('complete');
        $('#fre-post-project-2').removeClass('active');
    });
    /*open subcats in profs cats*/
    $('.page-template-page-projects .profs-cat-list_t.faa-parent').click(function () {
        $(this).toggleClass('active');
    });

    $('.page-template-page-proffessionals .all_sub_cat').click(function () {
        $(this).children('.noactive').each(function () {
            $(this).toggleClass('yesactive');
        });
        $(this).parent().toggleClass('nowactive');
        $(this).children('.text_count').each(function () {
            $(this).children('i').toggleClass('fa-angle-down fa-angle-up');
            if ($(this).children('em').text() == 'All') {
                $(this).children('em').text('Hide');
            } else {
                $(this).children('em').text('All');
            }
        });
    });

    $('.page-template-page-projects .all_sub_cat').click(function () {
        $(this).children('.noactive').each(function () {
            $(this).toggleClass('yesactive');
        });
        $(this).parent().toggleClass('nowactive');
        $(this).children('.text_count').each(function () {
            $(this).children('i').toggleClass('fa-angle-down fa-angle-up');
            if ($(this).children('em').text() == 'All') {
                $(this).children('em').text('Hide');
            } else {
                $(this).children('em').text('All');
            }
        });
    });

    $('.more-info').click(function () {
        $(this).parents('.profile-list-desc').toggleClass('open')
            .children('.excp-txt').hide()
            .children('.scroll-pane').show();
    });

    $('.questions-list_t,.colaps .freelance-portfolio-title').click(function () {
        $(this).toggleClass('open');
    });

    $('.scroll-pane').jScrollPane();

    $('.hamburger').click(function () {
        $('body').toggleClass('cbp-spmenu-right');
    });
    /*show more skills and awards*/
    $('.author .show_more,.page-template-page-profile .show_more').click(function () {
        $(this).hide().removeClass('show')
            .siblings('.hide_more').addClass('show')
            .parent().toggleClass('opened');

    });

    var skillsh = $('.skills_awards > .skill-list').height();
    var awardsh = $('.skills_awards > .award-list').height();
    if ((skillsh > 205) || (awardsh > 205)) {
        $('.show_more').slideDown();
    }

    $('.author .hide_more,.page-template-page-profile .hide_more').click(function () {
        $(this).hide().removeClass('show')
            .siblings('.show_more').addClass('show')
            .parent().toggleClass('opened');
    });

    /*modl show contacts*/
    $('#modal_info .show-more-info').click(function () {
        $('#modal_info').removeClass('in')
            .fadeOut();
        $('body').removeClass('modal-open');
        $('.modal-backdrop').removeClass('in').remove();
        $('.info-open').hide();
        $('.hidden-contacts').removeClass('hidden');
        var header = $('header').height(),
            contacts = $('.hidden-contacts').offset().top,
            top = contacts - header - 10;
        $('body,html').animate({
            scrollTop: top
        }, 1000);
    });
    /*load more posts in blog*/
    $('#true_loadmore').click(function () {
        $(this).text('Loading...');
        var data = {
            'action': 'loadmore',
            'query': true_posts,
            'page': current_page
        };
        $.ajax({
            url: ajaxurl,
            data: data,
            type: 'POST',
            success: function (data) {
                if (data) {
                    $('#true_loadmore').text('Show more').before(data);
                    current_page++;
                    if (current_page == max_pages) $("#true_loadmore").remove();
                } else {
                    $('#true_loadmore').remove();
                }
            }
        });
    });
    /*sorting projects*/
    $('#project_orderby .option').click(function () {
        var nameoption = $(this).attr('id');
        $(this).addClass('active')
            .siblings('.option').removeClass('active');
        $("#project_orderby select option[value=" + nameoption + "]").attr('selected', 'seleted')
            .siblings('option').removeAttr('selected');
        $("#project_orderby select").change();
    });
    $('#profile_orderby .option').click(function () {
        var nameoption = $(this).attr('id');
        $(this).addClass('active')
            .siblings('.option').removeClass('active');
        $("#profile_orderby select option[value=" + nameoption + "]").attr('selected', 'seleted')
            .siblings('option').removeAttr('selected');
        $("#profile_orderby select").change();
    });
    /*text when review and no endorsments*/
    if ($('.modal-endors ul>li').length <= 0) {
        $('.modal-endors').addClass('hidden');
    }

    $('.page-benefits .nav-item:first-child').addClass('active');
    $('.page-benefits .tab-content .tab-pane:first-child').addClass('active in');

    if ($(window).width() <= 767) {
        var jobtxt = $('.profile-freelance-wrap .hidden-xs.fre-jobs_txt').html();
        $(jobtxt).prependTo($('.profile-freelance-wrap .visible-xs.fre-jobs_txt'));

    }

    /*page-contact-us*/
    var username = $('.page-contact-us .user-name').text();
    var userrole = $('.page-contact-us .user-role').text();
    $('.page-contact-us input[name=your-name]').val(username);
    $('.page-contact-us input[name=your-role]').val(userrole);

    $('.page-contact-us .add-file input').change(function () {
        let fileadd = $(this);
        let filename = this.files[0].name;
        if (filename !== '') {
            $(this).parent().parent().children('i+span').text(filename);
            $('.delete_file').show();
        } else {
            $(this).parent().parent().children('i+span').text('Attach file');
            $('.delete_file').hide();
        }
    });

    $('.page-contact-us .delete_file').click(function () {
        $('.page-contact-us .add-file input').val('');
        $(this).hide();
        $('.page-contact-us .select_file').children('i+span').text('Attach file');
    });

    $('.page-contact-us  .fre-cancel-btn').click(function () {
        $('.page-contact-us .add-file input').val('');
        $('.page-contact-us .delete_file').hide();
        $('.page-contact-us .select_file').children('i+span').text('Attach file');
    });

    function setEqualHeight(columns) {
        var tallestcolumn = 0;
        columns.each(
            function () {
                currentHeight = $(this).height();
                if (currentHeight > tallestcolumn) {
                    tallestcolumn = currentHeight;
                }
            });
        columns.height(tallestcolumn);
    }

    function autoheight() {

        var wind = $(window).width();

        if ((wind > 767) && (wind < 992)) {
            setEqualHeight($('.fre-how-work_list .fre-how-work_list_wp'));
        }

        if (wind <= 767) {
            $('.page-referrals_posters>.row').addClass('owl-carousel');
            $('.page-referrals_bnr>.row').addClass('owl-carousel');
            $(".page-referrals .owl-carousel").owlCarousel({
                loop: true,
                items: 1,
                margin: 0,
                animate: 'slideInRight',
                navText: '',
                nav: true,
                smartSpeed: 650
            });
        }

        setEqualHeight($('.improv_list_wp'));

        if (wind >= 768) {

            setEqualHeight($('.fre-blog-list-sticky_main + div .fre-blog-item'));
            setEqualHeight($('.fre-blog-thd_bl .fre-blog-item'));
            setEqualHeight($('.single .block-posts>.row>div'));
            setEqualHeight($('.tabs_wp.skills1>div'));
            setEqualHeight($('.tabs_wp.endorsment>div'));
            setEqualHeight($('.fre-post-package_txt'));
            setEqualHeight($('.page-referrals_posters li>img'));
            setEqualHeight($('.skills_awards>div'));
            setEqualHeight($('#rating>.row>div'));
            setEqualHeight($('.project-detail-box .project-detail-about .fre-profile-box>div'));
            setEqualHeight($('.profile-list-container .fre-freelancer-wrap'));
            setEqualHeight($('.profile-content.fre-freelancer-wrap>.row>div'));
            setEqualHeight($('.home .fre-freelancer-wrap'));
            setEqualHeight($('.home .fre-jobs-list .jobs-t'));
            setEqualHeight($('.home .fre-stories .stories-content'));
            setEqualHeight($('.page-template-page-proffessionals .profs-cat_sublist'));
            setEqualHeight($('.page-template-page-projects .profs-cat_sublist'));
            setEqualHeight($('.author .profile-freelance-info.row>div'));
            setEqualHeight($('.page-template-page-profile .profile-freelance-wrap>.fre-profile-box:first-child .profile-freelance-info>div'));
        }
    }

    autoheight();

    function resetHeight() {
        $('.fre-blog-list-sticky_main + div .fre-blog-item').css("height", "");
        $('.fre-blog-thd_bl .fre-blog-item').css("height", "");
        $('.single .block-posts>.row>div').css("height", "");
        $('.tabs_wp.skills1>div').css("height", "");
        $('.tabs_wp.endorsment>div').css("height", "");
        $('.fre-post-package_txt').css("height", "");
        $('.page-referrals_posters li>img').css("height", "");
        $('.skills_awards>div').css("height", "");
        $('#rating>.row>div').css("height", "");
        $('.project-detail-box .project-detail-about .fre-profile-box>div').css("height", "");
        $('.profile-list-container .fre-freelancer-wrap').css("height", "");
        $('.profile-content.fre-freelancer-wrap>.row>div').css("height", "");
        $('.home .fre-jobs-list .jobs-t').css("height", "");
        $('.author .profile-freelance-info.row>div').css("height", "");
        $('.page-template-page-proffessionals .profs-cat_sublist').css("height", "");
        $('.page-template-page-projects .profs-cat_sublist').css("height", "");
        $('.page-template-page-profile .profile-freelance-wrap>.fre-profile-box:first-child .profile-freelance-info>div').css("height", "");
    }

    $(".dop li:nth-child(even)").remove();
    $('input[type="file"].usp-input').change(function (e) {
        if ($('#file__new').val()) {
            let value = $('#file__new').val(),
                newValue = value.split('\\')[2];
            $('.value-1 .value').text(newValue);
            $('.value-1').addClass('visible');
            $('.file__label').attr('for', 'file__new-1')
        } else {
            $('.value-1').removeClass('visible');
        }
        if ($('#file__new-1').val()) {
            let value = $('#file__new-1').val(),
                newValue = value.split('\\')[2];
            $('.value-2 .value').text(newValue);
            $('.value-2').addClass('visible');
            $('.file__label').attr('for', 'file__new-2')
        } else {
            $('.value-2').removeClass('visible');
        }
        if ($('#file__new-2').val()) {
            let value = $('#file__new-2').val(),
                newValue = value.split('\\')[2];
            $('.value-3 .value').text(newValue);
            $('.value-3').addClass('visible');
            if ($('#file__new-1').val()) {
                $('.file__label').attr('for', 'file__new')
            } else {
                $('.file__label').attr('for', 'file__new-1')
            }
        } else {
            $('.value-3').removeClass('visible');
        }
    });
    $('.value-1 .closed').click(function () {
        $('#file__new').val('');
        $('.file__label').attr('for', 'file__new')
    })
    $('.value-2 .closed').click(function () {
        $('#file__new-1').val('');
        $('.file__label').attr('for', 'file__new-1')
    })
    $('.value-3 .closed').click(function () {
        $('#file__new-2').val('');
        $('.file__label').attr('for', 'file__new-2')
    })
    $('.input-block .closed').click(function () {
        $(this).parent().removeClass('visible');

        if ($('.value__block .visible').length < 3) {
            $('.usp-input').removeAttr('disabled');
        }
    });

    $(window).on('resize', function () {
        clearTimeout(window.resizedFinished);
        resetHeight();
        window.resizedFinished = setTimeout(function () {
            autoheight();
        }, 500);
    });

    $('#stripe_number, #two-ch_stripe_number').mask('9999 9999 9999 9999');
    $('#expiration').mask("99/99");
    $('#two-ch_expiration').mask("99/9999");
    $("#cvc, #two-ch_cvc").mask("999");
    $('#expiration').change(function () {
        let val = $(this).val(),
            val1 = val.split('/')[0],
            val2 = val.split('/')[1];
        $('#exp_month').val(val1)
        $('#exp_year').val(val2)
    });

    // select for currency
    if ($('select[name="project_currency"]').length > 0) {
        $('select[name="project_currency"]').select2({
            templateResult: function selectResultFormat(state) {
                let $elem = $(state.element);
                let icon = $elem.attr('data-icon');

                if (!state.id || icon === '') {
                    return state.text;
                }

                $state = $(
                    '<div style="width: 300px">' +
                    '<img src="' + icon + '" class="project_currency__flag">' +
                    state.text +
                    '</div>'
                );

                return $state;
            },

            templateSelection: function (state) {
                let $elem = $(state.element);
                let icon = $elem.attr('data-icon');

                if (!state.id || icon === '') {
                    return state.text;
                }

                let $state = $(
                    '<span><img class="project_currency__flag"> <span></span></span>'
                );

                $state.find("span").text(state.text);
                $state.find("img").attr("src", icon);

                return $state;
            },
        });
    }
// radio buttons for PRO bid on project
    $('html').on('click', '.bid-type__btn', function () {
        $('.bid-type__btn').removeClass('bid-type__btn--selected');
        $(this).addClass('bid-type__btn--selected');
    });

    /*
    * Arbitrate modal window
    */

// split type selector
    $('input[name="split_type"]').change(function () {
        let type = $(this).val();
        let sign = type === 'percent' ? '%' : '$';

        $('#split_value_freelancer, #split_value_client').val('');

        $('.split_type_sign').html(sign);
    });

// change split value
    $('#split_value_freelancer, #split_value_client').bind('keyup mouseup mousewheel', function (e) {
        e.preventDefault();

        let $current_input = $(this),
            $freelancer_input = $('#split_value_freelancer'),
            $client_input = $('#split_value_client'),
            user_type = $current_input.attr('data-user-type'),
            split_type = $('input[name="split_type"]:checked').val(),
            split_sign = split_type === 'percent' ? '%' : '$',
            total_value = 0;

        // if first character is zero - remove it
        if ($current_input.val().charAt(0) === '0') {
            $current_input.val($current_input.val().slice(1));
        }

        switch (split_type) {
            case 'percent':
                total_value = 100;
                break;

            case 'number':
                total_value = $('.btn-arbitrate-project').attr('data-bid-price');
                break;

            default:
                $freelancer_input.val('');
                $client_input.val('');

                AE.pubsub.trigger('ae:notification', {
                    msg: 'First choose split type',
                    notice_type: 'error'
                });
                break;
        }

        if (parseFloat($current_input.val()) > parseFloat(total_value)) {
            $freelancer_input.val('');
            $client_input.val('');

            AE.pubsub.trigger('ae:notification', {
                msg: 'Entered value is larger than ' + total_value + split_sign,
                notice_type: 'error'
            });

            return false;
        }

        let difference = parseFloat(total_value) - parseFloat($current_input.val());

        if (user_type === 'freelancer') {
            $client_input.val(difference);
        } else {
            $freelancer_input.val(difference);
        }

    });

    $('.btn-arbitrate-project').click(function () {
        let bid_value = $(this).attr('data-bid-price');
        $('#modal_arbitrate_winning_bid').html(bid_value);
    });


});

