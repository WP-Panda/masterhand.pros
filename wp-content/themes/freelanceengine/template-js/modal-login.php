<div class="modal fade" id="modal_login">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"></button>
				<?php _e("Welcome back!", ET_DOMAIN) ?>
			</div>
			<div class="modal-body">
				<form role="form" id="signin_form" class="fre-authen-login auth-form signin_form">
					<div class="fre-input-field">
						<label for="login_user_login"><?php _e('Your Username or Email', ET_DOMAIN) ?></label>
						<input type="text" class="need_valid" id="login_user_login" name="user_login" placeholder="<?php _e('Enter Username or Email', ET_DOMAIN) ?>">
					</div>
					<div class="fre-input-field">
						<label for="login_user_pass"><?php _e('Your Password', ET_DOMAIN) ?></label>
						<input type="password" class="need_valid" id="login_user_pass" name="user_pass" placeholder="<?php _e('Enter Password', ET_DOMAIN) ?>">
					</div>
					<div class="fre-form-btn">
					<button type="submit" class="fre-submit-btn btn-left btn-submit btn-sumary btn-sub-create">
						<?php _e('Sign in', ET_DOMAIN) ?>
					</button>
                    <a class="fre-cancel-btn show-forgot-form" href="#"><?php _e("Forgot Password?", ET_DOMAIN) ?></a>
                        <?php
			                if(fre_check_register() && function_exists('ae_render_social_button')){
			                    $before_string = __("You can also sign in by:", ET_DOMAIN);
			                    ae_render_social_button( array(), array(), $before_string );
			                }
			            ?>
                    </div>   
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
