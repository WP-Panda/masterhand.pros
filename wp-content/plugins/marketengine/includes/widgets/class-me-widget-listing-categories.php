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
class ME_Widget_Listing_Categories extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    public function ME_Widget_Listing_Categories() {
        $widget_ops = array('classname' => 'me-listing-categories', 'description' => __("A list or dropdown of listing categories.", "enginethemes"));
        parent::__construct('me-listing-categories', __("MarketEngine Categories", "enginethemes"), $widget_ops);
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
        static $first_dropdown = true;

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories') : $instance['title'], $instance, $this->id_base);

        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        echo $args['before_widget'];
        if ($title) {
            //echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'orderby'      => 'name',
            'show_count'   => $c,
            'hierarchical' => $h,
            'taxonomy' => 'listing_category',
        );

        if ($d) {
            $dropdown_id    = ($first_dropdown) ? 'listing_category' : "{$this->id_base}-dropdown-{$this->number}";
            $first_dropdown = false;

            // echo '<label class="screen-reader-text" for="' . esc_attr($dropdown_id) . '">' . $title . '</label>';

            $cat_args['show_option_none'] = __('Select Category');
            $cat_args['id']               = $dropdown_id;

            /**
             * Filter the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             */
            wp_dropdown_categories(apply_filters('marketengine_listing_categories_dropdown_args', $cat_args));
            ?>

<script type='text/javascript'>
/* <![CDATA[ */
(function() {
    var dropdown = document.getElementById( "<?php echo esc_js($dropdown_id); ?>" );
    function onCatChange() {
        if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
            location.href = "<?php echo home_url(); ?>/?listing_category=" + dropdown.options[ dropdown.selectedIndex ].value;
        }
    }
    dropdown.onchange = onCatChange;
})();
/* ]]> */
</script>

<?php
} else {
            ?>
        <div class="me-title-sidebar">
            <?php
                if ($title) { ?>
                    <h2><?php echo $title ?></h2>
            <?php 
                }
            ?>
            
        </div>
        <ul class="me-menu-categories">
<?php
$cat_args['title_li'] = '';
$cat_args['show_option_all'] = __("All categories", "enginethemes");

            /**
             * Filter the arguments for the Categories widget.
             *
             * @since 2.8.0
             *
             * @param array $cat_args An array of Categories widget options.
             */
            wp_list_categories(apply_filters('marketengine_listing_categories_args', $cat_args));
            ?>
        </ul>
<?php
}
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
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

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

        <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
        <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
        <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
        <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
    <?php
    }
}