<?php

class Mailster_Signup_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'mailster_signup', // Base ID
			'(Mailster) ' . esc_html__( 'Newsletter Signup Form', 'mailster' ), // Name
			array( 'description' => esc_html__( 'Sign Up form for the newsletter', 'mailster' ) ) // Args
		);
	}


	/**
	 *
	 *
	 * @param unknown $instance
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$title       = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text_before = apply_filters( 'widget_text_before', empty( $instance['text_before'] ) ? '' : $instance['text_before'], $instance, $this->id_base );
		$form        = apply_filters( 'widget_form', empty( $instance['form'] ) ? 0 : $instance['form'], $instance, $this->id_base );
		$text_after  = apply_filters( 'widget_text_after', empty( $instance['text_after'] ) ? '' : $instance['text_after'], $instance, $this->id_base );
		$on_homepage = empty( $instance['on_homepage'] ) ? '' : $instance['on_homepage'];

		$forms = mailster( 'forms' )->get_all();

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'mailster' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<label for="<?php echo $this->get_field_id( 'form' ); ?>"><?php esc_html_e( 'Form', 'mailster' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'form' ); ?>" name="<?php echo $this->get_field_name( 'form' ); ?>" >
		<?php foreach ( $forms as $id => $f ) : ?>
			<option value="<?php echo $f->ID; ?>"<?php echo $form == $f->ID ? ' selected' : ''; ?>>
				<?php echo esc_html( '#' . $f->ID . ' ' . $f->name ); ?>
			</option>
		<?php endforeach; ?>
		</select>
		<a href="edit.php?post_type=newsletter&page=mailster_forms&new"><?php esc_html_e( 'add form', 'mailster' ); ?></a>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php esc_html_e( 'Text before the form', 'mailster' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" type="text" value="<?php echo esc_attr( $text_before ); ?>" />
		<label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php esc_html_e( 'Text after the form', 'mailster' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" type="text" value="<?php echo esc_attr( $text_after ); ?>" />
		</p>
		<p>
		<label><input id="<?php echo $this->get_field_id( 'on_homepage' ); ?>" name="<?php echo $this->get_field_name( 'on_homepage' ); ?>" type="checkbox" value="1" <?php checked( $on_homepage ); ?> /> <?php esc_html_e( 'Show form on the newsletter homepage.', 'mailster' ); ?></label>
		</p>
		<?php
	}


	/**
	 *
	 *
	 * @param unknown $new_instance
	 * @param unknown $old_instance
	 * @return unknown
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance                = array();
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['text_before'] = ( $new_instance['text_before'] );
		$instance['form']        = strip_tags( $new_instance['form'] );
		$instance['text_after']  = ( $new_instance['text_after'] );
		$instance['on_homepage'] = isset( $new_instance['on_homepage'] );

		return $instance;
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $instance
	 * @return unknown
	 */
	public function widget( $args, $instance ) {
		global $post;
		// outputs the content of the widget
		extract( $args );

		if ( $post && mailster_option( 'homepage' ) == $post->ID && empty( $instance['on_homepage'] ) ) {
			return false;
		}

		$title       = apply_filters( 'widget_title', $instance['title'] );
		$text_before = apply_filters( 'widget_text_before', isset( $instance['text_before'] ) ? $instance['text_before'] : false );
		$form_id     = apply_filters( 'widget_form', $instance['form'] );
		$text_after  = apply_filters( 'widget_text_after', isset( $instance['text_after'] ) ? $instance['text_after'] : false );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="mailster-widget mailster-widget-signup">';
		if ( $text_before ) {
			echo '<div class="mailster-widget-text mailster-widget-text-before">' . $text_before . '</div>';
		}

		mailster_form( $form_id, true, 'mailster-in-widget' );

		if ( $text_after ) {
			echo '<div class="mailster-widget-text mailster-widget-text-after">' . $text_after . '</div>';
		}
		echo '</div>';

		echo $after_widget;
	}


}


class Mailster_Newsletter_List_Widget extends WP_Widget {


	public function __construct() {
		parent::__construct(
			'mailster_list_newsletter', // Base ID
			'(Mailster) ' . esc_html__( 'Newsletter List', 'mailster' ), // Name
			array( 'description' => esc_html__( 'Display the most recent newsletters', 'mailster' ) ) // Args
		);

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $instance
	 * @return unknown
	 */
	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_recent_newsletter', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Latest Newsletter', 'mailster' ) : $instance['title'], $instance, $this->id_base );
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
			$number = 10;
		}

		$r = new WP_Query(
			apply_filters(
				'widget_newsletter_args',
				array(
					'post_type'           => 'newsletter',
					'posts_per_page'      => $number,
					'no_found_rows'       => true,
					'post_status'         => array( 'finished', 'active' ),
					'ignore_sticky_posts' => true,
				)
			)
		);
		if ( $r->have_posts() ) :
			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			?>
		<div class="mailster-widget mailster-widget-recent-newsletter">
		<ul>
			<?php
			while ( $r->have_posts() ) :
				$r->the_post();
				?>
			<li><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( get_the_title() ? get_the_title() : get_the_ID() ); ?>">
				<?php
				if ( get_the_title() ) {
					the_title();
				} else {
					the_ID(); }
				?>
			</a></li>
			<?php endwhile; ?>
		</ul>
		</div>
			<?php
			echo $after_widget;

			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

		endif;

		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_recent_newsletter', $cache, 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $new_instance
	 * @param unknown $old_instance
	 * @return unknown
	 */
	public function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_entries'] ) ) {
			delete_option( 'widget_recent_entries' );
		}

		return $instance;
	}


	public function flush_widget_cache() {
		wp_cache_delete( 'widget_recent_newsletter', 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $instance
	 */
	public function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__( 'Latest Newsletter', 'mailster' );
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'mailster' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of Newsletters', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		<?php
	}


}


class Mailster_Newsletter_Subscribers_Count_Widget extends WP_Widget {


	public function __construct() {
		parent::__construct(
			'mailster_subscribers_count', // Base ID
			'(Mailster) ' . esc_html__( 'Number of Subscribers', 'mailster' ), // Name
			array( 'description' => esc_html__( 'Display the number of your Subscribers', 'mailster' ) ) // Args
		);

		add_action( 'mailster_subscriber_change_status', array( &$this, 'flush_widget_cache' ) );
		add_action( 'mailster_unassign_lists', array( &$this, 'flush_widget_cache' ) );
		add_action( 'mailster_update_subscriber', array( &$this, 'flush_widget_cache' ) );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $instance
	 * @return unknown
	 */
	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_subscribers_count', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$instance = wp_parse_args(
			$instance,
			array(
				'widget_id' => $this->id,
				'prefix'    => '',
				'postfix'   => esc_html__( 'Subscribers', 'mailster' ),
				'formatted' => true,
				'round'     => 1,
			)
		);

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );
		?>
		<?php echo isset( $before_widget ) ? $before_widget : ''; ?>
		<?php echo '<div class="mailster-widget mailster-widget-subscribers-count">'; ?>
		<?php echo isset( $instance['prefix'] ) ? $instance['prefix'] : ''; ?>
		<?php echo do_shortcode( '[newsletter_subscribers formatted="' . $instance['formatted'] . '" round="' . $instance['round'] . '"]' ); ?>
		<?php echo isset( $instance['postfix'] ) ? $instance['postfix'] : ''; ?>
		<?php echo '</div>'; ?>
		<?php echo isset( $after_widget ) ? $after_widget : ''; ?>
		<?php

		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_subscribers_count', $cache, 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $new_instance
	 * @param unknown $old_instance
	 * @return unknown
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['prefix']    = $new_instance['prefix'];
		$instance['postfix']   = $new_instance['postfix'];
		$instance['formatted'] = (bool) $new_instance['formatted'];
		$instance['round']     = (int) $new_instance['round'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_entries'] ) ) {
			delete_option( 'widget_recent_entries' );
		}

		return $instance;
	}


	public function flush_widget_cache() {
		wp_cache_delete( 'widget_subscribers_count', 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $instance
	 */
	public function form( $instance ) {
		$prefix    = isset( $instance['prefix'] ) ? $instance['prefix'] : '';
		$postfix   = isset( $instance['postfix'] ) ? $instance['postfix'] : esc_html__( 'Subscribers', 'mailster' );
		$formatted = isset( $instance['formatted'] ) ? (bool) $instance['formatted'] : true;
		$round     = isset( $instance['round'] ) ? absint( $instance['round'] ) : 1;
		?>
		<p><label for="<?php echo $this->get_field_id( 'prefix' ); ?>"><?php esc_html_e( 'Prefix', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'prefix' ); ?>" name="<?php echo $this->get_field_name( 'prefix' ); ?>" type="text" value="<?php echo esc_attr( $prefix ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'postfix' ); ?>"><?php esc_html_e( 'Postfix', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'postfix' ); ?>" name="<?php echo $this->get_field_name( 'postfix' ); ?>" type="text" value="<?php echo esc_attr( $postfix ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Round up to the next', 'mailster' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'round' ); ?>" >
			<option value="1" <?php selected( $round, 1 ); ?>><?php esc_html_e( 'do not round', 'mailster' ); ?></option>
			<option value="10" <?php selected( $round, 10 ); ?>><?php echo number_format( 10 ); ?></option>
			<option value="100" <?php selected( $round, 100 ); ?>><?php echo number_format( 100 ); ?></option>
			<option value="1000" <?php selected( $round, 1000 ); ?>><?php echo number_format( 1000 ); ?></option>
			<option value="10000" <?php selected( $round, 10000 ); ?>><?php echo number_format( 10000 ); ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id( 'formatted' ); ?>"><input id="<?php echo $this->get_field_id( 'formatted' ); ?>" name="<?php echo $this->get_field_name( 'formatted' ); ?>" type="checkbox" value="1" <?php checked( $formatted ); ?> /><?php esc_html_e( 'format number', 'mailster' ); ?></label>
		</p>
		<?php if ( ! empty( $instance ) ) : ?>
		<p><strong><?php esc_html_e( 'Preview', 'mailster' ); ?></strong></p>
		<p class="description"><?php $this->widget( array(), $instance ); ?></p>
		<?php endif; ?>
		<?php
	}


}

class Mailster_Newsletter_Subscriber_Button_Widget extends WP_Widget {


	public function __construct() {
		parent::__construct(
			'mailster_subscriber_button', // Base ID
			'(Mailster) ' . esc_html__( 'Subscriber Button', 'mailster' ), // Name
			array( 'description' => esc_html__( 'Display a button to let users subscribe', 'mailster' ) ) // Args
		);

		add_action( 'mailster_subscriber_change_status', array( &$this, 'flush_widget_cache' ) );
		add_action( 'mailster_unassign_lists', array( &$this, 'flush_widget_cache' ) );
		add_action( 'mailster_update_subscriber', array( &$this, 'flush_widget_cache' ) );
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @param unknown $instance
	 * @return unknown
	 */
	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'mailster_subscriber_button', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$instance = wp_parse_args(
			$instance,
			array(
				'widget_id' => $this->id,
				'title'     => esc_html__( 'Subscribe to our Newsletter', 'mailster' ),
				'prefix'    => '',
				'postfix'   => '',
				'form'      => 1,
				'label'     => esc_html__( 'Subscribe', 'mailster' ),
				'design'    => 'default',
				'width'     => 480,
				'showcount' => true,
				'ontop'     => false,
			)
		);

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );
		?>
		<?php echo isset( $before_widget ) ? $before_widget : ''; ?>
		<?php
		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}
		?>
		<?php echo '<div class="mailster-widget mailster-widget-subscriber-button">'; ?>
		<?php echo isset( $instance['prefix'] ) ? $instance['prefix'] : ''; ?>
		<?php echo do_shortcode( '[newsletter_button id=' . $instance['form'] . ' design="' . $instance['design'] . '' . ( $instance['ontop'] ? ' ontop' : '' ) . '" label="' . $instance['label'] . '" showcount="' . $instance['showcount'] . '" width="' . $instance['width'] . '"]' ); ?>
		<?php echo isset( $instance['postfix'] ) ? $instance['postfix'] : ''; ?>
		<?php echo '</div>'; ?>
		<?php echo isset( $after_widget ) ? $after_widget : ''; ?>
		<?php

		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_subscribers_count', $cache, 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $new_instance
	 * @param unknown $old_instance
	 * @return unknown
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['prefix']    = $new_instance['prefix'];
		$instance['postfix']   = $new_instance['postfix'];
		$instance['form']      = (int) $new_instance['form'];
		$instance['label']     = $new_instance['label'];
		$instance['design']    = $new_instance['design'];
		$instance['width']     = $new_instance['width'];
		$instance['showcount'] = (bool) $new_instance['showcount'];
		$instance['ontop']     = (bool) $new_instance['ontop'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_entries'] ) ) {
			delete_option( 'widget_recent_entries' );
		}

		return $instance;
	}


	public function flush_widget_cache() {
		wp_cache_delete( 'widget_subscribers_count', 'widget' );
	}


	/**
	 *
	 *
	 * @param unknown $instance
	 */
	public function form( $instance ) {

		$title   = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Subscribe to our Newsletter', 'mailster' );
		$prefix  = isset( $instance['prefix'] ) ? $instance['prefix'] : '';
		$postfix = isset( $instance['postfix'] ) ? $instance['postfix'] : '';

		$form      = isset( $instance['form'] ) ? $instance['form'] : 1;
		$label     = isset( $instance['label'] ) ? $instance['label'] : esc_html__( 'Subscribe', 'mailster' );
		$design    = isset( $instance['design'] ) ? $instance['design'] : 'default';
		$width     = isset( $instance['width'] ) ? $instance['width'] : 480;
		$showcount = isset( $instance['showcount'] ) ? $instance['showcount'] : true;
		$ontop     = isset( $instance['ontop'] ) ? $instance['ontop'] : false;

		$forms = mailster( 'forms' )->get_all();

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'mailster' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'prefix' ); ?>"><?php esc_html_e( 'Prefix', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'prefix' ); ?>" name="<?php echo $this->get_field_name( 'prefix' ); ?>" type="text" value="<?php echo esc_attr( $prefix ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'postfix' ); ?>"><?php esc_html_e( 'Postfix', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'postfix' ); ?>" name="<?php echo $this->get_field_name( 'postfix' ); ?>" type="text" value="<?php echo esc_attr( $postfix ); ?>" /></p>

		<p>
		<label for="<?php echo $this->get_field_id( 'form' ); ?>"><?php esc_html_e( 'Form', 'mailster' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'form' ); ?>" name="<?php echo $this->get_field_name( 'form' ); ?>" >
		<?php foreach ( $forms as $id => $f ) { ?>
			<option value="<?php echo $f->ID; ?>"<?php echo $form == $f->ID ? ' selected' : ''; ?>>
				<?php echo esc_html( '#' . $f->ID . ' ' . $f->name ); ?>
			</option>
		<?php } ?>
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Design', 'mailster' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'design' ); ?>" >
			<option value="default" <?php selected( $design, 'default' ); ?>><?php esc_html_e( 'default', 'mailster' ); ?></option>
			<option value="wp" <?php selected( $design, 'wp' ); ?>>WordPress</option>
			<option value="twitter" <?php selected( $design, 'twitter' ); ?>>Twitter</option>
			<option value="flat" <?php selected( $design, 'flat' ); ?>><?php esc_html_e( 'Flat', 'mailster' ); ?></option>
			<option value="minimal" <?php selected( $design, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'mailster' ); ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php esc_html_e( 'Label', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" type="text" value="<?php echo esc_attr( $label ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php esc_html_e( 'Width', 'mailster' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" class="small-text" /></p>
		<p><label for="<?php echo $this->get_field_id( 'showcount' ); ?>"><input id="<?php echo $this->get_field_id( 'showcount' ); ?>" name="<?php echo $this->get_field_name( 'showcount' ); ?>" type="checkbox" value="1" <?php checked( $showcount ); ?> /><?php esc_html_e( 'Show Count', 'mailster' ); ?></label></p>
		<p><label for="<?php echo $this->get_field_id( 'ontop' ); ?>"><input id="<?php echo $this->get_field_id( 'ontop' ); ?>" name="<?php echo $this->get_field_name( 'ontop' ); ?>" type="hidden" value=""><input id="<?php echo $this->get_field_id( 'ontop' ); ?>" name="<?php echo $this->get_field_name( 'ontop' ); ?>" type="checkbox" value="1" <?php checked( $ontop ); ?> /><?php esc_html_e( 'Count above Button', 'mailster' ); ?></label></p>
		<?php
	}


}
