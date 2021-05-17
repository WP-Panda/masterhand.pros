<?php
/**
 * Add create milestone into submit project
 * @param void
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_template_for_submit_form' ) ) {
	function ae_milestone_template_for_submit_form() {
		?>
		<li class="form-group custom-field">
			 <div class="fre-input-field">

	            <label class="fre-field-title" for="project-location">
	            	<?php if( MAX_MILESTONE <= 1 ) {
		            	printf( __( "Milestones (You can create %s milestone)", ET_DOMAIN ), MAX_MILESTONE ) ;
		             } else {
		            	printf( __( "Milestones (You can create %s milestones)", ET_DOMAIN ), MAX_MILESTONE ) ;
		            } ?>
	            </label>
	            <div id="add-milestone-form" >
		       		<select name="milestones" multiple class="tax-item milestone-select" id="milestones" style="display: none;"></select>
		       		<select name="milestones_id" multiple class="tax-item milestone-select" id="milestones_id" style="display: none;"></select>
					<ul data-rel="sortable" class="submit-project-list-milestone form-list-milestone"></ul>
					<div class="html_input_mile">
		        		<input type="text" name="milestone-input" id="milestone-input" class="input-item text-field" placeholder="<?php _e('Add milestone', ET_DOMAIN) ?>" autocomplete="off"> <a href="javascript:void(0)" class="btn-insert-milestone-item"><i class="fa fa-plus"></i></a>
		        	</div>
		    	</div>
	        </div>
		</li>
		<?php
	}
}

/**
 * Add create milestone into edit project
 * @param void
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_template_for_edit_form' ) ) {
	function ae_milestone_template_for_edit_form() {
		?>
		<div class="form-group project_category">
	         <label for="milestone-input" class="control-label title-plan">
				<?php echo __( "Milestones", ET_DOMAIN ); ?><br>
            </label>

	       	<div id="add-milestone-form" class="text-field">
	       		<select name="milestones" multiple class="tax-item milestone-select" id="milestones" style="display: none;"></select>
	       		<select name="milestones_id" multiple class="tax-item milestone-select" id="milestones_id" style="display: none;"></select>
				<ul data-rel="sortable" class="edit-project-list-milestone form-list-milestone"></ul>
				<div class="milestone-loading" style="text-align: center">
					<img src="<?php echo MILESTONE_DIR_URL . '/assets/images/loading.gif' ?>" alt="Milestone loading" />
				</div>
				<div class="html_input_mile">
	        	<input type="text"  style="display: none;" name="milestone-input" id="milestone-input" class="input-item form-control text-field" placeholder="<?php _e('Add milestone 2',ET_DOMAIN);?>" autocomplete="off">
	        </div>
	    	</div>
	    </div>
		<?php
	}
}

/**
 * Milestone template in workspace
 * @param object $project 		Convert of project post data
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_template_for_single_project' ) ) {
	function ae_milestone_template_for_single_project( $project ) {
		// Get project owner and bid author;
			$author_id = get_post_field( 'post_author', $project->ID );
		$bid_accepted = get_post_meta( $project->ID, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_accepted );
		$args = array(
			'post_type'      => 'ae_milestone',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_parent'    => $project->ID,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_key'		 => 'position_order'
		);

		$query = new WP_Query( $args );
		ae_query_milestone( $project, '', 'milestone', 'item', $query);
	}
}

/**
 * Render milestone template for workspace
 * @param object $project 		Convert of project post data
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_template_for_workspace' ) ) {
	function ae_milestone_template_for_workspace( $project ) {
		ae_render_milestone_for_workspace( $project, 'milestone-workspace' );
	}
}

/**
 * Milestone template in workspace mobile
 * @param object $project 		Convert of project post data
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_template_for_workspace_mobile' ) ) {
	function ae_milestone_template_for_workspace_mobile( $project ) {
		ae_render_milestone_for_workspace( $project, 'mobile/milestone-workspace' );
	}
}

/**
 * Render milestone template for workspace with specify template part
 * @param object $project 			Convert of project post data
 * @param string $template_part 	Template part for milestone item
 * @return void
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_render_milestone_for_workspace' ) ) {
	function ae_render_milestone_for_workspace( $project, $template_part ) {
			global $ae_post_factory, $current_user;
			$post_object = $ae_post_factory->get( 'ae_milestone' );

			// Get project owner and bid author;
			$author_id = get_post_field( 'post_author', $project->ID );
			$bid_accepted = get_post_meta( $project->ID, 'accepted', true );
			$bid_author = get_post_field( 'post_author', $bid_accepted );
			$args = array(
				'post_type'      => 'ae_milestone',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'post_parent'    => $project->ID,
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'meta_key'		 => 'position_order'
			);

			$query = new WP_Query( $args );
			if($query->have_posts()){
		?>
		<?php
			// Render milestones
			if( $project->post_status != 'complete' && $project->post_status != 'disputing' ) {
				$milestones = ae_query_milestone( $project, $post_object, $template_part, 'item', $query);
			} else {
				$milestones = ae_query_milestone( $project, $post_object, 'milestone-status', 'item', $query );
			}

			wp_reset_postdata();
		?>
		<!-- Milestones data for js -->
		<script type="application/json" id="milestones_data"><?php echo json_encode( $milestones ); ?></script>

		<!-- Template js -->
		<script type="text/template" id="milestone_template_project">
			<div class="item-list-milestone-wrapper {{= class_label }}">
				<span class="icon-status-milestone">
					<# if( post_status == 'open' || post_status == 'reopen' ) { #>
						<i class="fa fa-circle-o"></i>
					<# } else if( post_status == 'resolve' ) { #>
						<i class="fa fa-adjust"></i>
					<# } else { #>
						<i class="fa fa-circle"></i>
					<# } #>
				</span>
				<div class="content-steps-milestone">
					<span>{{= post_title }}</span>

					<div class="txt-status-milestone">
						<a class="change-status-milestone" href="javascript:void(0);"><span>{{=status_label}}</span>
							<?php if( $current_user->ID == $author_id ) { ?>
								<# if( post_status != 'done' ) { #>
									<i class="fa fa-angle-down"></i>
								<# } #>
							<?php } elseif( $current_user->ID == $bid_author ) { ?>
								<# if( post_status != 'resolve' && post_status != 'done' ) { #>
									<i class="fa fa-angle-down"></i>
								<# } #>
							<?php } ?>
						</a>

						<?php if( $current_user->ID == $author_id ) { ?>
							<# if( post_status != 'done' ) { #>
								<ul class="cat-action-milestone">
									<!-- show full action for project owner -->
									<# if( post_status == 'resolve' ) { #>
										<li><a data-icon="fa fa-circle-o" class="reopen-milestone" data-status="opened" href=""><i class="fa fa-circle-o color-text"></i>mark <span class="color-text"><?php _e( "Re-opened", ET_DOMAIN ); ?></span></a></li>
										<li><a data-icon="fa fa-circle" class="close-milestone" data-status="closed" href=""><i class="fa fa-circle color-text"></i>mark <span class="color-text"><?php _e( "Closed", ET_DOMAIN ); ?></span></a></li>
									<# } else if( post_status == 'open' || post_status == 'reopen' ) { #>
										<li><a data-icon="fa fa-circle" class="close-milestone" data-status="closed" href=""><i class="fa fa-circle color-text"></i>mark <span class="color-text"><?php _e( "Closed", ET_DOMAIN ); ?></span></a></li>
									<# } #>
									<!-- show limit action for bid author -->
								</ul>
							<# } #>
						<?php } elseif( $current_user->ID == $bid_author ) { ?>
							<# if( post_status != 'resolve' && post_status != 'done' ) { #>
								<ul class="cat-action-milestone">
									<li><a data-icon="fa fa-adjust" class="resolve-milestone" data-status="resolved" href=""><i class="fa fa-adjust color-text"></i>	mark <span class="color-text"><?php _e( "Resolved", ET_DOMAIN ); ?></span></a></li>
								</ul>
							<# } #>
						<?php } ?>
					</div>
				</div>
			</div><!-- /.item-list-milestone-wrapper -->
		</script>
		<?php
		}
	}
}

/**
 * Milestone notification template
 * @param string $type		 	Milestone action (reopen, resolve, close, remove)
 * @param int $milestone 		Milestone ID
 * @param int $project 			Project ID
 * @param string  $content 		Notification template
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_notification_template' ) ) {
	function ae_milestone_notification_template( $type, $milestone, $project, $content, $notify ) {
		$project_author = get_post_field( 'post_author', $project );
		$bid_accepted = get_post_meta( $project, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_accepted );
		$user_profile_id = get_user_meta($bid_author, 'user_profile_id', true);
		// Get milestone title
		$milestone_title = "";
		if( isset( $milestone ) && !empty( $milestone ) ) {
			$milestone_title = get_the_title( $milestone );
		}

		// Get project link
		$project_link = '<a class="project-link" href="'. get_permalink( $project ) .'?workspace=1">'. get_the_title( $project ) .'</a>';

		switch ( $type ) {
			case 'reopen_milestone':
                // Text: [Employer] Re-opened a milestone on [project_workspace] workspace
                $message = sprintf(__('%s re-opened a milestone on %s workspace', ET_DOMAIN),
                                '<strong>'.get_the_author_meta('display_name', $project_author).'</strong>',
                                '<strong>'.get_the_title($project).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.get_permalink($project).'?workspace=1">
	                            <span class="notify-avatar">'. get_avatar($project_author, 48) .'</span>
	                            <span class="notify-info">'.$message.'</span>
	                            <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
				break;

			case 'resolve_milestone':
				// Text: [Employer] Resolved a [milestone_title] milestone on [project_workspace] workspace
                $message = sprintf(__('%s resolved a %s milestone on %s workspace', ET_DOMAIN),
                                '<strong>'.get_the_author_meta('display_name', $bid_author).'</strong>',
                                '<strong>'.$milestone.'</strong>',
                                '<strong>'.get_the_title($project).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.get_permalink($project).'?workspace=1">
		                        <span class="notify-avatar">'. get_avatar($bid_author, 48) .'</span>
		                        <span class="notify-info">'.$message.'</span>
		                        <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
				break;

			case 'close_milestone':
				// Text: [Employer] closed a [milestone_title] milestone on [project_workspace] workspace
                $message = sprintf(__('%s closed a milestone on %s workspace', ET_DOMAIN),
                                '<strong>'.get_the_author_meta('display_name', $project_author).'</strong>',
                                '<strong>'.get_the_title($project).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.get_permalink($project).'?workspace=1">
	                            <span class="notify-avatar">'. get_avatar($project_author, 48) .'</span>
	                            <span class="notify-info">'.$message.'</span>
	                            <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
				break;

			case 'create_milestone':
				// Text: [Employer] has created milestone [milestone_title] on [project_workspace] workspace
                $message = sprintf(__('%s has created milestone %s on %s workspace', ET_DOMAIN),
                                '<strong>'.get_the_author_meta('display_name', $project_author).'</strong>',
                                '<strong>'.$milestone_title.'</strong>',
                                '<strong>'.get_the_title($project).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.get_permalink($project).'?workspace=1">
		                        <span class="notify-avatar">'. get_avatar($project_author, 48) .'</span>
		                        <span class="notify-info">'.$message.'</span>
		                        <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
				break;
		}

		return $content;
	}
}