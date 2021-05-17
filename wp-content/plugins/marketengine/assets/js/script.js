jQuery(document).ready(function($) {
	$('.me-list-thumbs').meSliderThumbs();

	var magnificInstance = true;
	var magnificItem = 0;
	$('.me-large-fancybox').on('click', function(event) {
		magnificInstance = true;
		$('.me-fancybox').magnificPopup({
		 	type: 'image',
		 	gallery: {
		      enabled: true
		    },
		    disableOn: function() {
		    	return magnificInstance;
		    }
		}).magnificPopup('open', magnificItem);
	});
	$('.me-fancybox').on('click', function(ev) {
		ev.preventDefault();
		var target = ev.currentTarget;
		var medium_img = $(target).attr('medium-img');
		$('.me-large-fancybox').find('img').attr('src', medium_img);
		magnificItem = $(target).parent('li').index();
		magnificInstance = false;
	});

	var owl_carousel = $("#me-related-slider");
    owl_carousel.owlCarousel({
		items : 2,
		margin: 26,
		nav: true,
		navText: ['<span></span>', '<span></span>'],
		dots: false,
		slideBy: 1,
		responsive:{
	        0:{
	            items:1,
	            nav:true,
	        },
	        992:{
	        	items: 3,
	        	nav: true
	        },
	        1200:{
	            items:4,
	            nav:true
	        }
	    },
  	});

	/**
	 * Hover category show/hide
	 */
	$('.me-has-category > li').hover(
		function() {
			$('body').addClass('marketengine-categories');
		},
		function() {
			$('body').removeClass('marketengine-categories');
		}
	);

	$('.me-overlay').hover(
		function() {
			$('body').removeClass('marketengine-categories');
		},
		function() {
			
		}
	);

	/**
	 * [description ME tabs]
	 */
	$('.me-tabs > li').on('click', function(ev) {
		var target = ev.currentTarget;
		$(target).parent('.me-tabs').children('li').removeClass('active');
		$(target).addClass('active');
		var pos = $(target).index();
		var parent = $(target).parent('.me-tabs');
		$(parent).next().children('div').hide()
		$(parent).next().children('div').eq(pos).show();
	});

	/**
	 * Show/Hide menu page tablet/mobile
	 */
	$('.me-page-humberger').on('click', function() {
		$('.marketengine-header-bottom').removeClass('me-account-active');
		$('.me-account-humberger').removeClass('active');
		$(this).toggleClass('active');
		$('.marketengine-header-bottom').toggleClass('me-page-active');

	});

	/**
	 * Show/Hide menu account mobile
	 */
	$('.me-account-humberger').on('click', function() {
		$('.marketengine-header-bottom').removeClass('me-page-active');
		$('.me-page-humberger').removeClass('active');
		$(this).toggleClass('active');
		$('.marketengine-header-bottom').toggleClass('me-account-active');
	});

	/**
	 * Contact tabs on mobile
	 */
	$('.me-contact-listing-tabs').on('click', function(ev) {
		$(this).toggleClass('active');
		$('body').toggleClass('me-contact-listing-tabs-active').removeClass('me-contact-user-tabs-active');
		return false;
	});

	$('.me-contact-user-tabs').on('click', function(ev) {
		$(this).toggleClass('active');
		$('body').toggleClass('me-contact-user-tabs-active').removeClass('me-contact-listing-tabs-active');
		return false;
	});



	/**
	 * Scroll messages inbox
	 */
	/*var width_window = $(window).width();
	var height_window = $(window).height();
	var height_header_top = $('#me-header-wrapper').innerHeight();
	var height_contact_header = $('.me-contact-header').innerHeight();
	var height_message_typing = $('.me-message-typing').innerHeight();
	var height_Scrollbar = height_window - height_header_top - height_message_typing - height_contact_header - 3;*/
	
	// if(width_window <= 767) {
	// 	$('.me-contact-messages').mCustomScrollbar({
	// 		setTop:"-1000000px",
	// 		setHeight: height_Scrollbar
	// 	});
	// 	$('.me-contact-user-wrap').mCustomScrollbar({
	// 		setHeight: height_Scrollbar
	// 	});

	// } else {
	// 	$('.me-contact-messages').mCustomScrollbar({
	// 		setTop:"-1000000px",
	// 		setHeight:500
	// 	});

	// 	$('.me-contact-user-wrap').mCustomScrollbar({
	// 		setHeight:536
	// 	});
	// }
	/**
	 * -----------------------------------------------------------------------------------
	 * Order
	 * -----------------------------------------------------------------------------------
	 */
	$( "#me-inquiries-pick-date-1" ).datepicker({
		onSelect: function( selectedDate ) {
			$( "#me-inquiries-pick-date-2" ).datepicker();
		    $( "#me-inquiries-pick-date-2" ).datepicker("option", "minDate", selectedDate );
		    setTimeout(function(){
	            $( "#me-inquiries-pick-date-2" ).datepicker('show');
	        }, 16);
		}
	});

	$( "#me-order-pick-date-1" ).datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect: function( selectedDate ) {
			$( "#me-order-pick-date-2" ).datepicker({dateFormat: 'yy-mm-dd'});
		    $( "#me-order-pick-date-2" ).datepicker("option", "minDate", selectedDate );
		    setTimeout(function(){
	            $( "#me-order-pick-date-2" ).datepicker('show');
	        }, 16);
		}
	});

	$('.me-orderlist-filter-tabs span').on('click', function() {
		$(this).parents('.me-tabs-section').toggleClass('me-order-filter-active');
	});

	$('.me-inquiries-filter-tabs span').on('click', function() {
		$(this).parents('.me-tabs-section').toggleClass('me-inquiries-filter-active');
	});

	/**
	 * -----------------------------------------------------------------------------------
	 * Resolution center
	 * -----------------------------------------------------------------------------------
	 */
	$( "#me-pick-date-1" ).datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect: function( selectedDate ) {
			$( "#me-pick-date-2" ).datepicker({dateFormat: 'yy-mm-dd'});
		    $( "#me-pick-date-2" ).datepicker("option", "minDate", selectedDate );
		    setTimeout(function(){
	            $( "#me-pick-date-2" ).datepicker('show');
	        }, 16);
		}
	});

	/* 
	Move dispute.js
	
	$('.me-switch-tab-filter-1, .me-switch-tab-filter-2').on('click', function() {
		$('.me-resolution').toggleClass('me-rslt-filter');
	});

	$('.me-disputed-case-tabs').on('click', function() {
		$(this).toggleClass('active');
		$('body').toggleClass('me-disputed-case-tabs-active').removeClass('me-disputed-action-tabs-active');
		return false;
	});

	$('.me-disputed-action-tabs').on('click', function() {
		$(this).toggleClass('active');
		$('body').toggleClass('me-disputed-action-tabs-active').removeClass('me-disputed-case-tabs-active');
		return false;
	});

	$('.me-receive-item-field').on('change', function(event) {
		var get_refund_block_id = $(this).data('get-refund-block');
		$('#dispute-get-refund-yes').removeClass('active');
		$('#dispute-get-refund-no').removeClass('active');
		$(document.getElementById(get_refund_block_id)).addClass('active');
		$('.me-solution-item').attr('checked', false);
	});
	*/
});

