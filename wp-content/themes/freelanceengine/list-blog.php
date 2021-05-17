<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('post');
$postdata    = array();
	if(have_posts()){
?>
<!-- blog list -->

	<?php
		while(have_posts()) { the_post();
			$convert    = $post_object->convert($post);
			$postdata[] = $convert;
			get_template_part('template/post', 'blog');
		}
		/**
		 * render post data for js
		*/
		echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
	?>

<!--// blog list  -->
<!-- pagination -->
<?php
			echo '<div class="paginations-wrapper">';
			ae_pagination($wp_query, get_query_var('paged'));
			echo '</div>';
	} else {
		_e( 'No posts yet!', ET_DOMAIN );
	}
?>
<!--// pagination -->