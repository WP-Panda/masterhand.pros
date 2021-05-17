<?php
/**
 * Get all milestone by post parent
 * @param void
 * @return object $milestone 	Milestone post data
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_get_milestone' ) ) {
	function ae_get_milestones( $post_parent = '' ) {
		$milestones = get_posts( array(
			'post_type'      => 'ae_milestone',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_parent'    => $post_parent,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_key'		 => 'position_order'
		) );

		return $milestones;
	}
}

/**
 * Query loop for milestone
 * Using WP_Query class
 * @param object $project 		Convert of project post data
 * @param object $post_object
 * @param string $slug 			Template slug
 * @param string $name 			Template Name
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_query_milestone' )) {
	function ae_query_milestone( $project, $post_object = '', $slug, $name, $query) {
		$milestones = array();
		// $args = array(
		// 	'post_type'      => 'ae_milestone',
		// 	'posts_per_page' => -1,
		// 	'post_status'    => 'any',
		// 	'post_parent'    => $project->ID,
		// 	'orderby'        => 'meta_value',
		// 	'order'          => 'ASC',
		// 	'meta_key'		 => 'position_order'
		// );

		// $query = new WP_Query( $args );
		if($query->have_posts()) {
			echo '<ul class="cat-list-milestone">';
			while( $query->have_posts() ):
				$query->the_post();
				global $post;

				// Convert milestone
				if( !empty( $post_object ) ) {
					$milestones[] = $post_object->convert( $post );
				}

				// Get milestone item template
				ae_get_milestone_template_part( $slug, $name );
			endwhile;
			wp_reset_postdata();
			echo '</ul>';
		}

		return $milestones;
	}
}

/**
 * Get template part for plugin
 * eg. milestone-item.php
 * @param string $slug 		Template slug `milestone`
 * @param string $name  	Template name `item`
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_get_milestone_template_part') ) {
	function ae_get_milestone_template_part( $slug, $name ) {
		$template = dirname( __FILE__ ). "/template/{$slug}-{$name}.php";

        if ($template) {
            load_template($template, false);
        }
	}
}

/**
 * Convert milestone meta for milestone item in list: class name, status, icon class name
 * @param object $milestone 		Convert of milestone post data
 * @return array $milestone_meta 	Array of milestone meta fields
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_get_milestone_meta' ) ) {
	function ae_get_milestone_meta( $milestone ) {
		$milestone_meta = array();
		switch ( $milestone->post_status ) {
			case 'open':
				$milestone_meta = array(
					'icon_class' => 'fa fa-circle-o',
				);
				break;

			case 'reopen':
				$milestone_meta = array(
					'icon_class' => 'fa fa-circle-o',
				);
				break;

			case 'resolve':
				$milestone_meta = array(
					'icon_class' => 'fa fa-adjust',
				);
				break;

			case 'done':
				$milestone_meta = array(
					'icon_class' => 'fa fa-circle',
				);
				break;
		}

		return $milestone_meta;
	}
}

/**
 * Update new changelog meta for project
 * @param object $project
 * @param int $val
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_update_project_new_changelog_meta' ) ) {
	function ae_update_project_new_changelog_meta( $project, $val = 1 ) {
		global $user_ID;

		if( $user_ID == $project->post_author ){
	        update_post_meta( $project->ID, 'fre_freelancer_new_changelog', $val);
	    } else{
	        update_post_meta( $project->ID, 'fre_employer_new_changelog', $val);
	    }
	}
}

/**
 * Reset new changelog meta
 * @param object $project
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_reset_project_new_changelog_meta' ) ) {
	function ae_reset_project_new_changelog_meta( $project ) {
		global $user_ID;
		if( $user_ID == $project->post_author ){
	        update_post_meta( $project->ID, 'fre_employer_new_changelog', 0 );
	    } else{
	        update_post_meta( $project->ID, 'fre_freelancer_new_changelog', 0 );
	    }
	}
}

/**
 * Get new changelog meta
 * @param object $project
 * @return int $val
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_get_project_new_changelog_meta' ) ) {
	function ae_get_project_new_changelog_meta( $project ) {
		global $user_ID;
		if( $user_ID == $project->post_author ){
	        $val = get_post_meta( $project->ID, 'fre_employer_new_changelog', true );
	    } else{
	        $val = get_post_meta( $project->ID, 'fre_freelancer_new_changelog', true );
	    }

	    return !empty( $val ) ? (int)$val : 0;
	}
}