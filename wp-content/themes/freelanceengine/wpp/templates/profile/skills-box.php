<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="fre-profile-box skills_awards_wp">
    <div class="row skills_awards">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 skill-list">
            <!-- пока нет наград - ставим 12 вместо col-lg-6 col-md-6 col-sm-6-->
            <a href="#modal_edit_skills" data-toggle="modal"
               class="unsubmit-btn btn-right wpp-open-edit-skills">
				<?php _e( 'Add skills', WPP_TEXT_DOMAIN ); ?>
            </a>
            <div class="bl_t">
				<?php echo __( 'Skills and Endorsements:', WPP_TEXT_DOMAIN );
				if ( ! $is_company ) { ?>
                    <div class="skill-list__placeholder"> <?php echo __( 'You can put here your personal skills, related keywords . For example Polite, Demanding, Recognize Brilliance, Pay Promptly etc.', WPP_TEXT_DOMAIN ); ?>
                    </div>
				<?php } else { ?>
                    <div class="skill-list__placeholder">
						<?php echo __( 'You can put here your professional and personal skills, related keywords . For example Drilling, General Woodworking, Cleaning Sewer Lines, Problem-Solving etc.', WPP_TEXT_DOMAIN );
						?>
                    </div>
				<?php } ?>
            </div>

            <ul id="list_skills_user">
				<?php
		
				$endorse_class = wpp_is_endorse_allow( $user_ID ) ? ' mode-endorse' : '';

				$skills = WPP_Skills_User::getInstance()->get_user_skill_list();

				if ( ! empty( $skills ) ) :
					foreach ( $skills as $skill ) {

						$endorsed_data = wpp_is_endorse_allow( $user_ID ) ? sprintf( ' data-uid="%s" data-skill="%s"', $user_ID, $skill['id'] ) : '';
						$endorsed      = wpp_is_endorsed( $user_ID, $skill['id'] ) ? ' endorsed' : '';


						printf( '<li class="item-list-skills"><span class="item-endorse-skill%s%s"%s>%s</span><span class="endorse-skill" title="%s">%s</span></li>', $endorse_class, $endorsed, $endorsed_data, $skill['title'], __( 'counts of endorsement', WPP_TEXT_DOMAIN ), $skill['count'] );
					}
				endif;
				?>
            </ul>
			<?php wpp_get_template_part( 'template-js/wpp/modal-edit-skills' ); ?>
        </div>

		<?php //wpp_get_template_part( 'wpp/templates/profile/awards', [] ); ?>
    </div>
	<?php //wpp_get_template_part( 'wpp/templates/universal/hide-more', [] ); ?>
</div>
