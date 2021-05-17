<div class="modal fade" id="modal_arbitrate">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Resolve Dispute", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="arbitrate_form" class="fre-modal-form fre-arbitrate-form">
					<p><?php _e('You are about to resolve this dispute. You can send your comment and transfer money to the winner.', ET_DOMAIN);?></p>

                    <div class="fre-input-field">
                        <p style="margin-bottom: 10px"><?php _e('Choose type of split payout', ET_DOMAIN); ?></p>

                        <label class="radio-inline" for="arbitrate-split-percent">
                            <input id="arbitrate-split-percent" type="radio" name="split_type" value="percent" checked>
                            <span></span>
                            <?php _e('Percent', ET_DOMAIN);?>
                        </label>

                        <label class="radio-inline" for="arbitrate-split-number">
                            <input id="arbitrate-split-number" type="radio" name="split_type" value="number">
                            <span></span>
                            <?php _e('Number', ET_DOMAIN);?>
                        </label>
                    </div>

                    <p>
                        <strong><?php _e('Winning Bid:', ET_DOMAIN);?></strong> <span id="modal_arbitrate_winning_bid"></span>$
                    </p>

                    <br>

                    <div class="fre-input-field">
                        <p style="margin-bottom: 10px"><?php _e('Choose who can comment the dispute', ET_DOMAIN); ?></p>

                        <label class="radio-inline" for="arbitrate-comment-both">
                            <input id="arbitrate-comment-both" type="radio" name="split_comment" value="both">
                            <span></span>
                            <?php _e('Both', ET_DOMAIN);?>
                        </label>

                        <label class="radio-inline" for="arbitrate-comment-employer">
                            <input id="arbitrate-comment-employer" type="radio" name="split_comment" value="employer">
                            <span></span>
                            <?php _e('Employer', ET_DOMAIN);?>
                        </label>

                        <label class="radio-inline" for="arbitrate-comment-freelancer">
                            <input id="arbitrate-comment-freelancer" type="radio" name="split_comment" value="freelancer">
                            <span></span>
                            <?php _e('Professional', ET_DOMAIN);?>
                        </label>

                        <label class="radio-inline" for="arbitrate-comment-nobody">
                            <input id="arbitrate-comment-nobody" type="radio" name="split_comment" value="nobody" checked>
                            <span></span>
                            <?php _e('Nobody', ET_DOMAIN);?>
                        </label>
                    </div>

                    <div class="fre-input-field">
                        <label class="fre-field-title" for="split_value_freelancer"><?php _e('Professional', ET_DOMAIN); ?></label>
                        <input id="split_value_freelancer" type="number" min="0" name="split_value_freelancer" data-user-type="freelancer" placeholder="">
                        <span class="split_type_sign">%</span>
                    </div>

                    <div class="fre-input-field">
                        <label class="fre-field-title" for="split_value_client"><?php _e('Employer', ET_DOMAIN); ?></label>
                        <input id="split_value_client" type="number" min="0" name="split_value_client" data-user-type="client" placeholder="">
                        <span class="split_type_sign">%</span>
                    </div>

					<div class="fre-input-field no-margin-bottom">
						<label class="fre-field-title" for=""><?php _e('Your comment here', ET_DOMAIN); ?></label>
						<textarea name="comment_resolved" placeholder=""></textarea>
					</div>

                    <div class="fre-form-btn">
                    	<button type="submit" class="fre-normal-btn btn-submit">
							<?php _e('Arbitrate', ET_DOMAIN) ?>
						</button>

                        <br>

						<span class="fre-form-close" data-dismiss="modal">Cancel</span>
                    </div>

				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
