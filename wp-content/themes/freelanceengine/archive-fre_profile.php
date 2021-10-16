<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link       http://codex.wordpress.org/Template_Hierarchy
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$wp_query->query['post_status']    = 'publish';
$wp_query->query['post_']          = PROFILE;
$wp_query->query['with_companies'] = true;
$loop                              = new WP_Query( $wp_query->query );
get_header();
?>
    <div class="fre-page-wrapper section-archive-profile with_company">
        <div class="fre-page-title profs-cat">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <div class="profs-cat_t">
                            <h1><?php _e( 'Available Profiles', ET_DOMAIN ); ?></h1>
							<?php _e( 'find a professional here.', ET_DOMAIN ) ?>
                        </div>
                    </div>
                    <div class="col-sm-6 hidden-xs">
                        <div class="profs-cat_desc">
                            <a class="profs-cat_link"
                               href="<?php echo get_option( 'siteurl' ) . '/profile_category'; ?>">
								<?php echo __( 'Professionals by category', ET_DOMAIN ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fre-page-section">
            <div class="container">
                <div class="page-profile-list-wrap">
                    <div class="fre-profile-list-wrap">
						<?php get_template_part( 'template/filter', 'profiles' ); ?>
                        <div class="fre-profile-list-box">
                            <div class="fre-profile-result-sort">
                                <div class="row">
									<?php
									$query_post  = $loop->found_posts;
									$found_posts = '<span class="found_post">' . $query_post . '</span>';
									$plural      = sprintf( __( '%s profiles available', ET_DOMAIN ), $found_posts );
									$singular    = sprintf( __( '%s profile available', ET_DOMAIN ), $found_posts );
									$not_found   = sprintf( __( 'There are no available profiles on this site!', ET_DOMAIN ), $found_posts );
									?>
                                    <div class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6 col-sm-6 col-sm-push-6 hidden-xs">
										<?php if ( $query_post >= 1 ) { ?>
                                            <div id="profile_orderby" class="fre-profile-sort">
                                                <select class="hidden sort-order" name="orderby">
                                                    <option value="date"><?php _e( 'Newest Profiles', ET_DOMAIN ); ?></option>
                                                    <option value="hour_rate"><?php _e( 'Highest Hourly Rate', ET_DOMAIN ); ?></option>
                                                    <option value="rating"><?php _e( 'Highest Rating', ET_DOMAIN ); ?></option>
                                                    <option value="projects_worked"><?php _e( 'Most Projects Worked', ET_DOMAIN ); ?></option>
                                                </select>
                                                <span class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="hour_rate"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="rating"><?php _e( 'Ranking', ET_DOMAIN ); ?></span>
                                                <span class="option"
                                                      id="projects_worked"><?php _e( 'Projects', ET_DOMAIN ); ?></span>
                                            </div>
                                            <button class="fre-submit-btn btn-get-quotes btn-right"><?php _e( 'Get Multiple Quotes' ); ?></button>
										<?php } ?>
                                    </div>
                                    <div class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6 col-sm-6 col-sm-pull-6 col-xs-12">
                                        <div class="fre-profile-result">
                                               
                                                    <span class="plural <?php if ( $query_post == 1 ) {
	                                                    echo 'hide';
                                                    } ?>"><?php if ( $query_post < 1 ) {
		                                                    echo $not_found;
	                                                    } else {
		                                                    echo $plural;
	                                                    } ?></span>


                                            <div class="visible-xs">
												<?php if ( $query_post >= 1 ) { ?>
                                                    <div id="profile_orderby" class="fre-profile-sort">
                                                        <span class="sort-label"><?php _e( 'Sort by:', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="hour_rate"><?php _e( 'Price', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="rating"><?php _e( 'Ranking', ET_DOMAIN ); ?></span>
                                                        <span class="option"
                                                              id="projects_worked"><?php _e( 'Projects', ET_DOMAIN ); ?></span>
                                                    </div>
                                                    <button class="fre-submit-btn btn-get-quotes btn-right"><?php _e( 'Get Multiple Quotes' ); ?></button>
												<?php } ?>
                                            </div>
                                            <span class="singular <?php if ( $query_post > 1 || $query_post < 1 ) {
												echo 'hide';
											} ?>"><?php echo $singular; ?></span>

                                        </div>
                                    </div>
                                </div>
                            </div>
							<?php get_template_part( 'list', 'profiles' ); ?>
                        </div>
                    </div>
					<?php
					echo '<div class="fre-paginations paginations-wrapper">';
					ae_pagination( $loop, get_query_var( 'paged' ) );
					echo '</div>';
					?>
                </div>
            </div>
        </div>
    </div>
    <script>
        /*sticky-block*/
        var a = document.querySelector('.btn-get-quotes'), b = null, P = 90;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
        window.addEventListener('scroll', Ascroll, false);
        document.body.addEventListener('scroll', Ascroll, false);

        function Ascroll() {
            if (b == null) {
                var Sa = getComputedStyle(a, ''), s = '';
                for (var i = 0; i < Sa.length; i++) {
                    if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
                        s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
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
                R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('footer').getBoundingClientRect().top + 95);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
            if ((Ra.top - P) <= 0) {
                if ((Ra.top - P) <= R) {
                    b.className = 'stop-bl';
                    b.style.top = -R + 'px';
                } else {
                    b.className = 'sticky-bl';
                    b.style.top = P + 'px';
                }
            } else {
                b.className = '';
                b.style.top = '';
            }
            window.addEventListener('resize', function () {
                a.children[0].style.width = getComputedStyle(a, '').width
            }, false);
        }

    </script>
<?php
get_footer();