<?php
/**
 *
 * Template Name: Payment Completed
 * Created by PhpStorm.
 * User: User
 * Date: 28.02.2019
 * Time: 21:54
 *
 */

get_header();
?>
    <section class="blog-header-container">
        <div class="container">
            <!-- blog header -->
            <div class="row">
                <div class="col-md-12 blog-classic-top">
                    <h1><?php the_title(); ?></h1>
                </div>
            </div>
            <!--// blog header  -->
        </div>
    </section>
    <!-- Page Blog -->
    <section id="blog-page">
        <div class="container page-container">
            <!-- block control  -->
            <div class="row block-posts block-page">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="content-cancel-payment">
                        <h2><?php _e( "PAYMENT COMPLETED", ET_DOMAIN ); ?></h2>
                        <p class="sub-text"><?php _e( 'Thank you for upgrade your account. Redirect to the homepage within a <span class="count_down">10</span> seconds', ET_DOMAIN ); ?></p>
                        <div class="content-footer">
                            <a class="fre-btn btn-center fre-submit-btn"
                               href="<?php echo get_page_url( 'page-profile' ); ?>"><?php _e( 'Return to Profile', ET_DOMAIN ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var $count_down = jQuery('.count_down');
            setTimeout(function () {
                window.location = '<?php echo get_page_url( 'page-profile' );?>';
            }, 10000);
            setInterval(function () {
                if ($count_down.length > 0) {
                    var i = $count_down.html();
                    if (parseInt(i) > 0) {
                        $count_down.html(parseInt(i) - 1);
                    }
                }
            }, 1000);
        });
    </script>
<?php get_footer();