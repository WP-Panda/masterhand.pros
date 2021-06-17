<?php
	//global $user_ID;
	global $wp_query, $ae_post_factory, $post, $wpp_fr;
	$max_doxs = 5;

	$is_edit = false;

	if ( is_author() ) {
		$author_id = get_query_var( 'author' );
		if ( (int) $wpp_fr->user === (int) $author_id ) {
			$is_edit = true;
		}
	} else {
		$author_id = $wpp_fr->user;
	}

	$profile_id    = get_user_meta( $author_id, 'user_profile_id', true );
	$document_list = get_post_meta( $profile_id, 'document_list', true );


	if ( ! empty( $document_list ) ) {
		$documents_id = is_numeric( $document_list ) ? [ $document_list ] : explode( ', ', $document_list );
		$count_docs   = count( $documents_id );
	} else {
		$documents  = [];
		$count_docs = null;
	}

?>

<div class="fre-profile-box documents_page">
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="freelance-portfolio-title"><?php _e( 'Documents & Licenses', ET_DOMAIN ) ?></div>
            <input type="hidden" name="profile_id" value="<?php echo $profile_id ?>"/>
        </div>
		<?php
			if ( $count_docs < $max_doxs ) { ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="freelance-portfolio-add">
                        <a href='#add_file' data-toggle="modal" class='fre-submit-btn btn-right'>
							<?php _e( 'Add new', ET_DOMAIN ) ?>
                        </a>
                        <div class="modal fade" id="add_file" role="dialog" aria-labelledby="myModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal_t">
											<?php _e( 'My Documents & Licenses', ET_DOMAIN ); ?>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal"></button>
                                    </div>
                                    <form role="form" id="form_add_document" class="fre-modal-form">
                                        <div class="modal-body">
                                            <div class="fre-input-field box_upload_img">
                                                <ul id="list_ImgPreviews" class="portfolio-thumbs-list row image">
                                                </ul>
                                                <div class="upfiles-container">
                                                    <div class="fre-upload-file">
                                                       <?php _e('Upload Files', WPP_TEXT_DOMAIN ); ?>
                                                        <input id="upfiles" type="file" multiple="" accept=".jpeg, .gif, .png, .pdf, .xls, .xlsx, .txt, .doc, .docx">
                                                    </div>
                                                </div>
                                                <p class="fre-allow-upload">
													<?php _e( '(Maximum upload file size is limited to 5MB, maximum for 10 items, allowed file types in the png, jpg, gif, pdf, xls, xlsx, doc, txt)' ); ?>
                                                </p>
                                            </div>
                                            <div class="fre-form-btn">
                                                <button type="submit"
                                                        class="fre-submit-btn btn-left fre-normal-btn btn_submit_document">
													<?php _e( 'Save', ET_DOMAIN ) ?>
                                                </button>
                                                <span class="fre-cancel-btn fre-form-close"
                                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                                            </div>
                                            <style>
                                                .upfiles-container {
                                                    position: relative;
                                                    height: 50px;
                                                }

                                                .fre-upload-file {
                                                    position: absolute;
                                                    top: 0px;
                                                    left: 0px;
                                                    width: 100%;
                                                    height: 44px;
                                                    overflow: hidden;
                                                }

                                                #upfiles {
                                                    cursor: pointer;
                                                    display: block;
                                                    font-size: 999px;
                                                    opacity: 0;
                                                    position: absolute;
                                                    top: 0px;
                                                    left: 0px;
                                                    width: 100%;
                                                    height: 100%;
                                                }

                                                .fre-allow-upload {
                                                    text-align: center;
                                                }

                                                .delete-file {
                                                    cursor: pointer;
                                                }
                                            </style>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="item_PreviewTemplate" style="display: none;">
                            <li class="col-sm-3 col-xs-12 item">
                                <div class="portfolio-thumbs-wrap">
                                    <div class="portfolio-thumbs img-wrap">
                                        <div class="portfolio-thumbs_file-name"></div>
                                        <img src="">
                                    </div>
                                    <div class="portfolio-thumbs-action delete-file">
                                        <i class="fa fa-trash-o"></i>Remove
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
				<?php
			} else { ?>
                <div class="col-sm-6 col-xs-12">
                    <div class="freelance-portfolio-add text-right error-message"><?php _e( 'You can add only ' . $count_docs . ' files', ET_DOMAIN ); ?></div>
                </div>
				<?php
			} ?>
    </div>
	<?php if ( empty( $documents_id ) ) { ?>
        <p class="fre-empty-optional-profile"><?php _e( 'Add documents to your profile. (optional)', ET_DOMAIN ) ?></p>
	<?php } else { ?>
        <ul class="freelance-portfolio-list row">
			<?php

				foreach ( $documents_id as $item ) {
					//_dump( $item );
					$doc_data = get_post( $item );
					//wpp_dump( $doc_data );
					$document = [
						'id'    => $item,
						'url'   => wp_get_attachment_url( $item ),
						'mime'  => $doc_data->post_mime_type,
						'title' => $doc_data->post_title
					];

					$is_application_mime = stripos( $document[ 'mime' ], 'application' ) !== false ? true : false;
					if ( $is_application_mime ) {
						switch ( $document[ 'mime' ] ) {
							case 'application/pdf':
								$border = '#cc4b4c';
								$icon   = '/wp-content/uploads/2020/08/pdf.svg';
								break;
							case 'application/msword':
								$border = '#1e96e6';
								$icon   = '/wp-content/uploads/2020/08/doc.svg';
								break;
							case 'application/vnd.ms-excel':
								$border = '#91cda0';
								$icon   = '/wp-content/uploads/2020/08/xls.svg';
								break;
							case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
								$border = '#1e96e6';
								$icon   = '/wp-content/uploads/2020/08/docx.svg';
								break;
							case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
								$border = '#91cda0';
								$icon   = '/wp-content/uploads/2020/08/xlsx.svg';
								break;
						}
					} ?>

                    <li class="col-sm-4 col-md-3 col-lg-3 col-sx-12">
                        <div class="freelance-portfolio-wrap" id="portfolio_item_<?php echo $document[ 'id' ] ?>"
							<?php if ( $is_application_mime ) { ?>
                                style="border: 2px solid <?php echo $border ?>"
							<?php } ?>
                        >
                            <div class="freelance-portfolio"
                                 style="background:url(<?php echo $document[ 'url' ] ?>) center no-repeat;">
                                <a href="javascript:void(0)" class="fre-view-portfolio-new"
                                   data-id="<?php echo $document[ 'id' ] ?>"></a>
                                <img src="<?php echo $document[ 'url' ] ?>" style="display:none;">

								<?php if ( $is_application_mime ) { ?>
                                    <img src="<?php echo $icon ?>"
                                         style="width: 50px; position: absolute; bottom: 25px; left: 10px;">

                                    <span style="position: absolute; top: 25px; left: 15px;">
                                        <?php echo $document[ 'title' ] ?>
                                    </span>
								<?php } ?>
                            </div>
							<?php if ( true === $is_edit ) { ?>
                                <div class="portfolio-action">
									<?php if ( $is_application_mime ) { ?>
                                        <a href="<?php echo $document[ 'url' ] ?>" target="_blank"
                                           class="fre-submit-btn btn-center"><?php _e( 'Open', ET_DOMAIN ) ?></a>
									<?php } else { ?>
                                        <a href="#modal_show_file" data-toggle="modal" class="fre-submit-btn btn-center"
                                           data-id="<?php echo $document[ 'id' ] ?>"><?php _e( 'Open', ET_DOMAIN ) ?></a>
									<?php } ?>

                                    <a href="#modal_delete_file" data-toggle="modal" class="fre-cancel-btn btn-center"
                                       data-file_id="<?php echo $document[ 'id' ] ?>"><?php _e( 'Remove', ET_DOMAIN ) ?></a>
                                </div>
							<?php } ?>
                        </div>
                    </li>
				<?php } ?>
        </ul>
        <div class="modal" id="modal_show_file" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal_title">
							<?php _e( 'My Documents & Licenses', ET_DOMAIN ); ?>
                        </div>
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" class="show_file">
                        <div class="fre-form-btn">
                            <span class="fre-cancel-btn fre-form-close"
                                  data-dismiss="modal"><?php _e( 'Close', ET_DOMAIN ) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modal_delete_file" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"></button>
						<?php _e( 'Are your sure you want to delete this item?', ET_DOMAIN ) ?>
                    </div>
                    <div class="modal-body">
                        <form class="fre-modal-form form_delete_file" id="form_delete_file">
                            <div class="fre-content-confirm">
                                <p><?php _e( "Once the item is deleted, it will be permanently removed from the site and its information won't be recovered.", ET_DOMAIN ) ?></p>
                            </div>
                            <input type="hidden" value="" name="ID">
                            <div class="fre-form-btn">
                                <button class="fre-submit-btn btn-left btn_submit_document"
                                        type="submit"><?php _e( 'Confirm', ET_DOMAIN ) ?></button>
                                <span class="fre-cancel-btn"
                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
	<?php }
		wp_enqueue_script( 'doc_free', '/wp-content/themes/freelanceengine/js/doc_free.js', [], false, true ); ?>
</div>
<style>
    .documents_page img {
        border-radius: 0;
        -webkit-border-radius: 0;
    }

    .documents_page .freelance-portfolio {
        height: 200px;
    }

    .documents_page .freelance-portfolio-wrap {
        margin-bottom: 30px;
    }

    #modal_show_file .fre-form-close {
        margin: 0 auto;
    }
</style>