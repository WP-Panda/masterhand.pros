<?php

/**

 * The Sidebar containing widget area on static page left side

 *

 * @package FreelanceEngine

 * @since 1.0

 */

?>





<?php if ( is_active_sidebar( 'sidebar-page' ) ) : ?>

<div class="primary-sidebar widget-area" role="complementary">

    <div class="hidden-xs fre-profile-box"><?php dynamic_sidebar( 'sidebar-page' ); ?></div>

	<?php if (is_user_logged_in()) { ?>

   <!--  <a class="fre-submit-btn" href="<?php echo bloginfo('url') .'/contact-us/';?>">

        <span class="visible-xs fa fa-pencil"></span><span class="hidden-xs">Contact us</span>

    </a> -->

    <?php } else { ?>

  <!--   <a class="fre-submit-btn" href="<?php echo bloginfo('url') .'/login/';?>">

        <span class="visible-xs fa fa-pencil"></span><span class="hidden-xs">Contact us</span>

    </a>    --> 

    <?php } ?>

</div>
<!-- #primary-sidebar -->

<?php endif; ?>

