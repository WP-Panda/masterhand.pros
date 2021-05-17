<?php
/**
 * Use for page author.php and page-profile.php
 */
global $wp_query, $ae_post_factory, $post;
$current_user = wp_get_current_user();

$wp_query->query = array_merge($wp_query->query, array('posts_per_page' => 6));

$post_object = $ae_post_factory->get('portfolio');
$is_edit = false;
if (is_author()) {
    $author_id = get_query_var('author');
} else {
    $author_id = get_current_user_id();
    $is_edit = true;
}

$query_args = array(
    // 'post_parent' => $convert->ID,
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'post_type' => PORTFOLIO,
    'author' => $author_id,
    'is_edit' => $is_edit
);

query_posts($query_args);
//echo '<pre>' . var_dump($current_user) . '</pre>';

//new2
//if (function_exists('count_portfolio')) {
//    $cp = count_portfolio($current_user);
//    // $cp = 2;
//}

if (function_exists('set_function_for_add_pro_status')) {
    $pro_status = get_user_pro_status($user_ID);
    $count_portfolio = getValueByProperty($pro_status, 'work_in_portfolio');
    $flag = !$count_portfolio ? false : true;
} else $flag = false;
//new2

if (have_posts() or $is_edit) {
    ?>
    <div class="fre-profile-box">
        <div class="portfolio-container">
            <div class="profile-freelance-portfolio">
                <div class="row">

                    <div class="<?php echo $is_edit ? 'col-sm-6' : '' ?> col-xs-12">
                        <div class="freelance-portfolio-title"><?php _e('Portfolio', ET_DOMAIN) ?></div>
                    </div>
                    <?php
                    if ($is_edit) {
                        if (!$flag || count(query_posts($query_args)) < $count_portfolio) {
                            ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="freelance-portfolio-add">
                                    <a href="#"
                                       class="btn-right fre-submit-btn portfolio-add-btn add-portfolio"><?php _e('Add new', ET_DOMAIN); ?></a>
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <div class="col-sm-6 col-xs-12">
                                <div class="freelance-portfolio-add text-right error-message"><?php _e('You can add only ' . $count_portfolio . ' works', ET_DOMAIN); ?></div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <?php if (!have_posts() and $is_edit) { ?>
                    <p class="fre-empty-optional-profile"><?php _e('Add portfolio to your profile. (optional)', ET_DOMAIN) ?></p>
                <?php } else { ?>
                    <ul class="freelance-portfolio-list row">
                        <?php
                        $postdata = array();
                        while (have_posts()) {
                            the_post();
                            $convert = $post_object->convert($post, 'thumbnail');
                            $postdata[] = $convert;
                            get_template_part('template/portfolio', 'item');
                        }
                        ?>
                    </ul>
                <?php } ?>

                <?php
                if (!empty($postdata) && $wp_query->max_num_pages > 1) {
                    /**
                     * render post data for js
                     */
                    echo '<script type="data/json" class="postdata portfolios-data" >' . json_encode($postdata) . '</script>';

                    echo '<div class="freelance-portfolio-loadmore">';
                    ae_pagination($wp_query, get_query_var('paged'), 'load_more', 'View more');
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
<?php } ?>

