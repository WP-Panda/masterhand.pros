<?php
	/**
	 * Функции для обработки пользователей
	 */

	function wpp_fr_acc_not_user_template() {
		if ( !is_user_logged_in() ) {
			wpp_get_template_part( apply_filters( 'wpp_fr_acc_user_not_logged_template', '' ), [] );
			die();
		}
	}


	if ( !function_exists( 'wpp_fr_not_logged_template_message' ) ) :

		/**
		 * Сообщени о запрете доступа не авторизованным пользователем
		 */

		function wpp_fr_not_logged_template_message() {

			printf( '<p>%s</p>', __( 'You must be logged in', 'wpp-fr' ) );
		}
	endif;


	if ( !function_exists( 'wpp_fr_per_user_upload_dir' ) ) :
		/**
		 * Каждому пользователю свою дирректорию для загрузки
		 *
		 * @param $original
		 *
		 * @return mixed
		 */
		function wpp_fr_per_user_upload_dir( $original ) {
			// use the original array for initial setup
			$modified = $original;
			// set our own replacements
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$subdir = $current_user->user_login;
				$modified[ 'subdir' ] = $subdir;
				$modified[ 'url' ] = $original[ 'baseurl' ] . '/' . $subdir;
				$modified[ 'path' ] = $original[ 'basedir' ] . DIRECTORY_SEPARATOR . $subdir;
			}
			return $modified;
		}
	endif;
	#add_filter( 'upload_dir', 'wpp_fr_per_user_upload_dir' );

	if ( !function_exists( 'wpp_fr_per_user_upload_dir' ) ) :
		/**
		 * Показывать в медиатеке только загрузки пользователя
		 *
		 * @param $query
		 *
		 * @return mixed
		 */
		function wpp_fr_show_only_current_user_attachments( $query ) {
			$user_id = get_current_user_id();
			if ( $user_id ) {
				$query[ 'author' ] = $user_id;
			}
			return $query;
		}
	endif;
	#add_filter( 'ajax_query_attachments_args', 'wpp_fr_show_only_current_user_attachments' );