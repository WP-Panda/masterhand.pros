<div class="modal fade" id="modal_edit_skills">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php _e( "Edit skills", ET_DOMAIN ) ?>
                <button type="button" class="close" data-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="fre-input-field">
					<div class="input-group">
						<label class="fre-field-title"><? _e('Add new skill', ET_DOMAIN);?></label>
						<input type="text" id="add_new_skill" value="" title="<? _e('Write Your new skill and press button Enter', ET_DOMAIN);?>"
						   placeholder="<? _e('Write Your new skill and press button Enter', ET_DOMAIN);?>" maxlength="30">
						<span class="input-group-btn">
							<button class="fre-submit-btn btn btn-default add-new-skill" type="button"><? _e('Add', ET_DOMAIN);?></button>
					  	</span>
					</div>
				</div>
				<form method="POST" class="form-edit-skills">
					<select id="user_select_skill" name="skills" multiple></select>
					<div class="fre-form-btn show-skill-btn-group">
						<button class="fre-submit-btn btn-left submit-save-skill"><? _e('Save', ET_DOMAIN);?></button>
						<div class="fre-cancel-btn" data-dismiss="modal"><? _e( 'Close', ET_DOMAIN ); ?></div>
					</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->