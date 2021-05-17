<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */

global $post;
$session = et_read_session();
get_header();
if ( isset( $session['project_id'] ) ) {
	et_destroy_session( 'project_id' );
}
if ( isset( $_REQUEST['project_id'] ) ) {
	// save Session
	et_write_session( 'project_id', $_REQUEST['project_id'] );
}

the_post();
?>

    <div class="container page-container">
        <!-- block control  -->
        <div class="row block-posts block-page">
			<?php
			if ( is_social_connect_page() ) {
				the_content();
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				) );
			} else { ?>
                <div class="col-sm-3 col-xs-12 page-sidebar" id="right_content">
				    <?php get_sidebar( 'page' ); ?>
                   <!-- <button class="visible-xs blog-sidebar_more"><?php echo __( 'More information', ET_DOMAIN );?><i class="fa fa-angle-down animated-hover fa-falling"></i></button>-->
                </div><!-- RIGHT CONTENT -->
                <div class="col-sm-9 col-xs-12 posts-container" id="left_content">
                    <div class="fre-profile-box blog-content">
             		    <h1 class="text-center"><?php the_title(); ?></h1>
						<?php
						the_content();
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
						?>

                        <div class="clearfix"></div>
                    </div><!-- end page content -->
                </div><!-- LEFT CONTENT -->
                <div class="page-sidebar visible-xs col-xs-12">
                    <?php if (is_user_logged_in()) { ?>
                        <a class="center-btn fre-submit-btn" href="<?php echo bloginfo('url') .'/contact-us/';?>">Contact us</a>
                    <?php } else { ?>
                        <a class="center-btn fre-submit-btn" href="<?php echo bloginfo('url') .'/login/';?>">Contact us</a>    
                    <?php } ?>
                </div>    
			<?php } ?>
        </div>
        <!--// block control  -->
    </div>
<script>

	(function ($) {    
    var headr = 0;    
  	/*sticky-block*/
    var a = document.querySelector('.page-sidebar'), b = null, P = 90;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
    window.addEventListener('scroll', Ascroll, false);
    document.body.addEventListener('scroll', Ascroll, false);
    
    function Ascroll() {
      if (b == null) {
        var Sa = getComputedStyle(a, ''), s = '';
        for (var i = 0; i < Sa.length; i++) {
          if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
            s += Sa[i] + ': ' +Sa.getPropertyValue(Sa[i]) + '; '
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
          R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('footer').getBoundingClientRect().top + 115);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
      if ((Ra.top - P) <= 0) {
        if ((Ra.top - P) <= R) {
          b.className = 'stop-bl';
          b.style.top = - R +'px';
        } else {
          b.className = 'sticky-bl';
          b.style.top = P  + headr + 'px';
        }
      } else {
        b.className = '';
        b.style.top = '';
      }
      window.addEventListener('resize', function() {
        a.children[0].style.width = getComputedStyle(a, '').width
      }, false);
    }

})(jQuery);

</script>
    
<?php
get_footer();
?>