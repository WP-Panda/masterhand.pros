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
class ME_Widget_Price_Filter extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    public function ME_Widget_Price_Filter() {
        $widget_ops = array('classname' => 'me-price-filter', 'description' => __("A price filter for listing archive", "enginethemes"));
        parent::__construct('me-price-filter', __("MarketEngine Price Filter", "enginethemes"), $widget_ops);
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
        global $wp, $wp_query;

        if (!$wp_query->is_post_type_archive('listing') && !$wp_query->is_tax(get_object_taxonomies('listing'))) {
            return ;
        }

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Price filter', 'enginethemes') : $instance['title'], $instance, $this->id_base);

        if ('' === get_option('permalink_structure')) {
            $form_action = remove_query_arg(array('page', 'paged'), add_query_arg($wp->query_string, '', home_url($wp->request)));
        } else {
            $form_action = preg_replace('%\/page/[0-9]+%', '', home_url(trailingslashit($wp->request)));
        }

        $prices = $this->get_filtered_price();

        $min = $prices->min_price;
        $max = $prices->max_price;

        if ($min === $max) {
            return;
        }

        echo $args['before_widget'];

        ?>
        <form method="get" action="<?php echo $form_action; ?>">
            <?php do_action('marketengine_before_price_filter_form'); ?>
            <div class="me-title-sidebar">
                <h2><?php echo $title; ?></h2>
            </div>
            <div class="me-price-filter">
                <div id="me-range-price" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="1"></div>
                <div class="me-row">
                    <div class="me-col-xs-5"><input class="me-range-price me-range-min" type="number" name="price-min" value="<?php echo !empty($_GET['price-min']) ? esc_attr( $_GET['price-min'] ) : $min; ?>"></div>
                    <div class="me-col-xs-2 "><span class="me-range-dash">-</span></div>
                    <div class="me-col-xs-5"><input class="me-range-price me-range-max" type="number" name="price-max" value="<?php echo !empty($_GET['price-max']) ? esc_attr( $_GET['price-max'] ) : $max; ?>"></div>
                </div>
            </div>
            <div class="me-filter-button">
                <input class="me-filter-btn" type="submit" value="<?php _e("Filter", "enginethemes");?>">
            </div>
            <?php if (!empty($_GET['orderby'])): ?>
                <input type="hidden" name="orderby" value="<?php echo esc_attr( $_GET['orderby'] ); ?>" ?>
            <?php endif;?>
            <?php if (!empty($_GET['keyword'])): ?>
                <input type="hidden" name="keyword" value="<?php echo esc_attr( $_GET['keyword'] ); ?>" ?>
            <?php endif;?>
            <?php do_action('marketengine_after_price_filter_form'); ?>
        </form>
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

        $instance                 = $old_instance;
        $instance['title']        = sanitize_text_field($new_instance['title']);
        $instance['count']        = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown']     = !empty($new_instance['dropdown']) ? 1 : 0;

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
        $instance     = wp_parse_args((array) $instance, array('title' => ''));
        $title        = sanitize_text_field($instance['title']);
        $count        = isset($instance['count']) ? (bool) $instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        $dropdown     = isset($instance['dropdown']) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:');?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
    <?php
}

    /**
     * Get filtered min price for current products.
     * @return int
     */
    protected function get_filtered_price() {
        global $wpdb, $wp_the_query;

        $args       = $wp_the_query->query_vars;
        $tax_query  = isset($args['tax_query']) ? $args['tax_query'] : array();
        $meta_query = isset($args['meta_query']) ? $args['meta_query'] : array();

        if (!empty($args['taxonomy']) && !empty($args['term'])) {
            $tax_query[] = array(
                'taxonomy' => $args['taxonomy'],
                'terms'    => array($args['term']),
                'field'    => 'slug',
            );
        }
        
        foreach ($meta_query as $key => $query) {
            if (!empty($meta_query['filter_price'])) {
                unset($meta_query[$key]);
            }
        }

        $meta_query = new WP_Meta_Query($meta_query);
        $tax_query  = new WP_Tax_Query($tax_query);

        $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
        $tax_query_sql  = $tax_query->get_sql($wpdb->posts, 'ID');

        $sql = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
        $sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
        $sql .= "   WHERE {$wpdb->posts}.post_type = 'listing'
                    AND {$wpdb->posts}.post_status = 'publish'
                    AND price_meta.meta_key IN ('" . implode("','", array_map('esc_sql', array('listing_price'))) . "')
                    AND price_meta.meta_value > '' ";

        if(get_query_var( 'keyword' )) {
            $search = esc_sql( get_query_var('keyword') );
            $sql .= $this->parse_search(array('s' => $search ));
        }

        $sql .= $tax_query_sql['where'] . $meta_query_sql['where'];
        
        return $wpdb->get_row($sql);
    }

    /**
     * Generate SQL for the WHERE clause based on passed search terms.
     *
     * @since 3.7.0
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param array $q Query variables.
     * @return string WHERE clause.
     */
    protected function parse_search( $q ) {
        global $wpdb;

        $search = '';

        // added slashes screw with quote grouping when done early, so done later
        $q['s'] = stripslashes( $q['s'] );
        
        $q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );
        $q['search_terms_count'] = 1;
        if ( ! empty( $q['sentence'] ) ) {
            $q['search_terms'] = array( $q['s'] );
        } else {
            if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
                $q['search_terms_count'] = count( $matches[0] );
                $q['search_terms'] = $this->parse_search_terms( $matches[0] );
                // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
                if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
                    $q['search_terms'] = array( $q['s'] );
            } else {
                $q['search_terms'] = array( $q['s'] );
            }
        }

        $n = ! empty( $q['exact'] ) ? '' : '%';
        $searchand = '';
        $q['search_orderby_title'] = array();
        foreach ( $q['search_terms'] as $term ) {
            // Terms prefixed with '-' should be excluded.
            $include = '-' !== substr( $term, 0, 1 );
            if ( $include ) {
                $like_op  = 'LIKE';
                $andor_op = 'OR';
            } else {
                $like_op  = 'NOT LIKE';
                $andor_op = 'AND';
                $term     = substr( $term, 1 );
            }

            if ( $n && $include ) {
                $like = '%' . $wpdb->esc_like( $term ) . '%';
                $q['search_orderby_title'][] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $like );
            }

            $like = $n . $wpdb->esc_like( $term ) . $n;
            $search .= $wpdb->prepare( "{$searchand}(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_excerpt $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s))", $like, $like, $like );
            $searchand = ' AND ';
        }

        if ( ! empty( $search ) ) {
            $search = " AND ({$search}) ";
            if ( ! is_user_logged_in() )
                $search .= " AND ($wpdb->posts.post_password = '') ";
        }

        return $search;
    }

    /**
     * Check if the terms are suitable for searching.
     *
     * Uses an array of stopwords (terms) that are excluded from the separate
     * term matching when searching for posts. The list of English stopwords is
     * the approximate search engines list, and is translatable.
     *
     * @since 3.7.0
     *
     * @param array $terms Terms to check.
     * @return array Terms that are not stopwords.
     */
    protected function parse_search_terms( $terms ) {
        $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
        $checked = array();

        $stopwords = $this->get_search_stopwords();

        foreach ( $terms as $term ) {
            // keep before/after spaces when term is for exact match
            if ( preg_match( '/^".+"$/', $term ) )
                $term = trim( $term, "\"'" );
            else
                $term = trim( $term, "\"' " );

            // Avoid single A-Z and single dashes.
            if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) )
                continue;

            if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
                continue;

            $checked[] = $term;
        }

        return $checked;
    }

    /**
     * Retrieve stopwords used when parsing search terms.
     *
     * @since 3.7.0
     *
     * @return array Stopwords.
     */
    protected function get_search_stopwords() {
        if ( isset( $this->stopwords ) )
            return $this->stopwords;

        /* translators: This is a comma-separated list of very common words that should be excluded from a search,
         * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
         * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
         */
        $words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
            'Comma-separated list of search stopwords in your language' ) );

        $stopwords = array();
        foreach ( $words as $word ) {
            $word = trim( $word, "\r\n\t " );
            if ( $word )
                $stopwords[] = $word;
        }

        /**
         * Filters stopwords used when parsing search terms.
         *
         * @since 3.7.0
         *
         * @param array $stopwords Stopwords.
         */
        $this->stopwords = apply_filters( 'wp_search_stopwords', $stopwords );
        return $this->stopwords;
    }
}