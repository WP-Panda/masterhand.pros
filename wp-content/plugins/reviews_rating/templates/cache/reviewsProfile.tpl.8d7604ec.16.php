<?php
	/** Fenom template 'reviewsProfile.tpl' compiled at 2020-09-27 05:37:34 */
	return new Fenom\Render( $fenom, function( $var, $tpl ) {
		?><?php if ( ! empty( $var[ "list_reviews" ] ) && ( is_array( $var[ "list_reviews" ] ) || $var[ "list_reviews" ] instanceof \Traversable ) ) {
			foreach ( $var[ "list_reviews" ] as $var[ "rwProject" ] ) { ?>

				<?php
				if ( $var[ "rwProject" ][ 'its_reply' ] != true ) { ?>
                    <li>
                        <div class="fre-author-project-box row" data-id="<?php

							echo $var[ "rwProject" ][ 'user_id' ]; ?>">
                            <div class="col-sm-1 col-xs-3 avatar_wp"><?php
									echo call_user_func_array( 'get_avatar', [ $var[ "rwProject" ][ 'user_id' ] ] ); ?></div>
                            <div class="col-sm-11 col-xs-9">
                                <div class="col-sm-9 col-md-10 col-lg-10 col-xs-12 fre-author-project">
                    <span class="fre-author-project-box_t"><?php

		                    echo $var[ "rwProject" ][ 'author_project' ]; ?></span>
									<?php
										$var[ "user_status" ] = call_user_func_array( 'get_user_pro_status', [ $var[ "rwProject" ][ 'user_id' ] ] ); ?>

									<?php
										if ( $var[ "user_status" ] && $var[ "user_status" ] != @constant( 'PRO_BASIC_STATUS_EMPLOYER' ) && $var[ "user_status" ] != @constant( 'PRO_BASIC_STATUS_FREELANCER' ) ) { ?>
                                            <span class="status"><?php

													echo call_user_func_array( 'translate', [
														'PRO',
														@constant( 'ET_DOMAIN' )
													] ); ?></span>
											<?php

										} ?>
                                    <span class="hidden-xs rating-new">+<?php

											echo call_user_func_array( 'getActivityRatingUser', [ $var[ "rwProject" ][ 'user_id' ] ] ); ?></span>
                                </div>
								<?php

									if ( $var[ "rwProject" ][ 'vote' ] > 3 ) { ?>
                                        <div class="free-rating">
                                            <div class="reviews-rating-summary" title="">
                                                <div class="review-rating-result" style="width: <?php
													/* reviewsProfile.tpl:21: {$rwProject['vote']*20} */
													echo $var[ "rwProject" ][ 'vote' ] * 20; ?>%"></div>
                                            </div>
                                        </div>
										<?php

									} ?>
                                <span class="visible-xs col-xs-6 rating-new">+<?php

										echo call_user_func_array( 'getActivityRatingUser', [ $var[ "rwProject" ][ 'user_id' ] ] ); ?></span>
                                <div class="col-sm-8 hidden-xs fre-project_lnk">
                    <span><?php
		                    echo call_user_func_array( '_e', [ 'Project:', ET_DOMAIN ] ); ?></span>
                                    <a href="<?php
										echo $var[ "rwProject" ][ 'guid' ]; ?>" title="<?php
										echo call_user_func_array( 'esc_attr', [ $var[ "rwProject" ][ 'post_title' ] ] ); ?>">
										<?php
											echo $var[ "rwProject" ][ 'post_title' ]; ?>
                                    </a>
                                </div>

                                <span class="hidden-xs col-sm-4 posted text-right">
                    <?php
	                    echo call_user_func_array( '_e', [
		                    call_user_func_array( 'date', [
			                    'F d, Y',
			                    call_user_func_array( 'strtotime', [ $var[ "rwProject" ][ 'post_date' ] ] )
		                    ] ),
		                    @constant( 'ET_DOMAIN' )
	                    ] ); ?>
                </span>
                            </div>
                            <div class="visible-xs col-xs-12 fre-project_lnk">
                <span><?php
		                echo call_user_func_array( '_e', [ 'Project:', @constant( 'ET_DOMAIN' ) ] ); ?></span>
                                <a href="<?php
									echo $var[ "rwProject" ][ 'guid' ]; ?>" title="<?php
									echo call_user_func_array( 'esc_attr', [ $var[ "rwProject" ][ 'post_title' ] ] ); ?>">
									<?php
										echo $var[ "rwProject" ][ 'post_title' ]; ?>
                                </a>
                            </div>
                            <div class="col-sm-12 col-xs-12 author-project-comment">
                                <div class="col-sm-8 col-md-9 col-lg-9 col-xs-12">
									<?php
										/* reviewsProfile.tpl:45: {$.call.string_is_nl2br($rwProject['comment'])} */
										echo call_user_func_array( 'string_is_nl2br', [ $var[ "rwProject" ][ 'comment' ] ] ); ?>
                                    <div class="review_reply_comment">
										<?php
											/* reviewsProfile.tpl:47: {if $rwProject['reply_name'] != ''} */
											if ( $var[ "rwProject" ][ 'reply_name' ] != '' ) { ?>
                                                <b><?php
														/* reviewsProfile.tpl:48: {$rwProject['reply_name']} */
														echo $var[ "rwProject" ][ 'reply_name' ]; ?>:</b>
												<?php
												echo $var[ "rwProject" ][ 'reply_comment' ]; ?>
												<?php
												/* reviewsProfile.tpl:50: {/if} */
											} ?>
                                    </div>
                                </div>
                                <span class="visible-xs col-xs-12 posted text-right"><?php
										echo call_user_func_array( '_e', [
											$var[ "rwProject" ][ 'post_date' ],
											@constant( 'ET_DOMAIN' )
										] ); ?></span>

                                <div class="col-sm-4 col-md-3 col-lg-3 col-xs-12">
									<?php
										if ( $var[ "rwProject" ][ 'status' ] == 'hidden' && $var[ "user_ID" ] == $var[ "rwProject" ][ 'for_user_id' ] ) { ?>
                                            <span data-review_id="<?php
												/* reviewsProfile.tpl:57: {$rwProject['id']} */
												echo $var[ "rwProject" ][ 'id' ]; ?>"
                                                  class="review-must-paid  btn-right fre-submit-btn">
                            <?php
	                            echo call_user_func_array( '_e', [
		                            'Add to Rating & Reviews',
		                            @constant( 'ET_DOMAIN' )
	                            ] ); ?>
                        </span>
											<?php
										} ?>

									<?php
										if ( $var[ "rwProject" ][ 'status' ] != 'hidden' && $var[ "rwProject" ][ 'reply_to_review' ] != '' && $var[ "user_ID" ] == $var[ "rwProject" ][ 'for_user_id' ] ) { ?>
                                            <a href='#' data-review_id='<?php
												echo $var[ "rwProject" ][ 'reply_to_review' ]; ?>'
                                               data-project-id="<?php
												   echo $var[ "rwProject" ][ 'ID' ]; ?>" id='<?php
												echo $var[ "rwProject" ][ 'reply_to_review' ]; ?>'
                                               class='fre-submit-btn btn-left project-employer__reply project-employer_reply_history main_bl-btn'><?php
													echo call_user_func_array( '_e', [
														'Reply to review',
														@constant( 'ET_DOMAIN' )
													] ); ?></a>
											<?php
										} ?>
                                </div>
                            </div>
                        </div>
                    </li>
					<?php
				} ?>

				<?php
			}
		} ?><?php
	}, [
		'options'   => 128,
		'provider'  => false,
		'name'      => 'reviewsProfile.tpl',
		'base_name' => 'reviewsProfile.tpl',
		'time'      => 1601185052,
		'depends'   => [
			0 => [
				'reviewsProfile.tpl' => 1601185052,
			],
		],
		'macros'    => [],

	] );
