<?php
    /**
     * Template list profiles
     */

    global $wp_query, $inner_query, $ae_post_factory, $query;

    $term_category = $wp_query->query_vars['project_category'];

    $post_object = $ae_post_factory->get(PROFILE);

    #echo '<pre>'; var_export($inner_query); echo '</pre>';
?>
<div class="row profile-list-container">
<?php
    $postdata = array();
    $count_profiles = 0;

    // if request from /profile_category/.../ page
    if (isset($inner_query)){

        // users loop
        if ($inner_query->have_posts()){
            while ($inner_query->have_posts()) {
                $inner_query->the_post();
                $post = $inner_query->post;

                if ($post->post_type != PROFILE){
                    continue;
                }

                $postdata[] = $post_object->convert($post);
                $count_profiles++;

                get_template_part('template/profile', 'item');
            }

            rewind_posts();


            // companies loop
            $first_post = true;
            while ($inner_query->have_posts()){
                $inner_query->the_post();
                $post = $inner_query->post;

                if ($post->post_type != COMPANY){
                    continue;
                }

                $postdata[] = $post_object->convert($post);
                $count_profiles++;
            ?>

                <div class="col-md-12 col-sm-12 col-xs-12 profile-item out in">
                    <?php if ($first_post){
                        $first_post = false ?>
                        <div class="company-item project-item">
                            <div class="project-content fre-freelancer-wrap" style="max-height:100px">
                                <div class="fre-location text-center">Best Companies &amp; Prices in Town</div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php get_template_part('template/company', 'item')?>
                </div>
            <? }

        }
    } else {
        // if request from /profiles/ page
        if (have_posts()){
            while (have_posts()) {
                the_post();

                $convert = $post_object->convert($post);

                $postdata[] = $convert;
                $count_profiles++;
                get_template_part('template/profile', 'item');
            }
        }
    }
?>
</div>

<div class="profile-no-result" style="display: none;">
    <div class="profile-content-none">
        <p><?php _e( 'There are no results that match your search!', ET_DOMAIN ); ?></p>
        <ul>
            <li><?php _e( 'Try more general terms', ET_DOMAIN ) ?></li>
            <li><?php _e( 'Try another search method', ET_DOMAIN ) ?></li>
            <li><?php _e( 'Try to search by keyword', ET_DOMAIN ) ?></li>
        </ul>
    </div>
</div>

<?php wp_reset_query(); ?>
<?php
/**
 * render post data for js
 */
echo '<script type="data/json" class="postdata" >' . json_encode( $postdata ) . '</script>';

get_template_part('template-js/modal', 'get-queote');