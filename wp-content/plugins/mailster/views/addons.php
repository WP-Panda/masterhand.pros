<div class="wrap">

	<h1><?php esc_html_e( 'Mailster Newsletter Add Ons', 'mailster' ); ?></h1>

	<h3><?php esc_html_e( 'Extend the functionality of Mailster', 'mailster' ); ?></h3>

<?php $addons = mailster( 'helper' )->get_addons(); ?>

<?php if ( is_wp_error( $addons ) ) : ?>

	<div class="error below-h2">
		<p><strong><?php esc_html_e( 'There was an error retrieving the list from the server:', 'mailster' ); ?></strong><br><?php echo esc_html( $addons->get_error_message() ); ?></p>
	</div>

<?php else : ?>

	<ul class="addons-wrap">
		<?php foreach ( $addons as $addon ) : ?>
			<?php

			if ( ! empty( $addon->hidden ) ) {
				continue;
			}

			$addon->link = isset( $addon->link ) ? $addon->link : ( isset( $addon->wpslug ) ? 'https://wordpress.org/plugins/' . dirname( $addon->wpslug ) . '/' : '' );

			?>
		<li class="mailster-addon<?php echo ! empty( $addon->is_free ) ? ' is-free' : ''; ?><?php echo ! empty( $addon->is_feature ) ? ' is-feature' : ''; ?>">
			<div class="bgimage" style="background-image:url(<?php echo isset( $addon->image ) ? esc_url( $addon->image ) : ''; ?>)">
				<?php if ( isset( $addon->wpslug ) && current_user_can( 'manage_plugins' ) ) : ?>
					<?php
					$url = add_query_arg(
						array(
							'tab'       => 'plugin-information',
							'plugin'    => dirname( $addon->wpslug ),
							'from'      => 'import',
							'TB_iframe' => true,
							'width'     => 745,
							'height'    => 745,
						),
						network_admin_url( 'plugin-install.php' )
					);
					?>
					<a href="<?php echo esc_url( $url ); ?>" class="thickbox">&nbsp;</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $addon->link ); ?>" class="external">&nbsp;</a>
				<?php endif; ?>
			</div>
			<h4><?php echo esc_html( $addon->name ); ?></h4>
			<p class="author"><?php esc_html_e( 'by', 'mailster' ); ?>
			<?php
			if ( $addon->author_url ) :
				echo '<a href="' . esc_url( $addon->author_url ) . '" class="external">' . esc_html( $addon->author ) . '</a>';
			else :
				echo esc_html( $addon->author );
			endif;
			?>
			</p>
			<p class="description"><?php echo strip_tags( $addon->description ); ?></p>
			<div class="action-links">
			<?php if ( ! empty( $addon->wpslug ) ) : ?>

				<?php if ( is_dir( dirname( WP_PLUGIN_DIR . '/' . $addon->wpslug ) ) ) : ?>
					<?php if ( is_plugin_active( $addon->wpslug ) ) : ?>
						<a class="button" href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $addon->wpslug, 'deactivate-plugin_' . $addon->wpslug ); ?>"><?php esc_html_e( 'Deactivate', 'mailster' ); ?></a>
					<?php elseif ( is_plugin_inactive( $addon->wpslug ) ) : ?>
						<a class="button" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $addon->wpslug, 'activate-plugin_' . $addon->wpslug ); ?>"><?php esc_html_e( 'Activate', 'mailster' ); ?></a>
					<?php endif; ?>
				<?php elseif ( current_user_can( 'install_plugins' ) && current_user_can( 'update_plugins' ) ) : ?>
						<a class="button button-primary" href="<?php echo wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . dirname( $addon->wpslug ) . '&mailster-addon' ), 'install-plugin_' . dirname( $addon->wpslug ) ); ?>"><?php esc_html_e( 'Install', 'mailster' ); ?></a>
				<?php endif; ?>

			<?php else : ?>

					<a class="button button-primary external" href="<?php echo esc_url( $addon->link ); ?>"><?php esc_html_e( 'More Information', 'mailster' ); ?></a>

			<?php endif; ?>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
<div class="clear affiliate-note">
	Disclosure: Some of the links on this page are affiliate links. This means if you click on the link and purchase the item, we may receive an affiliate commission.
</div>
<?php endif; ?>

<div id="ajax-response"></div>
<br class="clear">
</div>
