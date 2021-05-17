<style>
    #modal_send_banner_to_emails{
        font-family: 'GothamPro',sans-serif; text-align: left;
    }
    #modal_send_banner_to_emails .input-group{
        display: block;
    }
    #add_new_email{
        width: 80%; float: left;
    }
    #modal_send_banner_to_emails .input-group-btn{
        width: 20%; float: left;
    }
    #modal_send_banner_to_emails .add-new-email{
        height: 60px; width: 200px;
    }
    #modal_send_banner_to_emails .submit-send-email{
        width: 200px;
    }
    .form-send-banner-to-emails .select2-container {
        width: 100% !important;
    }
    .form-send-banner-to-emails .select2-container--default .select2-selection--multiple {
        padding: 10px;
        display: block;
        line-height: 1 !important;
        width: 100%;
        border: 1px solid #2c33c1 !important;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        font-size: 16px;
        font-weight: 400;
        color: #878787;
        height: auto;
        o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .form-send-banner-to-emails li {
        margin-bottom: 10px;
        display: inline-block;
    }
    .form-send-banner-to-emails li span {
        font-size: 18px;
    }
</style>
<div class="modal fade" id="modal_send_banner_to_emails">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php _e( "Send banner", ET_DOMAIN ) ?>
                <button type="button" class="close" data-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="fre-input-field">
					<div class="input-group">
						<label class="fre-field-title"><? _e('Add emails', ET_DOMAIN);?></label>
						<input type="text" id="add_new_email" value="" title="<? _e('Write email and press button Enter', ET_DOMAIN);?>"
						   placeholder="<? _e('Write email and press button Enter', ET_DOMAIN);?>" maxlength="30">
						<span class="input-group-btn">
							<button class="fre-submit-btn btn btn-default add-new-email" type="button"><? _e('Add', ET_DOMAIN);?></button>
					  	</span>
					</div>
				</div>
				<form method="POST" class="form-send-banner-to-emails">
					<select id="select_emails" name="emails" multiple></select>
					<div class="fre-form-btn show-skill-btn-group">
						<button class="fre-submit-btn btn-left submit-send-email"><? _e('Send', ET_DOMAIN);?></button>
						<div class="fre-cancel-btn" data-dismiss="modal"><? _e( 'Close', ET_DOMAIN ); ?></div>
					</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->