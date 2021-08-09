<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Time: 20:32
	 */

	function wpp_fr_top_bottom() { ?>
        <a href="#" class="layout__elevator" id="scroll_to_top" title="Наверх">
            <svg class="icon-svg icon-svg_scroll-up" width="32" height="32" viewBox="0 0 32 32" aria-hidden="true"
                 version="1.1" role="img">
                <path d="M16 0C7.164 0 0 7.164 0 16s7.164 16 16 16 16-7.164 16-16S24.836 0 16 0zm8.412 19.523c-.517.512-1.355.512-1.872 0L16 13.516l-6.54 6.01c-.518.51-1.356.51-1.873 0-.516-.513-.517-1.343 0-1.855l7.476-7.326c.517-.512 1.356-.512 1.873 0l7.476 7.327c.516.513.516 1.342 0 1.854z"></path>
            </svg>
        </a>

        <style>
            .layout__elevator {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1;
                display: none;
                width: 60px;
                height: 100%;
                color: #d5dddf;
                text-align: center;
                transform: translateY(0);
                will-change: transform;
            }

            .layout__elevator:hover {
                background: #f5f7f8;
                color: #a6b5ba;
            }

            @media only screen and (min-width: 1234px) {
                .layout__elevator {
                    display: block;
                }
            }
        </style>
        <script>
            ;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});
            jQuery(function(s){lastScrollPosition=0;var l=s("#scroll_to_top"),o=0,a=!1;l.on("click",function(o){o.preventDefault(),l.hasClass("back-down")?(l.attr("title","SCROLL_TOP").removeClass("back-down"),s("html,body").scrollTo(lastScrollPosition,700,{axis:"y"}),lastScrollPosition=0):(lastScrollPosition=window.pageYOffset,l.attr("title","SCROLL_DOWN").addClass("back-down"),s("html,body").scrollTo(0,700,{axis:"y"}))}),s(window).on("scroll",function(){100<window.pageYOffset?a||(l.removeClass("hidden"),a=!0):a&&(l.addClass("hidden"),a=!1),o<window.pageYOffset&&l.hasClass("back-down")&&l.removeClass("back-down"),o=window.pageYOffset})});
        </script>
	<?php }

	add_action( 'wp_footer', 'wpp_fr_top_bottom', 100000 );