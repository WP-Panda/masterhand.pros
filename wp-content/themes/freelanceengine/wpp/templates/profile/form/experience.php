<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

if ( fre_share_role() || wpp_fre_is_freelancer() ) { ?>
    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-experience-field">
        <label class="fre-field-title"><?php _e( 'Years experience', ET_DOMAIN ); ?></label>
        <input type="number" value="<?php echo $experience; ?>"
               name="et_experience" id="et_experience" min="0"
               placeholder="<?php _e( 'Total', ET_DOMAIN ) ?>">
    </div>
<?php }