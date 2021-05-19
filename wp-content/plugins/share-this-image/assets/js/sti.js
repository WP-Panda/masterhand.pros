// StiHooks

var StiHooks = StiHooks || {};
StiHooks.filters = StiHooks.filters || {};

(function($){
    "use strict";

    var selector = sti_vars.selector;
	var currentImage = false;
	var currentImageElements = {};

	StiHooks.add_filter = function( tag, callback, priority ) {

		if( typeof priority === "undefined" ) {
			priority = 10;
		}

		StiHooks.filters[ tag ] = StiHooks.filters[ tag ] || [];
		StiHooks.filters[ tag ].push( { priority: priority, callback: callback } );

	};

	StiHooks.apply_filters = function( tag, value, options ) {

		var filters = [];

		if( typeof StiHooks.filters[ tag ] !== "undefined" && StiHooks.filters[ tag ].length > 0 ) {

			StiHooks.filters[ tag ].forEach( function( hook ) {

				filters[ hook.priority ] = filters[ hook.priority ] || [];
				filters[ hook.priority ].push( hook.callback );
			} );

			filters.forEach( function( StiHooks ) {

				StiHooks.forEach( function( callback ) {
					value = callback( value, options );
				} );

			} );
		}

		return value;
	};

	$.fn.sti = function( options ) {
	
		var opts = $.extend({
			selector: sti_vars.selector,
			title: sti_vars.title,
			summary: sti_vars.summary,
			minWidth: sti_vars.minWidth,
			minHeight: sti_vars.minHeight,
			sharer: sti_vars.sharer,
			position: sti_vars.position,
			analytics: sti_vars.analytics,
			buttons: sti_vars.buttons,
			twitterVia: sti_vars.twitterVia,
			appId: sti_vars.appId,
            align: {x:"left", y:"top"},
            offset: {x:0, y:0},
			custom_data: sti_vars.custom_data
        }, options );

		var stiBoxSingleSelector = '';

		var appendButtonsTo = StiHooks.apply_filters( 'sti_append_buttons_to', 'body' );

		var methods = {

			createImgHash: function( str ) {				
				var character,
					hash,
					i;
							
				if( !str ) { return ""; }
						
				hash = 0;
						
				if ( str.length === 0 ) { return hash; }
						
				for( i=0;i<str.length;i++ ) {
					character = str[i];
					hash = methods.hashChar( str,character,hash );
				}

                hash = Math.abs( hash ) * 1.1 + "";
						
				return hash.substring(0,5);
						
			},

			scrollToImage: function(el) {

				var urlParam = methods.getUrlParams();
				var hash = ( typeof urlParam['scroll'] !== 'undefined' ) ? urlParam['scroll'] : '';

				if ( ! hash ) {
					return;
				}

				$('img, [data-media]').each(function() {
					var media = $(this).data('media') ? $(this).data('media') : $(this)[0].src;

					if ( media ) {
						media = methods.fillFullPath( media );
						media = methods.checkForImgFullSize( media );
					}

					if (hash === methods.createImgHash(media)) {
						// Divi gallery support
						if ( $(this).closest('.et_pb_gallery_item').length ) {
							$(this).trigger('click');
							return false;
						}
					}

				});

			},
			
			hashChar: function( str,character,hash ) {				
				hash = ( hash<<5 ) - hash + str.charCodeAt( character );					
				return hash&hash;					
			},

			stringToId: function( input ) {
				var hash = 0, len = input.length;
				for (var i = 0; i < len; i++) {
					hash  = ((hash << 5) - hash) + input.charCodeAt(i);
					hash |= 0;
				}
				return hash;
			},
			
			shareButtons: function() {
			
				var buttonsList = '';

				var buttons = methods.isMobile() ? opts.buttons.mobile : opts.buttons.desktop;

				if ( buttons ) {
					for ( var i=0;i<buttons.length;i++ ) {
						var network = buttons[i];
						buttonsList += '<div class="sti-btn sti-' + network +'-btn" data-network="' + network + '" rel="nofollow">';
						buttonsList += methods.getSvgIcon( network );
						buttonsList += '</div>';
					}
				}
				
				return buttonsList;
				
			},

            getSvgIcon: function( network ) {

                var icon = '';

				if ( opts.custom_data && opts.custom_data.buttons && opts.custom_data.buttons[network] ) {
					icon += opts.custom_data.buttons[network]['icon'];
					return icon;
				}

                switch( network ) {

                    case "facebook" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>';
                        break;

					case "messenger" :
						icon += '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"<g><path d="M12,0A11.77,11.77,0,0,0,0,11.5,11.28,11.28,0,0,0,3.93,20L3,23.37A.5.5,0,0,0,3.5,24a.5.5,0,0,0,.21,0l3.8-1.78A12.39,12.39,0,0,0,12,23,11.77,11.77,0,0,0,24,11.5,11.77,11.77,0,0,0,12,0Zm7.85,8.85-6,6a.5.5,0,0,1-.68,0L9.94,12.1l-5.2,2.83a.5.5,0,0,1-.59-.79l6-6a.5.5,0,0,1,.68,0l3.24,2.78,5.2-2.83a.5.5,0,0,1,.59.79Z"/></g></svg>';
						break;

                    case "twitter" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/></svg>';
                        break;

                    case "linkedin" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z"/></svg>';
                        break;

                    case "reddit" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M24 11.5c0-1.65-1.35-3-3-3-.96 0-1.86.48-2.42 1.24-1.64-1-3.75-1.64-6.07-1.72.08-1.1.4-3.05 1.52-3.7.72-.4 1.73-.24 3 .5C17.2 6.3 18.46 7.5 20 7.5c1.65 0 3-1.35 3-3s-1.35-3-3-3c-1.38 0-2.54.94-2.88 2.22-1.43-.72-2.64-.8-3.6-.25-1.64.94-1.95 3.47-2 4.55-2.33.08-4.45.7-6.1 1.72C4.86 8.98 3.96 8.5 3 8.5c-1.65 0-3 1.35-3 3 0 1.32.84 2.44 2.05 2.84-.03.22-.05.44-.05.66 0 3.86 4.5 7 10 7s10-3.14 10-7c0-.22-.02-.44-.05-.66 1.2-.4 2.05-1.54 2.05-2.84zM2.3 13.37C1.5 13.07 1 12.35 1 11.5c0-1.1.9-2 2-2 .64 0 1.22.32 1.6.82-1.1.85-1.92 1.9-2.3 3.05zm3.7.13c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm9.8 4.8c-1.08.63-2.42.96-3.8.96-1.4 0-2.74-.34-3.8-.95-.24-.13-.32-.44-.2-.68.15-.24.46-.32.7-.18 1.83 1.06 4.76 1.06 6.6 0 .23-.13.53-.05.67.2.14.23.06.54-.18.67zm.2-2.8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm5.7-2.13c-.38-1.16-1.2-2.2-2.3-3.05.38-.5.97-.82 1.6-.82 1.1 0 2 .9 2 2 0 .84-.53 1.57-1.3 1.87z"/></svg>';
                        break;

                    case "pinterest" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12.14.5C5.86.5 2.7 5 2.7 8.75c0 2.27.86 4.3 2.7 5.05.3.12.57 0 .66-.33l.27-1.06c.1-.32.06-.44-.2-.73-.52-.62-.86-1.44-.86-2.6 0-3.33 2.5-6.32 6.5-6.32 3.55 0 5.5 2.17 5.5 5.07 0 3.8-1.7 7.02-4.2 7.02-1.37 0-2.4-1.14-2.07-2.54.4-1.68 1.16-3.48 1.16-4.7 0-1.07-.58-1.98-1.78-1.98-1.4 0-2.55 1.47-2.55 3.42 0 1.25.43 2.1.43 2.1l-1.7 7.2c-.5 2.13-.08 4.75-.04 5 .02.17.22.2.3.1.14-.18 1.82-2.26 2.4-4.33.16-.58.93-3.63.93-3.63.45.88 1.8 1.65 3.22 1.65 4.25 0 7.13-3.87 7.13-9.05C20.5 4.15 17.18.5 12.14.5z"/></svg>';
                        break;

					case "whatsapp":
						icon += '<svg enable-background="new 0 0 100 100" version="1.1" viewBox="0 0 100 100" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><defs><rect height="100" id="SVGID_1_" width="100"/></defs><path d="M95,49.247c0,24.213-19.779,43.841-44.182,43.841c-7.747,0-15.025-1.98-21.357-5.455L5,95.406   l7.975-23.522c-4.023-6.606-6.34-14.354-6.34-22.637c0-24.213,19.781-43.841,44.184-43.841C75.223,5.406,95,25.034,95,49.247    M50.818,12.388c-20.484,0-37.146,16.535-37.146,36.859c0,8.066,2.629,15.535,7.076,21.611l-4.641,13.688l14.275-4.537   c5.865,3.851,12.891,6.097,20.437,6.097c20.481,0,37.146-16.533,37.146-36.858C87.964,28.924,71.301,12.388,50.818,12.388    M73.129,59.344c-0.273-0.447-0.994-0.717-2.076-1.254c-1.084-0.537-6.41-3.138-7.4-3.494c-0.993-0.359-1.717-0.539-2.438,0.536   c-0.721,1.076-2.797,3.495-3.43,4.212c-0.632,0.719-1.263,0.809-2.347,0.271c-1.082-0.537-4.571-1.673-8.708-5.334   c-3.219-2.847-5.393-6.364-6.025-7.44c-0.631-1.075-0.066-1.656,0.475-2.191c0.488-0.482,1.084-1.255,1.625-1.882   c0.543-0.628,0.723-1.075,1.082-1.793c0.363-0.717,0.182-1.344-0.09-1.883c-0.27-0.537-2.438-5.825-3.34-7.976   c-0.902-2.151-1.803-1.793-2.436-1.793c-0.631,0-1.354-0.09-2.076-0.09s-1.896,0.269-2.889,1.344   c-0.992,1.076-3.789,3.676-3.789,8.963c0,5.288,3.879,10.397,4.422,11.114c0.541,0.716,7.49,11.92,18.5,16.223   C63.2,71.177,63.2,69.742,65.186,69.562c1.984-0.179,6.406-2.599,7.312-5.107C73.398,61.943,73.398,59.792,73.129,59.344"/></g></svg>';
						break;

					case "telegram":
						icon += '<svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z"></path></svg>';
						break;

                    case "vkontakte" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.547 7h-3.29a.743.743 0 0 0-.655.392s-1.312 2.416-1.734 3.23C14.734 12.813 14 12.126 14 11.11V7.603A1.104 1.104 0 0 0 12.896 6.5h-2.474a1.982 1.982 0 0 0-1.75.813s1.255-.204 1.255 1.49c0 .42.022 1.626.04 2.64a.73.73 0 0 1-1.272.503 21.54 21.54 0 0 1-2.498-4.543.693.693 0 0 0-.63-.403h-2.99a.508.508 0 0 0-.48.685C3.005 10.175 6.918 18 11.38 18h1.878a.742.742 0 0 0 .742-.742v-1.135a.73.73 0 0 1 1.23-.53l2.247 2.112a1.09 1.09 0 0 0 .746.295h2.953c1.424 0 1.424-.988.647-1.753-.546-.538-2.518-2.617-2.518-2.617a1.02 1.02 0 0 1-.078-1.323c.637-.84 1.68-2.212 2.122-2.8.603-.804 1.697-2.507.197-2.507z"/></svg>';
                        break;

                    case "tumblr" :
                        icon += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.5.5v5h5v4h-5V15c0 5 3.5 4.4 6 2.8v4.4c-6.7 3.2-12 0-12-4.2V9.5h-3V6.7c1-.3 2.2-.7 3-1.3.5-.5 1-1.2 1.4-2 .3-.7.6-1.7.7-3h3.8z"/></svg>';
                        break;

                    case "digg" :
                        icon += '<svg enable-background="new 0 0 512 512" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><rect height="42.351" width="51.831" x="152.89" y="114.421"></rect><path d="M77.039,179.508H0v155.27h127.082V111.662H77.039V179.508z M72.396,294.889H46.298v-70.594h26.098V294.889z   " ></path><rect height="156.168" width="51.831" x="152.89" y="179.185"></rect><path d="M231.426,335.354h77.06v24.297h-77.943v43.573h105.88h22.105V180.11H231.426V335.354z M277.713,223.684   h26.125v70.599h-26.125V223.684z"></path><path d="M384.915,180.11v155.244h77.042v24.297h-77.929v43.573h105.88H512V180.11H384.915z M457.31,294.283h-26.081   v-70.599h26.081V294.283z"></path></g></svg>';
                        break;

                    case "delicious" :
                        icon += '<svg enable-background="new 0 0 24 24" version="1.1" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M23,0.5H12c-0.3,0-0.5,0.2-0.5,0.5v10.5H1c-0.3,0-0.5,0.2-0.5,0.5v11c0,0.3,0.2,0.5,0.5,0.5h11c0.3,0,0.5-0.2,0.5-0.5V12.5  H23c0.3,0,0.5-0.2,0.5-0.5V1C23.5,0.7,23.3,0.5,23,0.5z"/></svg>';
                        break;

                    case "odnoklassniki" :
                        icon += '<svg enable-background="new 0 0 30 30" version="1.1" viewBox="0 0 30 30" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M22,15c-1,0-3,2-7,2s-6-2-7-2c-1.104,0-2,0.896-2,2c0,1,0.568,1.481,1,1.734C8.185,19.427,12,21,12,21l-4.25,5.438  c0,0-0.75,0.935-0.75,1.562c0,1.104,0.896,2,2,2c1.021,0,1.484-0.656,1.484-0.656S14.993,23.993,15,24  c0.007-0.007,4.516,5.344,4.516,5.344S19.979,30,21,30c1.104,0,2-0.896,2-2c0-0.627-0.75-1.562-0.75-1.562L18,21  c0,0,3.815-1.573,5-2.266C23.432,18.481,24,18,24,17C24,15.896,23.104,15,22,15z" id="K"/><path d="M15,0c-3.866,0-7,3.134-7,7s3.134,7,7,7c3.865,0,7-3.134,7-7S18.865,0,15,0z M15,10.5c-1.933,0-3.5-1.566-3.5-3.5  c0-1.933,1.567-3.5,3.5-3.5c1.932,0,3.5,1.567,3.5,3.5C18.5,8.934,16.932,10.5,15,10.5z" id="O"/></svg>';
                        break;

                    case "mobile":
                        icon += '<svg enable-background="new 0 0 64 64" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M48,39.26c-2.377,0-4.515,1-6.033,2.596L24.23,33.172c0.061-0.408,0.103-0.821,0.103-1.246c0-0.414-0.04-0.818-0.098-1.215  l17.711-8.589c1.519,1.609,3.667,2.619,6.054,2.619c4.602,0,8.333-3.731,8.333-8.333c0-4.603-3.731-8.333-8.333-8.333  s-8.333,3.73-8.333,8.333c0,0.414,0.04,0.817,0.098,1.215l-17.711,8.589c-1.519-1.609-3.666-2.619-6.054-2.619  c-4.603,0-8.333,3.731-8.333,8.333c0,4.603,3.73,8.333,8.333,8.333c2.377,0,4.515-1,6.033-2.596l17.737,8.684  c-0.061,0.407-0.103,0.821-0.103,1.246c0,4.603,3.731,8.333,8.333,8.333s8.333-3.73,8.333-8.333C56.333,42.99,52.602,39.26,48,39.26  z"/></svg>';
                        break;

                }

                return icon;

            },
			
			showShare: function(e, box, relative) {

                if ( ! e ) {
                    return false;
                }

				e = StiHooks.apply_filters( 'sti_share_container', e, { box: box } );

				methods.setBoxLayout.call(e, box, relative);

				$(box).show();

			},

			detectRightContainer: function(el) {

				var e = $(el);

				if ( e.closest('.sti').length > 0 ) return false;
				if ( methods.isMobile() && el.nodeName === 'IMG' ) {
					if ( e[0].naturalWidth < opts.minWidth || e[0].naturalHeight < opts.minHeight ) return false;
				} else {
					if ( e.width() < opts.minWidth || e.height() < opts.minHeight ) return false;
				}

				if ( e.closest('.nivoSlider').length ) {
					e = e.closest('.nivoSlider');
				}
				else if ( e.closest('.coin-slider').length ) {
					e = e.closest('.coin-slider');
				}
				else if ( e.closest('.woocommerce-product-gallery').length ) {
					e = e.closest('.woocommerce-product-gallery');
				}

				return e;

			},

			setBoxLayout: function( box, relative ) {

				var e = $(this);

				var offset = e.offset();
				var parentOffset = $('body').offset();
				var parentPosition = $('body').css('position');

				if ( relative ) {
					parentPosition = 'none';
					offset = e.position();
				}

				if ( offset && parentOffset  ) {

					var top = 0;
					var left = 0;

					if ( parentPosition === 'relative' ) {
						top = offset.top - parentOffset.top + parseInt( e.css('padding-top') );
						left = offset.left - parentOffset.left + parseInt( e.css('padding-left') );
					} else {
						top = offset.top + parseInt( e.css('padding-top') );
						left = offset.left + parseInt( e.css('padding-left') );
					}

					if ( parentPosition === 'none' ) {
						top = top + parseInt( e.css('margin-top') );
						left = left + parseInt( e.css('margin-left') );
					}

					$( box ).css({
						top : top,
						left: left
					});

				}

			},

			hideShare: function() {
				$('#'+stiBoxSingleSelector).hide();
			},

			closeMobileButtons: function() {
				$('.sti-mobile-btn').removeClass('sti-mobile-show');
			},

            replaceVariables: function( data, sstring ) {
                return sstring.replace('{{image_link}}', data.media)
                      .replace('{{page_link}}', data.link)
                      .replace('{{title}}', data.title)
                      .replace('{{summary}}', data.summary);
            },

			windowSize: function( network ) {
			
				switch( network ) { 			
					case "facebook" : return "width=670,height=320";
					break;

					case "messenger" : return "width=900,height=500";
					break;
					
					case "twitter" : return "width=626,height=252";
					break;

					case "linkedin" : return "width=620,height=450";
					break;
					
					case "delicious" : return "width=800,height=600";
					break;
					
					default: return "width=800,height=350";
					
				}	
				
			},
			
			replaceChars: function(string) {
				var str = string;
				if ( str && str !== '' ) {
					var specialCharsRegex = /[`~!@#$%^&*()_|+\-=?;:'",’<>\{\}\[\]\\\/]/gi;
					specialCharsRegex = StiHooks.apply_filters( 'sti_chars_remove_regex', specialCharsRegex, { str: str } );
					str = string.replace( specialCharsRegex, '' );
				}
				return str;
			},

            getBgImageURL: function(e) {

                var insideE = undefined;

                if ( e.css('background-image') ) {
                    insideE = e.css('background-image').replace('url(','').replace(')','').replace(/\"/gi, "");
                }

                return insideE;

            },

			fillFullPath: function( url ) {

				if ( url.indexOf( window.location.host ) == -1 && url.indexOf( 'http' ) != 0 && url.indexOf( 'www' ) != 0 ) {
					var root = window.location.href;
					if ( url.charAt(0) === '/' ) {
						root = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
					}
					url = root + url
				}

				return url;

			},

			checkForImgFullSize: function( url ) {
				var matches = url.match(/(-\d+?x\d+?)\.(png|jpg|jpeg|gif|svg)/);
				if ( matches ) {
					url = url.replace( matches[1], '' );
				}
				return url;
			},

			shareData: function(el, network) {

				var data    = {},
					e       =  currentImage ? $(currentImage) : ( currentImageElements && $(el).closest('.sti-top').data('el') ? currentImageElements[$(el).closest('.sti-top').data('el')] : ( $(el).closest('.sti-container') ? $(el).closest('.sti-container') : false ) ),
					caption = false,
                    captionText = false;

				e = StiHooks.apply_filters( 'sti_element', e );

				data.w_size = methods.windowSize( network );
				data.media  = e.data('media') ? e.data('media') : e[0].src;

                if ( typeof data.media === 'undefined' ) {

                    var insideE = methods.findMediaElement(e);

                    if ( insideE ) {
                        data.media = insideE.data('media') ? insideE.data('media') : ( insideE[0].src ? insideE[0].src : methods.getBgImageURL(insideE) );
                        e = insideE;
                    }

                }

                if ( data.media ) {
					data.media = methods.fillFullPath( data.media );
					data.media = methods.checkForImgFullSize( data.media );
				}

				caption = e.closest('.wp-caption');
				if ( caption.length ) {
					captionText = caption.find('.wp-caption-text').text();
				}

				data.e        =  e;
				data.hash     =  methods.getUrlHash( data );
				data.network  = network;
                data.title    =  e.data('title') ? e.data('title') : ( e.attr('title') ? e.attr('title') : ( opts.title ? opts.title : document.title ) );
                data.summary  =  e.data('summary') ? e.data('summary') : ( captionText ? captionText : ( e.attr('alt') ? e.attr('alt') : ( opts.summary ? opts.summary : '' ) ) );
				data.local    =  location.href.replace(/\?img.*$/, '').replace(/\&img.*$/, '').replace(/#.*$/, '').replace(/[\?|&]scroll.*$/, '');
				data.schar    =  ( data.local.indexOf("?") != -1 ) ? '&' : '?';
				data.ssl      =  data.media.indexOf('https://') >= 0 ? '&ssl=true' : '';
				data.link     =  e.data('url') ? e.data('url') : ( ( data.local.indexOf("?") != -1 ) ? data.local + data.hash : data.local + data.hash.replace(/&/, '?') );
				data.locUrl   =  e.data('url') ? '&url=' + encodeURIComponent( e.data('url') ) : '';
				data.page     =  opts.sharer ? opts.sharer + '?url=' + encodeURIComponent(data.link) + '&img=' + encodeURIComponent( data.media.replace(/^(http?|https):\/\//,'') ) + '&title=' + encodeURIComponent(methods.replaceChars(data.title)) + '&desc=' + encodeURIComponent(methods.replaceChars(data.summary)) + '&network=' + network + data.ssl + data.hash :
											   data.local + data.schar + 'img=' + encodeURIComponent( data.media.replace(/^(http?|https):\/\//,'') ) + '&title=' + encodeURIComponent(methods.replaceChars(data.title)) + '&desc=' + encodeURIComponent(methods.replaceChars(data.summary)) + '&network=' + network + data.locUrl + data.ssl + data.hash;

				data = StiHooks.apply_filters( 'sti_data', data );

				return data;

			},

			getUrlHash: function( data ) {
				if ( $(data.e).closest('.et_pb_gallery_item').length > 0 || $(data.e).closest('.mfp-container').length > 0 ) {
					return '&scroll=' + methods.createImgHash( data.media );
				}
				return '';
			},

			findMediaElement: function(e) {

                var insideE = false;

                if ( e.find('.coin-slider').length && e.find('.coin-slider .cs-title + a').length && e.find('.coin-slider .cs-title + a').css('background-image') ) {
                    insideE = e.find('.coin-slider .cs-title + a');
                }
                else if ( e.find('img.nivo-main-image').length ) {
                    insideE = e.find('img.nivo-main-image');
                }
                else if ( e.find('[data-media]').length ) {
                    insideE = e.find('[data-media]');
                }
                else if ( e.find('img').length ) {
                    insideE =  e.find('img');
                }

                return insideE;

            },

			share: function(network, data) {	
				
				var url = '';
					
				switch( network ) {
				
					case "facebook" :
                        url += 'http://www.facebook.com/sharer.php?u=';
                        url += encodeURIComponent(data.page);
					break;

					case "messenger" :
						if ( !methods.isMobile() ) {
							url += 'http://www.facebook.com/dialog/send?';
							url += 'link=' + encodeURIComponent(data.page);
							url += '&redirect_uri=' + encodeURIComponent(data.local+data.schar+'close=1');
						} else {
							url += 'fb-messenger://share/?';
							url += 'link=' + encodeURIComponent(data.page);
						}
						if ( opts.appId ) {
							url += '&app_id=' + encodeURIComponent( opts.appId );
						}
					break;

					case "linkedin" :
						url += 'http://www.linkedin.com/shareArticle?mini=true';
						url += '&url=' + encodeURIComponent(data.page);
					break;		
					
					case "vkontakte" :
						url += 'http://vk.com/share.php?';
						url += 'url=' + encodeURIComponent(data.link);
						url += '&title=' + encodeURIComponent(data.title);
						url += '&description=' + encodeURIComponent(data.summary);
						url += '&image=' + encodeURIComponent(data.media);
						url += '&noparse=true';
					break;

					case "odnoklassniki" :
						url += 'https://connect.ok.ru/offer';
						url += '?url=' + encodeURIComponent(data.link);
						url += '&title=' + encodeURIComponent(data.title);
						url += '&imageUrl=' + encodeURIComponent(data.media);
					break;
					
					case "twitter" :
						url += 'https://twitter.com/intent/tweet?';
						url += 'text=' + encodeURIComponent(data.title);
						url += '&url=' + encodeURIComponent(data.page);
						if (opts.twitterVia) {
						url += '&via=' + opts.twitterVia;
						}
					break;

					case "whatsapp" :
						url += 'https://api.whatsapp.com/send?';
						url += 'text=' + encodeURIComponent( data.title + ' | ' + data.summary + ' ' + data.page);
						break;

					case "telegram" :
						url += 'https://telegram.me/share/url';
						url += '?url=' + encodeURIComponent(data.link);
						url += '&text=' + encodeURIComponent(data.title + ' ' + data.media);
					break;

					case "pinterest" :
						url += 'http://pinterest.com/pin/create/button/?';
						url += 'url=' + encodeURIComponent(data.link);
						url += '&media=' + encodeURIComponent(data.media);
						url += '&description=' + encodeURIComponent(data.title);
					break;	
					
					case "tumblr" :
						url += 'http://www.tumblr.com/share/photo?';
						url += 'source=' + encodeURIComponent(data.media);
						url += '&caption=' + encodeURIComponent(data.summary);
						url += '&click_thru=' + encodeURIComponent(data.link);
					break;	
					
					case "reddit" :
						url += 'http://reddit.com/submit?';
						url += 'url=' + encodeURIComponent(data.link);
						url += '&title=' + encodeURIComponent(data.title);
						url += '&text=' + encodeURIComponent(data.summary);
					break;	
					
					case "digg" :
						url += 'http://digg.com/submit?phase=2&';
						url += 'url=' + encodeURIComponent(data.link);
						url += '&title=' + encodeURIComponent(data.title);
						url += '&bodytext=' + encodeURIComponent(data.summary);
					break;
					
					case "delicious" :
						url += 'http://delicious.com/post?';
						url += 'url=' + encodeURIComponent(data.link);
						url += '&title=' + encodeURIComponent(data.title);
					break;
					
				}

				if ( opts.custom_data && opts.custom_data.buttons && opts.custom_data.buttons[network] ) {
					url += opts.custom_data.buttons[network]['link'];
					url = methods.replaceVariables( data, url );
					if ( opts.custom_data.buttons[network]['blank'] ) {
						window.open( url, '_blank' );
						return;
					}
				}

				url = StiHooks.apply_filters( 'sti_sharing_url', url, { data: data } );

				methods.openPopup(url, data);
				
			},

			openPopup: function(url, data) {
				var win = window.open( url, 'Share This Image', data.w_size + ',status=0,toolbar=0,menubar=0,scrollbars=1' );
				var timer = setInterval( function() {
					if( win.closed ) {
						clearInterval( timer );
						methods.createAndDispatchEvent( document, 'stiSharingWindowClosed', { url: url, data: data } );
					}
				}, 1000);
			},
			
			analytics: function( category, label ) {
				if ( opts.analytics ) {
					try {
						if ( typeof gtag !== 'undefined' && gtag !== null ) {
							gtag('event', 'STI click', {
								'event_label': label,
								'event_category': category,
								'transport_type' : 'beacon'
							});
						}
						if ( typeof ga !== 'undefined' && ga !== null ) {
							ga('send', 'event', 'STI click', category, label);
						}
						if ( typeof _gaq !== 'undefined' && _gaq !== null ) {
							_gaq.push(['_trackEvent', 'STI click', category, label ]);
						}
						if ( typeof pageTracker !== "undefined" && pageTracker !== null ) {
							pageTracker._trackEvent( 'STI click', category, label )
						}
						// This uses Monster Insights method of tracking Google Analytics.
						if ( typeof __gaTracker !== 'undefined' && __gaTracker !== null ) {
							__gaTracker( 'send', 'event', 'STI click', category, label );
						}
					}
					catch (error) {
					}
				}
			},

			createCustomEvent: function( event, params ) {

				var customEvent = false;
				params = params || null;

				if ( typeof window.CustomEvent === "function" ) {
					customEvent = new CustomEvent( event, { bubbles: true, cancelable: true, detail: params } );

				}
				else if ( document.createEvent ) {
					customEvent = document.createEvent( 'CustomEvent' );
					customEvent.initCustomEvent( event, true, true, params );
				}

				return customEvent;

			},

			createAndDispatchEvent: function( obj, event, params ) {

				var customEvent = methods.createCustomEvent( event, params );

				if ( customEvent ) {
					obj.dispatchEvent( customEvent );
				}

			},

			relayoutButtons: function() {
				if ( opts.position === 'image' || ( opts.position === 'image_hover' && methods.isMobile() ) ) {
					$('.sti-top').each(function() {
						var el = $(this).prev();
						var elId = $(this).attr('id');
						methods.setBoxLayout.call(el, '#' + elId, true);
					});
				}
			},

			isMobile: function() {
				var check = false;
				(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
				return check;
			},

			getUrlParams: function() {

				var urlParams = {};
				var match,
					pl = /\+/g,  // Regex for replacing addition symbol with a space
					search = /([^&=]+)=?([^&]*)/g,
					decode = function (s) {
						return decodeURIComponent(s.replace(pl, " "));
					},
					query = window.location.search.substring(1);

				while (match = search.exec(query)) {
					urlParams[decode(match[1])] = decode(match[2]);
				}

				return urlParams;

			}
			
		};


		if ( options === 'relayout' ) {
			methods.relayoutButtons();
		}

		stiBoxSingleSelector = 'sti-box-s-' + methods.stringToId(opts.selector);

		if ( !methods.isMobile() ) {

			if ( opts.position !== 'image_hover' ) {
				var i = 0;
				this.each(function() {

					var el = methods.detectRightContainer(this);

					if ( el && ! el.next().hasClass('sti') ) {

						do {
							i++
						} while( $('#sti-box-'+i).length > 0 );

						currentImageElements[i] = el;

						el.after('<div data-el="'+i+'" id="sti-box-'+i+'" class="sti sti-top style-flat-small sti-inside" style="display: none;"><div class="sti-share-box">' + methods.shareButtons() + '</div></div>');
						methods.showShare(el, '#sti-box-' + i, true);

					}

				});
			
			} else {

				if ( ! $('#'+stiBoxSingleSelector).length ) {
					$(appendButtonsTo).append('<div id="'+stiBoxSingleSelector+'" class="sti sti-top sti-hover style-flat-small" style="display: none;"><div class="sti-share-box">' + methods.shareButtons() + '</div></div>');
				}

				$(document).on('mouseenter', opts.selector, function(e) {
					e.preventDefault();
					var el = methods.detectRightContainer(this);
					if ( $('.sti-hover:visible').length === 0 ) {
						methods.showShare(el, '#'+stiBoxSingleSelector);
					}
					currentImage = this;
				});
					
				$(document).on('mouseleave', opts.selector, function(e) {
					e.preventDefault();
					var target = e.relatedTarget || e.toElement;
					if ( ! $(target).closest('.sti').length ) {
						methods.hideShare();
					}
				});

				$(document).on('mouseleave', '.sti', function(e) {
					e.preventDefault();
					var target = e.relatedTarget || e.toElement;
					if ( !( currentImage && target == currentImage ) ) {
						methods.hideShare();
					}
				});

			}
		
		} else {

			if ( ! $('#'+stiBoxSingleSelector).length ) {
				$(appendButtonsTo).append('<div id="'+stiBoxSingleSelector+'" class="sti sti-top sti-mobile style-flat-small" style="display: none;"><div class="sti-share-box">' + methods.shareButtons() + '</div></div>');
			}

			var i = 0;

			this.each(function() {

				var el = methods.detectRightContainer(this);

				if ( el && ! el.next().hasClass('sti-mobile-btn') ) {

					do {
						i++
					} while( $('#sti-mobile-btn-'+i).length > 0 );

					currentImageElements[i] = el;

					el.after('<div data-el="'+i+'" data-box="'+stiBoxSingleSelector+'" id="sti-mobile-btn-'+i+'" class="sti-top sti-mobile-btn" style="display: none;">' + methods.getSvgIcon( 'mobile' ) + '</div>');
					methods.showShare(el, '#sti-mobile-btn-' + i, true);

				}

			});

			$('.sti-mobile-btn').on('click touchend', function(e) {
				e.preventDefault();
				currentImage = $(this).prev();
				//methods.hideShare();
				$('.sti-mobile').hide();
				var stiBox = $(this).data('box');
				if ( $('.sti-mobile:visible').length === 0 ) {
					methods.showShare(currentImage, '#'+stiBox);
					methods.closeMobileButtons();
					$(this).addClass('sti-mobile-show');
				}
			});

			$(opts.selector).on('click touchend', function(e) {
				//methods.hideShare();
				$('.sti-mobile').hide();
				methods.closeMobileButtons();
			});
		
		}


		// STI sharing buttons initialized
		methods.createAndDispatchEvent( document, 'stiInit');


		$('.sti-btn, a[href^="#sti-"]').on('click touchend', function(e) {
			e.preventDefault();
            e.stopPropagation();
			e.stopImmediatePropagation();

			var network = $(this).data('network');

			network = StiHooks.apply_filters( 'sti_network', network, { el: this } );

			var data = methods.shareData(this, network);

            methods.share(network, data);

			methods.analytics( network, data.media );

			methods.createAndDispatchEvent( this, 'stiButtonClick', { button: network, data: data } );

        });

		$( window ).resize(function() {
			methods.relayoutButtons();
		});

		$( window ).scroll(function() {
			if ( opts.position === 'image_hover' && ! methods.isMobile() && $('#'+stiBoxSingleSelector).is(':visible') ) {
				var el = methods.detectRightContainer( currentImage );
				methods.showShare(el, '#'+stiBoxSingleSelector);
			}
		});

		methods.scrollToImage(this);

	};

    // Call plugin method
	$(window).on('load', function() {
        $(selector).sti();
    });


    // Support for third party plugins
    $(document).on('shown.simplelightbox doLightboxViewImage modula_lightbox2_lightbox_open theiaPostSlider.changeSlide, envirabox-change, elementor/popup/show', function() {
		setTimeout(function() {
			$(selector).sti();
		}, 500);
    });


	$(window).on('load', function() {

		var $imageLinks = $('a img, a.popup-image');
		var element = false;
		var lightboxesSelector = '.nivo-lightbox-image:visible, .slb_content:visible, .mfp-img:visible, #envirabox-img:visible';
		var watchToElements = '.wp-block-jetpack-slideshow, .jp-carousel-wrap, .wp-block-envira-envira-gallery';

		if ( sti_vars.position === 'image' ) {

			$imageLinks.on('click', function() {
				element = false;
				var currentImg = this;
				setTimeout(function() {
					var $lightboxes = $(lightboxesSelector);
					if ( $lightboxes.length > 0 ) {
						$(selector).sti();
						if ( typeof StiHooks === 'object' && typeof StiHooks.add_filter === 'function' ) {
							element = $(currentImg);
							StiHooks.add_filter( 'sti_element', sti_element );
						}
					}
				}, 1000);
			});

			var timeoutID;
			$('body').on('DOMSubtreeModified', watchToElements, function() {
				window.clearTimeout(timeoutID);
				timeoutID = window.setTimeout( function() {
					$(selector).sti();
				}, 1000 );
			});

		}

		// Lazy load support
		var lazyTimeoutID;
		$('img').on('load', function(){
			window.clearTimeout(lazyTimeoutID);
			lazyTimeoutID = window.setTimeout( function() {
				$(selector).sti('relayout');
			}, 100 );
		});

		function sti_element( value ) {
			var $lightboxes = $(lightboxesSelector);
			if ( element && $lightboxes.length > 0 ) {
				return element;
			}
			return value;
		}

		// Support for Ajax Load More Plugin
		window.almComplete = function(alm){
			setTimeout(function() {
				$(selector).sti();
			}, 200);
		};

	});

})( jQuery );