<div class="modal fade" id="modal_edit_skills">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<?php _e( "Edit skills", WPP_TEXT_DOMAIN ) ?>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="fre-input-field">
                    <div class="input-group">
                        <label class="fre-field-title"><?php _e( 'Add new skill', WPP_TEXT_DOMAIN ); ?></label>
                        <input type="text" id="add_new_skill" value=""
                               title="<?php _e( 'Write Your new skill and press button Enter', WPP_TEXT_DOMAIN ); ?>"
                               placeholder="<?php _e( 'Write Your new skill and press button Enter', WPP_TEXT_DOMAIN ); ?>"
                               maxlength="30">
                        <span class="input-group-btn">
							<button class="fre-submit-btn btn btn-default wpp-add-new-skill"
                                    type="button"><?php _e( 'Add', WPP_TEXT_DOMAIN ); ?></button>
					  	</span>
                    </div>
                </div>
                <form method="POST" class="wpp-form-edit-skills">
                    <select id="user_select_skill" name="skills" multiple></select>
                    <div class="fre-form-btn show-skill-btn-group">
                        <button class="fre-submit-btn btn-left wpp-submit-save-skill"><?php _e( 'Save', WPP_TEXT_DOMAIN ); ?></button>
                        <div class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Close', WPP_TEXT_DOMAIN ); ?></div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->