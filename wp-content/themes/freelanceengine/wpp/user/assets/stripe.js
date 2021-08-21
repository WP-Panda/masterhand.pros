(function($, Models, Collections, Views) {
	$(document).ready(function() { 
		Views.stripeUpdate = Backbone.View.extend({
			el:'.update-stripe-container',
			events: {
				'click .btn-update-stripe': 'openEditStripeModal',
				'click .stripe_disconnect' : 'deauthorize_stripe'
			},
			initialize : function(options){
				this.blockUi = new Views.BlockUi();
			},
			deauthorize_stripe : function(event){
				event.preventDefault();
				var view = this;
				$currentTarget = $(event.currentTarget);
				var r = confirm(ae_stripe_escrow_globals.confirm_disconnect);
				if(r == true){
					$.ajax ({
						type : 'post',
						url  : ae_globals.ajaxURL,
						data : { 
							action : 'fre-stripe-escrow-deauthorize'
						},
						beforeSend : function () {
							view.blockUi.block($currentTarget);
						},
						success : function (res) {
							view.blockUi.unblock();
							if(!res.success){
								AE.pubsub.trigger('ae:notification', {
					                msg: res.msg,
					                notice_type: 'error'
					            });
							}else{
								AE.pubsub.trigger('ae:notification', {
					                msg: res.msg,
					                notice_type: 'success'
					            });
								window.location.reload();
							}
						}
					});
				}
			},
			openEditStripeModal: function(event){
				event.preventDefault();				
				if( typeof StripeEscrowForm === 'undefined' ){
					StripeEscrowForm = new Views.StripeEscrowForm();					
				}
				StripeEscrowForm.openModal();
			}
		});	
		Views.StripeEscrowForm = Views.Modal_Box.extend({
	        el: 'div#stripe_escrow_modal',
	        events: {
	            'submit form#stripe_form': 'submitStripe'
	        },
	        initialize: function(options) {
	            Views.Modal_Box.prototype.initialize.apply(this, arguments);
	            // bind event to modal
	            _.bindAll(this, 'stripeResponseHandler');
	            Stripe.setPublishableKey(ae_stripe_escrow.stripe_public_key);
	            this.blockUi = new Views.BlockUi();	            
	        },	       
	        submitStripe: function(event) {
	            event.preventDefault();
	            var $form = $(event.currentTarget);
	            this.blockUi.block($form);
	            Stripe.createToken($form, this.stripeResponseHandler);
	        },
	        validateCard: function(response) {
	            var error = response.error;
	            switch (error.param) {
	                case 'number':
	                    $('#stripe_number').focus();
	                    break;
	                case 'exp_year':
	                    $('#exp_year').focus();
	                    break;
	                case 'exp_month':
	                    $('#exp_month').focus();
	                    break;
	                case 'cvc':
	                    $('#cvc').focus();
	                    break;
	                default:
	                    break;
	                    $("#submit_stripe").removeAttr("disabled");
	            }
	            AE.pubsub.trigger('ae:notification', {
	                msg: error.message,
	                notice_type: 'error'
	            });
	            $("#submit_stripe").removeAttr("disabled", "disabled");
	        },
	        stripeResponseHandler: function(status, response) {
	            var view = this;
	            if (status !== 200 && response.error !== undefined) {
	                view.validateCard(response);
	                view.blockUi.unblock();
	                return false;
	            } else {
	                view.saveRipientInfor(response);
	            }
	        },
	        saveRipientInfor : function (res) {
				var view = this;				
				var data = {
					token: res.id,
					stripe_email: view.$el.find('#stripe_email').val(),
					stripe_name: view.$el.find('#name_card').val(),
					action: 'fre-stripe-escrow-customer'
				};
				$.ajax ({
					type : 'post',
					url  : ae_globals.ajaxURL,
					data : data,
					beforeSend : function () {
					},
					success : function (res) {
						view.blockUi.unblock();
						if(res.success) {						
							view.closeModal();
							AE.pubsub.trigger('ae:notification',{
								msg	: res.msg,
								notice_type	: 'success'
							});		
						} else {
							AE.pubsub.trigger('ae:notification',{
								msg	: res.msg,
								notice_type	: 'error'
							});						
						}
					}
				});
			}
	    });
		new Views.stripeUpdate();
	});
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);