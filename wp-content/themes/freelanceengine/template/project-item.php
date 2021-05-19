<?php

	global $wp_query, $ae_post_factory, $post;
	$post_object = $ae_post_factory->get( PROJECT );
	$current     = $post_object->current_post;

	if ( function_exists( 'optionsProject' ) ) {
		$optionsProject = optionsProject( $current );
	} else {
		$optionsProject = null;
	}
?>

<li class="project-item" <?php echo $optionsProject[ 'highlight_project' ] ?>>
    <div class="project-content fre-freelancer-wrap">
        <a class="project-name" href="<?php the_permalink(); ?>"
           title="<?php the_title(); ?>"><?php the_title(); ?>
			<?php if ( ( $optionsProject[ 'create_project_for_all' ] ) || ( $optionsProject[ 'priority_in_list_project' ] ) || ( $optionsProject[ 'urgent_project' ] ) || ( $optionsProject[ 'hidden_project' ] ) ) { ?>
                <i><?php //new2
						echo $optionsProject[ 'create_project_for_all' ];
						echo $optionsProject[ 'priority_in_list_project' ];
						echo $optionsProject[ 'urgent_project' ];
						echo $optionsProject[ 'hidden_project' ];
						//new2 ?></i>
			<?php } ?>
        </a>

        <div class="project-list-info">
            <span class="project-posted"><?php printf( __( 'Posted %s', ET_DOMAIN ), get_the_date() ); ?></span>
            <span class="project-bid"><?php echo $current->text_total_bid; ?></span>
            <span class="fre-location"><?php echo $current->str_location; ?></span>
            <span class="free-hourly-rate"><?php echo $current->budget; ?></span>
        </div>
        <div class="project-list-desc">
            <p><?php echo $current->post_content_trim; ?></p>
        </div>
        <div class="project-list-skill"><?php echo $current->project_categories; ?></div>
    </div>
</li>