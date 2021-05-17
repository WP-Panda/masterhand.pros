<?php
global $user_ID;

$avatar_id = get_user_meta($user_ID,'et_avatar',true);
$avatar_url = get_user_meta($user_ID,'et_avatar_url',true);

$avatar_data = wp_get_attachment_image_url($avatar_id,'full');
?>

    <div class="modal fade" id="uploadAvatar" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal"></button>
                 <?php _e("Upload profile picture", ET_DOMAIN) ?>
            </div>
            <div class="modal-content">
                <div class="modal-body">
                    <form class="form-save-avatar" data-processing='no'>
                        <div class="text-center">

                            <div class="preview-image" id="container_crop_avatar" data-is_crop="false">
                                <?php if(!empty($avatar_data)){ ?>
                                <img src="<?php echo $avatar_data ?>" class="avatar photo avatar-default">
                                <?php } else { ?>
                                <?php echo get_avatar( $user_ID, 150 ) ?>
                                <?php } ?>
                            </div>

                            <div id="md_user_avatar_container" data-avatar_id="<?php echo $avatar_id ?>">
                                <button type="submit" class="fre-submit-btn btn-left fre-submit-portfolio"><?php _e('Save profile picture', ET_DOMAIN) ?></button>

                                <a class="fre-form-close fre-cancel-btn btn-right" href="#" id="md_user_avatar_browse_button" style="margin-left: 0">
                                    <?php _e( 'Change picture', ET_DOMAIN ) ?>
                                </a>
                                <span class="et_ajaxnonce hidden" id="<?php echo de_create_nonce( 'md_user_avatar_et_uploader' ); ?>"></span>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
