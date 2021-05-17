<?php

class MailsterExport {


	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}

	public function init() {

		add_filter( 'wp_import_post_data_processed', array( &$this, 'import_post_data' ), 10, 2 );
		add_action( 'wp_import_insert_post', array( &$this, 'convert_old_campaign_ids' ), 10, 4 );
		add_action( 'export_wp', array( &$this, 'set_temporary_export_meta' ) );
		add_filter( 'wxr_export_skip_postmeta', array( &$this, 'add_mailster_data_to_export' ), 10, 3 );

	}

	/**
	 *
	 *
	 * @param unknown $postdata
	 * @param unknown $post
	 * @return unknown
	 */
	public function import_post_data( $postdata, $post ) {

		if ( ! isset( $postdata['post_type'] ) || $postdata['post_type'] != 'newsletter' ) {
			return $postdata;
		}

		kses_remove_filters();

		preg_match_all( '/(src|background|href)=["\'](.*)["\']/Ui', $postdata['post_content'], $links );
		$links = $links[2];

		$old_home_url = '';
		foreach ( $links as $link ) {
			if ( preg_match( '/(.*)wp-content(.*)\/mailster/U', $link, $match ) ) {
				$new_link                 = str_replace( $match[0], MAILSTER_UPLOAD_URI, $link );
				$old_home_url             = $match[1];
				$postdata['post_content'] = str_replace( $link, $new_link, $postdata['post_content'] );
			}
		}

		if ( $old_home_url ) {
			$postdata['post_content'] = str_replace( $old_home_url, trailingslashit( home_url() ), $postdata['post_content'] );
		}

		mailster_notice( __( 'Please make sure all your campaigns are imported correctly!', 'mailster' ), 'error', false, 'import_campaings' );

		return $postdata;

	}


	public function convert_old_campaign_ids( $post_id, $original_post_ID, $postdata, $post ) {

		global $wpdb;

		if ( $postdata['post_type'] != 'newsletter' ) {
			return;
		}

		if ( $post_id == $original_post_ID ) {
			return;
		}

		$tables = array( 'actions', 'queue', 'subscriber_meta' );

		echo '<h4>';
		printf( __( 'Updating Mailster tables for Campaign %s:', 'mailster' ), '"<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . $postdata['post_title'] . '</a>"' );
		echo '</h4>';

		foreach ( $tables as $table ) {
			printf( '<code>%s</code>', 'mailster_' . $table );

			$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_{$table} SET campaign_id = %d WHERE campaign_id = %d", $post_id, $original_post_ID );
			if ( false !== ( $rows = $wpdb->query( $sql ) ) ) {
				printf( '..' . __( 'completed for %d rows.', 'mailster' ), $rows );
			}
			echo '<br>';
		}

	}

	public function set_temporary_export_meta( $args ) {

		global $wpdb;

		if ( 'all' != $args['content'] && 'newsletter' != $args['content'] ) {
			return;
		}

		add_action( 'shutdown', array( &$this, 'unset_temporary_export_meta' ) );

		$sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) SELECT ID AS post_id, '_mailster_temp_export_hook', '1' FROM {$wpdb->posts} WHERE post_type = 'newsletter'";

		return $wpdb->query( $sql );

	}

	public function unset_temporary_export_meta() {

		global $wpdb;

		$sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_mailster_temp_export_hook'";

		return $wpdb->query( $sql );

	}

	public function add_mailster_data_to_export( $skip_it, $meta_key, $meta ) {
		if ( '_mailster_temp_export_hook' == $meta_key ) :
			global $wpdb;

			$tables = array( 'actions', 'queue', 'subscriber_meta' );

			foreach ( $tables as $table ) {
				$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailster_{$table} WHERE campaign_id = %d", $meta->post_id );

				$data = $wpdb->get_results( $sql, ARRAY_N );

				if ( ! empty( $data ) ) {
					printf(
						'<wp:postmeta>
						<wp:meta_key>%1$s</wp:meta_key>
						<wp:meta_value>>%2$s</wp:meta_value>
					</wp:postmeta>',
						wxr_cdata( '_mailster_table_data_' . $table ),
						wxr_cdata( serialize( $data ) )
					);
				}
			}

			?>

			<?php
			$skip_it = true;
		endif;

		return $skip_it;
	}


}
