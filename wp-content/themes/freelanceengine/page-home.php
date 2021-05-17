<?php
/**
 * Template Name: Front Page Template
*/
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID;?>
<div class="fre-page-wrapper">
<?php get_template_part('template/main', 'block');?>

<div class="fre-how-work">
    <div class="fre-how-work_bg hidden-xs"  style="background:url(<?php if (get_theme_mod("img_back") ) { echo get_theme_mod("img_back"); } else { echo get_template_directory_uri(). '/img/main_bg2.jpg';}?>) top left no-repeat;"></div>
	<div class="container">
        <div class="row">
           
            <div class="col-sm-12 col-md-4 col-lg-4 col-xs-12"  style="background:url(<?php if (get_theme_mod("img_back") ) { echo get_theme_mod("img_back"); } else { echo get_template_directory_uri(). '/img/main_bg2.jpg';}?>) top left no-repeat;">
                <?php if (get_theme_mod("title_work") ) {?>
                     <h4 class="fre-how-work_t"><?php echo get_theme_mod("title_work");?></h4>
                 <?php } ?>             
                 <div class="fre-how-work_desc"><?php echo get_theme_mod("desc_work_4");?></div>
            </div>
                                         	      
            <div class="col-sm-12 col-md-8 col-lg-8 col-xs-12">
                 <div class="fre-how-work_list row">
                    <?php if ((get_theme_mod("desc_work_1"))||(get_theme_mod("title_work_1"))) {?>  
				        <div class="col-sm-4 col-md-6 col-lg-6 col-xs-12">
                           <div class="fre-how-work_list_wp">
                               <div class="fre-how-work_list_bg" style="background:url(<?php if (get_theme_mod("img_work_1") ) { echo get_theme_mod("img_work_1"); } else { echo get_template_directory_uri(). '/img/work1.png';}?>) center no-repeat;"></div>
                               <div class="fre-how-work_list_t"><?php echo get_theme_mod("title_work_1");?></div>
                               <div class="fre-how-work_list_txt"><?php echo get_theme_mod("desc_work_1");?></div>
                            </div>
                        </div>
                    <?php }?>	
                     <?php if ((get_theme_mod("desc_work_2"))||(get_theme_mod("title_work_2"))) {?>  
				        <div class="col-sm-4 col-md-6 col-lg-6 col-xs-12">
                           <div class="fre-how-work_list_wp">
                                <div class="fre-how-work_list_bg" style="background:url(<?php if (get_theme_mod("img_work_2") ) { echo get_theme_mod("img_work_2"); } else { echo get_template_directory_uri(). '/img/work2.png';}?>) center no-repeat;"></div>
                                <div class="fre-how-work_list_t"><?php echo get_theme_mod("title_work_2");?></div>
                                <div class="fre-how-work_list_txt"><?php echo get_theme_mod("desc_work_2");?></div>
                            </div>
                        </div>
				    <?php }?>	
                    <?php if ((get_theme_mod("desc_work_3"))||(get_theme_mod("title_work_3"))) {?>  
				       <div class="col-sm-4 col-md-6 col-lg-6 col-xs-12">
                           <div class="fre-how-work_list_wp">
                                 <div class="fre-how-work_list_bg" style="background:url(<?php if (get_theme_mod("img_work_3") ) { echo get_theme_mod("img_work_3"); } else { echo get_template_directory_uri(). '/img/work3.png';}?>) center no-repeat;"></div>
                                <div class="fre-how-work_list_t"><?php echo get_theme_mod("title_work_3");?></div>
                               <div class="fre-how-work_list_txt"><?php echo get_theme_mod("desc_work_3");?></div>
                           </div>
                        </div>
				    <?php }?>	
                 </div>
            </div>   
           
        </div>
    </div>
</div>

<div class="profs-cat">
    <div class="container">          
       <div class="row">
           <div class="col-sm-6 col-xs-12">
                <div class="profs-cat_t">
                    <span><?php _e('Categories', ET_DOMAIN)?></span>
                    <?php _e('Select a category for your task', ET_DOMAIN)?>
                </div>
           </div>
           <div class="col-sm-6 col-xs-12 hidden-xs">
                <div class="profs-cat_desc">
                    <span><?php echo get_theme_mod("title_ncat");?></span>
                    <div class="profs-cat_desc_text"><?php echo get_theme_mod("title_prcat");?></div>
                </div>
           </div>   
        <?php
            $taxonomy = 'project_category';
        if(userRole($user_ID) == FREELANCER) {
            $taxslug = get_option('siteurl') . '/project_category';
        } else {
            $taxslug = get_option('siteurl') . '/profile_category';
        }
            $terms = get_terms(array('taxonomy'=>$taxonomy, 'posts_per_page' => 11, 'hide_empty'  => 0, 'parent' => 0));
                if ( $terms && !is_wp_error( $terms ) ) :?>  
                    <div class="row">
                        <?php foreach ( $terms as $term ) {
                                 $termid = $term->term_id;?>
                                <div class="col-sm-6 col-md-4 col-lg-4 col-xs-12">
                                   <div class="profs-cat-list_t faa-parent animated-hover">
                                      <a href="<?php echo $taxslug . '/' . $term->slug; ?>" style="background:#fff url(<?php the_field('catic',$taxonomy . '_' . $termid);?>) 40px center no-repeat;">
                                       <?php echo $term->name; ?></a>
                                      <i class="fa fa-angle-right faa-passing"></i>    	
                                   </div>
                                </div>
                        <?php } ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 col-xs-12">
                            <a class="all_cat" href="<?php echo $taxslug;?>"><?php echo _('See all categories');?></a>
                        </div>
                    </div>
        <?php endif;?>
        </div>
    </div>
</div>

<div class="perfect-freelancer">
    <div class="container">      
        <div class="perfect-freelancer_t">
             <span><?php echo get_theme_mod("title_profbl");?></span>
             <?php echo get_theme_mod("subtitle_profbl");?>
        </div>
        <?php get_template_part( 'home-list', 'profiles' );?>
        <a class="main_bl-btn" href="<?php echo bloginfo('url');?>/register/?role=client"><?php _e('Hire a Pro', ET_DOMAIN);?></a>
    </div>
</div>    

<div class="fre-jobs">
    <div class="container">      
        <div class="fre-jobs_t">
             <span><?php echo get_theme_mod("title_project");?></span>
             <?php echo get_theme_mod("subtitle_project");?>
        </div>
        <?php get_template_part( 'home-list', 'projects' );?>
        <a class="main_bl-btn" href="<?php echo bloginfo('url');?>/register/?role=professional"><?php _e('Sign up as a Pro', ET_DOMAIN)?></a>
    </div>
</div>
  
<div class="fre-stories">
    <div class="container">
        <div class="fre-stories_t">
            <span><?php echo get_theme_mod("title_story");?></span>
            <?php echo get_theme_mod("subtitle_story");?>
        </div>
        <?php get_template_part( 'home-list', 'testimonial' );?>
    </div>
</div>
    
<div class="fre-blog">
    <div class="container">
        <div class="fre-blog_t">
            <?php $cathref = get_term(1);
                    if ($cathref) {?>
                       <span><a href="<?php echo $cathref->slug;?>">Know-How</a></span>
                   <?php  } else { ?>
                       <span>Know-How</span>
                   <?php }?>
                 Wisdom of Masterhand PRO. Lifehacks, Secrets of Success and much more….
        </div>
        <?php get_template_part( 'home-list', 'blog' );?>
    </div>
</div>
</div>
<?php get_footer(); ?>