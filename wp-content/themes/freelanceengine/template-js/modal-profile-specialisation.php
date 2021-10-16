<?
global $user_status;
?>
<!--modal--ad--category-->
<div class="modal fade" id="editcategory" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_t">
					<?php _e( 'My categories', ET_DOMAIN );
					if ( $user_status !== PRO_BASIC_STATUS_FREELANCER ) {
						$col = 5;
					} else {
						$col = 2;
					} ?>
                </div>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="cnt-profile-hide" id="ctn-edit-profile">
                    <div class="fre-employer-info-form" id="accordion"
                         role="tablist"
                         aria-multiselectable="true">
                        <div class="form-detail-profile-page">
                            <p><?php echo sprintf( __( "Number of Specializations - max %s", ET_DOMAIN ), $col ); ?></p>
                            <div class="fre-input-field">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-12">
                                        <select class="" id="list_parentProjectCat" data-max="<?php echo $col ?>">
                                            <option value="0" selected><?php _e( 'Select Category' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12">
                                        <select class="" id="list_subProjectCat" disabled>
                                            <option value="0" selected>Select Subcategory</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12">
                                        <button class="btn fre-submit-btn add-proj-cat">Add</button>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-profile-project-category row" id="list_profProjectCat"></ul>


                            <div class="employer-info-save btn-update-profile btn-update-profile-top">
                                <button class="btn-left fre-submit-btn save-prof-proj-cat"><?php _e( 'Save', ET_DOMAIN ) ?></button>
                                <span class="fre-cancel-btn employer-info-cancel-btn"
                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--edit--form-->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog login -->
</div>

<!--end--modal-->

