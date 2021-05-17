<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $post;

$cc=!empty(trim(strip_tags($post->post_content))) ? str_replace(array("\r\n", "\r", "\n"), ' ', trim(strip_tags($post->post_content))) : 'MasterHand';

echo "<script>
    var meta = document.createElement('meta');
    meta.name = \"description\";
    meta.content = \"$cc\";
    document.getElementsByTagName('head')[0].appendChild(meta);
    
    document.getElementsByTagName('head')[0].innerHTML+='<meta property=\"og:description\" content = \"$cc\">';
</script>";

get_header();
the_post();
wp_enqueue_script('likesUsers');
?>

<div class="fre-page-wrapper">
    <div class="container">
        <div class="cats-list">
            <?php $taxonomy = 'category';
            $terms = get_terms(array('taxonomy'=>$taxonomy, 'hide_empty'  => 0, 'parent' => 1));
                if ( $terms && !is_wp_error( $terms ) ) :?>
                    <div class="row">
                        <?php foreach ( $terms as $term ) {
                                  $termid = $term->term_id;?>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="profs-cat-list_t text-center">
                                      <a href="<?php echo get_term_link($term->slug, $taxonomy); ?>" style="background:#fff url(<?php the_field('catic',$taxonomy . '_' . $termid);?>) 40px center no-repeat;">
                                       <?php echo $term->name; ?></a>
                                    </div>
                                </div>
                        <?php } ?>
                    </div>
               <?php endif;?>
        </div>

        <?php echo blog_breadcrumbs();?>

        <div class="block-posts" id="post-control">
            <h1 class="title-blog"><?php the_title() ?></h1>
            <div class="subtitle-blog"><?php the_title() ?></div>
            <div class="fre-blog-item_img" <?php if(has_post_thumbnail()) {?> style="background:url(<?php the_post_thumbnail_url();?>) center no-repeat;" <?php } ?> alt="<?php the_title();?>"/>
                <div class="fre-blog-item_cat"><?php $category = get_the_category(); echo $category[0]->cat_name;?></div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-xs-12">
                    <div class="row blog-wrapper">
                        <div class="col-sm-2 col-xs-3 avatar-author">
                            <div class="stories-img" style="background:url(<?php if(has_post_thumbnail($post)){  echo get_avatar_url($post->post_author);} else { echo get_template_directory_uri().'/img/noimg.png';}?>) center no-repeat;"></div>
                        </div>
                        <div class="col-sm-3 col-xs-9"><?php the_author();?></div>
                        <div class="col-sm-3 col-xs-9 date"><?php the_date();?></div>
    <!--                    <div class="col-sm-4 hidden-xs text-right sharing"></div>-->
                    </div>
                    <div class="blog-content"><?php the_content();?></div>
                    <?php likesPost(get_the_ID()) ?>
                    <div class="text-right sharing">
                        <?php
                        if (function_exists('ADDTOANY_SHARE_SAVE_KIT')) {
                            ADDTOANY_SHARE_SAVE_KIT();
                        }
                        ?>
                    </div>
                </div>
                <div class="col-sm-4 category hidden-xs post-sidebar" id="right_content">
                    <?php get_sidebar('blog'); ?>
                    <?php $query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 7, 'post__not_in' => array($post->ID),'cat' => $category[0]->cat_id)); ?>
                        <div class="fre-blog-list-sticky">
                            <?php  while($query->have_posts()) { $query->the_post();
                                get_template_part('template/blog', 'stickynoimg');
                            }?>
                        </div>
                    <?php wp_reset_query();?>
                </div>
            </div>
            <?php comments_template();?>
        </div>

        <div class="fre-blog fre-blog-fst_bl">
            <?php $query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 10, 'post__not_in' => array($post->ID),'cat' => $category[0]->cat_id)); ?>
                 <div class="profs-cat_t"><span><?php echo __('Related news', ET_DOMAIN);?></span></div>
                    <div class="fre-blog-list owl-carousel">
                        <?php  while($query->have_posts()) { $query->the_post();
                            get_template_part('template/blog', 'item');
                        }?>
                    </div>
            <?php wp_reset_query();?>
        </div>

        <!-- Mailster subscribtion form -->
        <div class="fre-blog-subscribe-form mailster-subscribe__block">
            <div class='fre-blog-subscribe-form_title'><?php echo __('Subscribe', ET_DOMAIN)?></div>
            <div class="emaillist">
                <?php echo mailster_form(3); ?>
            </div>
        </div>

    </div>
</div>
<?php get_footer();?>