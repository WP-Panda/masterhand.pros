
<div class="modal fade" id="modal_view_portfolio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="row">
                <div class="col-lg-5 col-lg-push-7">
                    <div class="modal-portfolio-info">
                        <span class="close" data-dismiss="modal"></span>
                        <h2 class="post_title"></h2>
                        <p class="post_content"></p>
                        <div class="fre-label-container list_profile_category">
                            <?php /*if ( ! empty( $current->tax_input['skill'] ) ) {
								foreach ( $current->tax_input['skill'] as $skill ) {
									if ( ! empty( $skill->name ) ) {
										echo '<span class="fre-label">' . $skill->name . '</span>';
									}
								}
							} */?>
                        </div>
                        <div class="fre-label-container list_skill">
							<?php /*if ( ! empty( $current->tax_input['skill'] ) ) {
								foreach ( $current->tax_input['skill'] as $skill ) {
									if ( ! empty( $skill->name ) ) {
										echo '<span class="fre-label">' . $skill->name . '</span>';
									}
								}
							} */?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-lg-pull-5">
                    <div class="modal-portfolio-img list_image">
						<?php /*if ( ! empty( $current->list_image_portfolio ) ) {
							foreach ( $current->list_image_portfolio as $p ) { */?><!--
                                <img src="<?php /*echo $p['image'] */?>">
							--><?php /*}
						} */?>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>