(function ($, Models, Collections, Views) {
    $(document).ready(function () {

        // Set defatul amount of milestones is zero
        window.milestoneAmount = 0;
        position_order = 0;


        /**
         * MODEL FOR MILESTONE
         */
        Models.Milestone = Backbone.Model.extend({
            action: 'ae_sync_milestone',
            initialize: function () {
            }

        });

        /**
         * COLLECTION FOR LIST OF MILESTONES
         */
        Collections.MilestoneList = Backbone.Collection.extend({
            model: Models.Milestone,

            parse: function (result) {
                return result.posts;
            }
        });

        /**
         * VIEW FOR MILESTONE ITEM IN SUBMIT AND EDIT PROJECT
         */
        if ($('#milestone_template').length > 0) {
            Views.Milestone = Backbone.View.extend({
                tagName: 'li',
                className: 'milestone-item-wrapper',
                template: _.template($('#milestone_template').html()),
                events: {
                    'input .txt-milestone-item': 'editMilestone',
                    'click .btn-del-milestone-item': 'removeMilestone'
                },

                initialize: function () {
                    //init jQuery Sortable
                    $('[data-rel^="sortable"]').sortable({
                        start: function (event, ui) {
                            ui.item.startPosition = ui.item.index();
                        },
                        stop: function (event, ui) {
                            var el = $('#milestones option').eq(ui.item.startPosition);
                            var el_id = $('#milestones_id option').eq(ui.item.startPosition);

                            var cpy = el.clone();
                            var cpy_id = el_id.clone();

                            el.remove();
                            el_id.remove();

                            if (ui.item.index() === 0) {
                                $('#milestones').prepend(cpy);
                                $('#milestones_id').prepend(cpy_id);
                            } else {
                                $('#milestones option').eq(ui.item.index() - 1).after(cpy);
                                $('#milestones_id option').eq(ui.item.index() - 1).after(cpy_id);
                            }
                        }
                    });
                },

                render: function () {
                    this.$el.html(this.template(this.model.toJSON()));
                    return this;
                },

                // Edit value of milestone
                editMilestone: function (e) {
                    //e.preventDefault();
                    var input = this.$el.find('input');
                    var index = this.model.get('position_order');

                    $('#milestones').find('option').each(function () {
                        if ($(this).data('index') == index) {
                            $(this).val(input.val());
                            $(this).text(input.val());
                        }
                    });

                },

                // Remove milestone from list
                removeMilestone: function (e) {
                    e.preventDefault();

                    // Remove a milestone
                    this.model.set('do_action', 'remove_milestone');
                    this.model.destroy();

                    var index = this.model.get('position_order');
                    this.$el.remove();

                    $('#milestones').find('option').each(function () {
                        if ($(this).data('index') == index) {
                            $(this).remove();
                        }
                    });

                    $('#milestones_id').find('option').each(function () {
                        if ($(this).data('index') == index) {
                            $(this).remove();
                        }
                    });

                    //Restore milestone input
                    window.milestoneAmount--;
                    if (window.milestoneAmount < ae_ms_localize.max_milestone) {
                        $('#milestone-input').fadeIn().val('');
                    }
                }
            });
        }

        /**
         * VIEW FOR MILESTONE ITEM IN SINGLE PROJECT
         */
        Views.MilestoneSingleProject = Backbone.View.extend({
            el: 'li',
            className: 'item-list-milestone',
            template: '',
            model: [],
            events: {
                'click a.change-status-milestone': 'changeStatus',
                'click ul.cat-action-milestone': 'noEvent',
                'click ul.cat-action-milestone > li > a': 'chooseStatus',
                'click .resolve-milestone': 'resolveMilestone',
                'click .close-milestone': 'closeMilestone',
                'click .open-milestone': 'openMilestone',
                'click .reopen-milestone': 'reopenMilestone',
            },

            initialize: function () {
                $(document).click(function () {
                    $('.cat-action-milestone').slideUp('fast');
                    $('.fa-angle-down').removeClass('rotate');
                });

                if ($('#milestone_template_project').length > 0) {
                    this.template = _.template($('#milestone_template_project').html());
                }

                this.blockUi = new Views.BlockUi();

                this.model.bind('change', this.render, this);
            },

            render: function () {
                this.$el.html(this.template(this.model.toJSON()));
                return this;
            },

            changeStatus: function (e) {
                e.preventDefault();
                var list = this.$el;
                $('.cat-action-milestone').slideUp('fast');
                $('.fa-angle-down').removeClass('rotate');
                e.stopPropagation();
                if (list.find('.cat-action-milestone').css('display') == 'none') {
                    list.find('.cat-action-milestone').slideDown('fast');
                    list.find('.fa-angle-down').addClass('rotate');
                } else {
                    list.find('.cat-action-milestone').slideUp('fast');
                    list.find('.fa-angle-down').removeClass('rotate');
                }
            },
            noEvent: function (e) {
                e.stopPropagation();
            },
            chooseStatus: function (e) {
                e.preventDefault();
            },

            // Action resolve milestone
            resolveMilestone: function (e) {
                var params = {
                    post_status: 'resolve',
                    do_action: 'resolve_milestone',
                }
                // Request to server
                if (this.model.get('post_status') == 'open' || this.model.get('post_status') == 'reopen') {
                    this.actionChangeStatus(params);
                }
            },

            // Action close milestone
            closeMilestone: function (e) {
                var params = {
                    post_status: 'done',
                    do_action: 'close_milestone',
                }
                // Request to server
                this.actionChangeStatus(params);
            },

            // Action re-open milestone
            reopenMilestone: function (e) {
                var params = {
                    post_status: 'reopen',
                    do_action: 'reopen_milestone',
                }
                // Request to server
                if (this.model.get('post_status') == 'resolve') {
                    this.actionChangeStatus(params);
                }
            },

            actionChangeStatus: function (params) {
                var view = this;
                this.$el.find('.fa-angle-down').removeClass('rotate');
                this.$el.find('.cat-action-milestone').slideUp('fast', function () {
                    if (view.model.get('post_status') != params.post_status) {
                        view.model.save(params, {
                            beforeSend: function () {
                                view.blockUi.block(view.$el);
                            },

                            success: function (res, result) {
                                if (result.success == true) {
                                    view.model.set(res.data);
                                    view.render();

                                    // Add changelog
                                    AE.pubsub.trigger('ae:addChangelog');

                                    AE.pubsub.trigger('ae:notification', {
                                        notice_type: 'success',
                                        msg: result.msg
                                    });
                                } else {
                                    AE.pubsub.trigger('ae:notification', {
                                        notice_type: 'error',
                                        msg: result.msg
                                    })
                                }
                            },

                            complete: function () {
                                view.blockUi.unblock();
                            }
                        });
                    }
                });
            }
        });

        Views.MilestoneListProject = Backbone.View.extend({
            el: '.cat-list-milestone',
            initialize: function () {
                var view = this;
                _.bindAll(this, 'addOne');

                if ($('#milestones_data').length > 0) {
                    var milestones = JSON.parse($('#milestones_data').html());
                    this.collection = new Collections.MilestoneList(milestones);
                } else {
                    this.collection = new Collections.MilestoneList();
                }

                this.milestone_view = [];

                /**
                 * init milestone item view
                 */
                this.collection.each(function (milestone, index, col) {
                    var el = $('li.item-list-milestone').eq(index);
                    var milestone_item = new Views.MilestoneSingleProject({el: el, model: milestone});
                    view.milestone_view.push(milestone_item);
                });

                this.listenTo(this.collection, 'add', this.addOne);

                // Block view
                this.blockUi = new Views.BlockUi();

                // Init heartbeat
                this.liveRenderMilestone();
            },

            addOne: function (milestone) {
                var milestoneItem = new Views.MilestoneSingleProject({model: milestone});
                this.milestone_view.push(milestone);
            },

            liveRenderMilestone: function () {
                view = this;
                view.initHeartBeat();

                $(document).on('heartbeat-tick', function (event, data) {
                    if (data.hasOwnProperty('milestone_new_changelog')) {
                        if (data['milestone_new_changelog'] == 1) {
                            view.blockUi.block(view.$el);
                            view.collection.fetch({
                                data: {
                                    action: "ae_fetch_milestones",
                                    post_parent: data['milestone_project_id']
                                },

                                success: function (result, res, xhr) {
                                    view.blockUi.unblock();
                                }
                            });
                        }
                    }
                })
            },

            initHeartBeat: function () {
                $(document).on('heartbeat-send', function (e, data) {
                    data['new_changelog'] = true;
                });
            }
        });

        //var milestones = new Collections.MilestoneList();
        var milestoneListProject = new Views.MilestoneListProject();

        /**
         * VIEW FOR LIST OF MILESTONE IN SUBMIT PROJECT
         */
        Views.MilestoneList = Backbone.View.extend({
            initialize: function () {
                this.listenTo(this.collection, 'add', this.addOne);
            },

            addOne: function (milestone) {
                var milestone = new Views.Milestone({model: milestone});
                this.$el.append(milestone.render().el);
            },

            render: function () {
                this.collection.each(this.addOne, this);
                return this;
            }
        });

        /**
         * VIEW FOR LIST OF MILESTONE IN SUBMIT PROJECT
         */
        Views.SubmitMilestoneList = Views.MilestoneList.extend({
            el: '.submit-project-list-milestone',
        })

        /**
         * VIEW FOR LIST OF MILESTONE IN EDIT PROJECT
         */
        Views.EditMilestoneList = Views.MilestoneList.extend({
            el: '.edit-project-list-milestone',
        })

        /**
         * VIEW FOR ADDING MILESTONE
         */
        Views.AddMilestone = Backbone.View.extend({
            el: '#add-milestone-form',
            events: {
                'keydown #milestone-input': 'addMilestone',
                'click .btn-insert-milestone-item' : 'triggerActEnter',
            },

            initialize: function () {

            },
            triggerActEnter : function(event){
                console.log('123');
                var element = $(event.currentTarget );
                var e = jQuery.Event("keydown");
                e.which = 13; // # Some key code value
                e.keyCode = 13
                $("#milestone-input").trigger(e);
            },
            addMilestone: function (e) {
                var input = this.$el.find('#milestone-input');
                var $this = this;
                var keycode = (e.keyCode ? e.keyCode : e.which);
                console.log(keycode);
                if (keycode == '13') {
                    if (input.val() != '') {
                        if (window.milestoneAmount < ae_ms_localize.max_milestone) {
                            var milestone = new Models.Milestone({
                                'post_title': input.val(),
                                'post_status': 'open',
                                'position_order': position_order,
                            });

                            this.collection.add(milestone);

                            // Add options to select for saving data
                            this.$el.find('#milestones').append('<option data-index=' + position_order + ' value="' + input.val() + '" selected>' + input.val() + '</option>');
                            this.$el.find('#milestones_id').append('<option data-index=' + position_order + ' value="0" selected>0</option>');

                            //$( '[data-rel^="sortable"]' ).append($li);
                            $('[data-rel^="sortable"]').sortable('refresh');

                            input.val('');

                            this.index++;
                            window.milestoneAmount++;
                            position_order++;
                        }

                        // Check limitaion of milestone was added
                        if (window.milestoneAmount >= ae_ms_localize.max_milestone) {
                            input.fadeOut();
                        }
                    } else {
                        //
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }
        });

        // Check if the page is submit project or not
        if ($('.submit-project-list-milestone').length > 0) {
            var milestoneList = new Collections.MilestoneList();
            var milestoneListView = new Views.SubmitMilestoneList({collection: milestoneList});
            var addMilestone = new Views.AddMilestone({collection: milestoneList});
            milestoneListView.render();
        }

        /**
         * Get milestone when edit project
         */
        AE.pubsub.on('AE:beforeSetupFields', function (model) {
            var post_type = model.get('post_type');
            window.milestoneAmount = 0;
            $('.milestone-loading').show();
            if (ae_globals.is_submit_project != 1) {
                $('#milestone-input').hide();
            }
            if (post_type == 'project') {
                var milestones = new Collections.MilestoneList();

                milestones.fetch({
                    data: {
                        action: 'ae_fetch_milestones',
                        post_parent: model.get('id'),
                    },
                    success: function (result, res, xhr) {
                        if (res.success == true) {
                            for (i = 0; i < res.posts.length; i++) {
                                $('#milestones').append('<option data-index=' + res.posts[i].position_order + ' value="' + res.posts[i].post_title + '" selected>' + res.posts[i].post_title + '</option>');
                                $('#milestones_id').append('<option data-index=' + res.posts[i].position_order + ' value="' + res.posts[i].id + '" selected>' + res.posts[i].id + '</option>');
                                window.milestoneAmount++;
                                position_order++;
                            }

                            if (window.milestoneAmount >= ae_ms_localize.max_milestone) {
                                $('#milestone-input').hide();
                            } else {
                                $('#milestone-input').show();
                            }

                            // Remove loading
                            $('.milestone-loading').hide();
                        } else {
                            $('#milestone-input').show();
                            $('.milestone-loading').hide();
                        }
                    }
                });

                var milestoneListView = new Views.SubmitMilestoneList({collection: milestones});
                var addMilestone = new Views.AddMilestone({collection: milestoneList});
                // Reset milestone list and option
                milestoneListView.$el.html('');
                $('#milestones').html('');
                $('#milestones_id').html('');

                // Render html
                milestoneListView.render();
            }
        });
        // end edit milestone on edit project
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);