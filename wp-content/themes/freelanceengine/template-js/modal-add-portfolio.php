<?php
	global $user_ID;
	$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
?>
<div class="modal fade" id="modal_add_portfolio">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Add new portfolio", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="create_portfolio" class="fre-modal-form auth-form create_portfolio">
					<div class="fre-input-field">
                		<label class="fre-field-title"><?php _e('Portfolio Title', ET_DOMAIN) ?></label>
                		<input type="text" name="post_title"  />
                	</div>
					<div class="fre-input-field">
						<label class="fre-field-title"><?php _e('Portfolio Description', ET_DOMAIN) ?></label>
						<textarea name="post_content" cols="30" rows="10"></textarea>
					</div>

					<div class="fre-input-field box_upload_img">
                        <div id="portfolio_img_thumbnail" style="display: none"></div>
                        <ul class="portfolio-thumbs-list row image ctn_portfolio_img">

						</ul>

                        <div id="portfolio_img_container" style="height: 50px">
                            <span class="et_ajaxnonce hidden" data-id="<?php echo wp_create_nonce( 'portfolio_img_et_uploader' ); ?>"></span>
                            <!--<label class="fre-upload-file" for="portfolio_img_browse_button">
                			<input type="file" name="post_thumbnail" id="portfolio_img_browse_button" value="<?php /*_e('Browse', ET_DOMAIN); */?>" />
                			<?php /*_e('Upload Files', ET_DOMAIN) */?>
                		    </label>-->
                            <a class="fre-upload-file" href="#" id="portfolio_img_browse_button" style="display: block;">
		                        <?php _e( 'Upload Files', ET_DOMAIN ) ?>
                            </a>
                        </div>
                		<p class="fre-allow-upload"><?php _e('(Maximum upload file size is limited to 10MB, allowed file types in the png, jpg, and gif.)', ET_DOMAIN) ?></p>
					</div>

                    <div class="fre-input-field">
                        <label class="fre-field-title"><?php _e('Categories (optional)', ET_DOMAIN); ?></label>

                        <select  class="fre-chosen-multi" name="profile_category" multiple data-placeholder="<?php _e('Select an option', ET_DOMAIN); ?>">
                            <?php
                            if($profile_id) {
                                $profile_categories = wp_get_object_terms( $profile_id, 'project_category' );
                            } else {
                                $profile_categories = get_terms( 'project_category', array('hide_empty' => false) );
                            }
                            if(!empty($profile_categories)){
                                $value = 'term_id';
                                foreach ($profile_categories as $category) {
                                    echo '<option value="'.$category->$value.'">'.$category->name.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="fre-input-field no-margin-bottom">
                        <label class="checkline" for="best_work">
                            <input id="best_work" name="best_work" type="checkbox" value="1" onclick="selectOnlyThis(1)">
                            <span><?php _e('Best work', ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <div class="fre-input-field no-margin-bottom">
                        <label class="checkline" for="client">
                            <input id="client" name="client" type="checkbox" value="1" onclick="selectOnlyThis(2)">
                            <span><?php _e('Client', ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <script>
                        function selectOnlyThis(num) {
                            if(num == 1) {
                                $('#client')[0].checked = false
                            } else {
                                $('#best_work')[0].checked = false
                            }
                        }
                    </script>

                	<div class="fre-form-btn">
                		<button type="submit" class="fre-submit-btn btn-left fre-normal-btn fre-submit-portfolio">
							<?php _e('Save', ET_DOMAIN) ?>
						</button>
						<span class="fre-cancel-btn fre-form-close" data-dismiss="modal"><?php _e('Cancel', ET_DOMAIN) ?></span>
                	</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->