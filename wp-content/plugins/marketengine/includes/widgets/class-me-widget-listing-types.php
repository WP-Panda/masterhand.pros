<?php

if (!defined('ABSPATH')) {
    exit;
}


/**
 * MarketEngine Listing Categories Widget
 *
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @author EngineTeam
 * @since 1.0
 */
class ME_Widget_Listing_Types extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    public function ME_Widget_Listing_Types() {
        $widget_ops = array('classname' => 'me-listing-types', 'description' => __("A list listing types", "enginethemes"));
        parent::__construct('me-listing-types', __("MarketEngine Listing Types", "enginethemes"), $widget_ops);
    }

    /**
     * Outputs the content for the current Listing Categories widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Categories widget instance.
     */
    public function widget($args, $instance) {
        global $wp_query;
        if (!$wp_query->is_post_type_archive('listing') && !$wp_query->is_tax(get_object_taxonomies('listing'))) {
            return ;
        }
        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Listing Types', 'enginethemes') : $instance['title'], $instance, $this->id_base);

        echo $args['before_widget'];

        $types = marketengine_get_listing_types();

        $current = !empty($_GET['type']) ? esc_attr( $_GET['type'] ) : '';

        ?>
            <div class="me-title-sidebar">
                <h2><?php echo $title  ?></h2>
            </div>
            <div class="me-listingtype-filter">
                <label>
                    <input type="radio" name="type" value="" <?php checked( '', $current); ?> onclick="window.location.href='<?php echo remove_query_arg('type'); ?>'">
                    <a href="<?php echo remove_query_arg('type'); ?>" ><?php _e("All", "enginethemes"); ?></a>
                </label>
            </div>
            <?php foreach ($types as $key => $type) : ?>

                <?php 
                    $link = add_query_arg('type', $key);
                    $link = preg_replace('%\/page/[0-9]+%', '',  $link );
                    if($key == 'contact') {
                        $link = remove_query_arg( array('price-min', 'price-max'), $link );
                    }
                ?>

                <div class="me-listingtype-filter">
                    <label>
                        <input type="radio" name="type" value="<?php echo $key; ?>" <?php checked( $key, $current); ?> onclick="window.location.href='<?php echo esc_attr( $link ); ?>'">
                        <a href="<?php echo esc_attr( $link ); ?>"><?php echo $type; ?></a>
                    </label>
                </div>

            <?php endforeach; ?>

        <?php

        echo $args['after_widget'];
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    public function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );

        return $instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    public function form($instance) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = sanitize_text_field( $instance['title'] );
        $count = isset($instance['count']) ? (bool) $instance['count'] :false;
        $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
    <?php
    }
}