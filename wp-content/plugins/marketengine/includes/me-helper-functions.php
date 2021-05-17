<?php
/**
 * This file containt helper functions
 * @package Includes/Helper
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieve page id base on option name
 *
 *
 * @package Includes/Helper
 * @category Function
 *
 * @access public
 * @param  string $page_option_name The page option name
 *
 * @return int| null Page Id if exist or null if page not existed
 */
function marketengine_get_option_page_id($page_option_name)
{
    $page_id = absint(marketengine_option('me_' . $page_option_name . '_page_id'));
    $page    = get_post($page_id);
    if (!$page) {
        return -1;
    }
    return $page_id;
}

if (!function_exists('marketengine_get_page_permalink')) {
    /**
     * Retrieve page url base on page option name
     *
     *
     * @package Includes/Helper
     * @category Function
     *
     * @access public
     * @param  string $page_option_name The page option name
     * @return string
     */
    function marketengine_get_page_permalink($page_name)
    {
        $page = marketengine_option('me_' . $page_name . '_page_id');
        if (!$page = get_post($page)) {
            return home_url();
        }
        return get_permalink($page);
    }
}

/**
 * Returns the endpoint name by query_var.
 *
 * @access public
 *
 * @package Includes/Helper
 * @category Function
 *
 * @param  string $query_var
 * @return string
 */
function marketengine_get_endpoint_name($query_var)
{
    $query_var        = str_replace('-', '_', $query_var);
    $defaults         = marketengine_default_endpoints();
    $default_endpoint = isset($defaults[$query_var]) ? $defaults[$query_var] : '';

    $endpoint = marketengine_option('ep_' . $query_var);
    return $endpoint ? $endpoint : $default_endpoint;
}

/**
 * Returns the default endpoints.
 *
 * @package Includes/Helper
 * @category Function
 *
 * @access public
 * @return array of endpoints
 */
function marketengine_default_endpoints()
{
    $endpoint_arr = array(
        'forgot_password'   => 'forgot-password',
        'reset_password'    => 'reset-password',
        'register'          => 'register',
        'edit_profile'      => 'edit-profile',
        'change_password'   => 'change-password',
        'listings'          => 'listings',
        'orders'            => 'orders',
        'order_id'          => 'order',
        'purchases'         => 'purchases',
        'pay'               => 'pay',
        'listing_id'        => 'listing-id',
        'seller_id'         => 'seller',
        'inquiry'           => 'inquiry',
    );
    return $endpoint_arr;
}

/**
 * Get the option value
 *
 * @param string $option The option name
 * @param mix $default The option default value
 *
 * @package Includes/Helper
 * @category Function
 *
 * @since 1.0
 * @return string
 */
function marketengine_option($option, $default = '')
{
    $options = ME_Options::get_instance();
    return $options->get_option($option, $default);
}

/**
 * Update option value
 *
 * @param string $option The option name
 * @param mix $value The option value
 *
 * @package Includes/Helper
 * @category Function
 *
 * @since 1.0
 * @return string
 */
function marketengine_update_option($option, $value)
{
    $options = ME_Options::get_instance();
    return $options->update_option($option, $value);
}

/**
 * Retrieve the client ip
 *
 * @package Includes/Helper
 * @category Function
 *
 * @since 1.0
 * @return string
 */
function marketengine_get_client_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
/**
 * Retrieve the client agent
 *
 * @package Includes/Helper
 * @category Function
 *
 * @since 1.0
 * @return string
 */
function marketengine_get_client_agent()
{
    return !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
}

/**
 * Trims text to a certain number of words.
 *
 * This function is localized. For languages that count 'words' by the individual
 * character (such as East Asian languages), the $num_words argument will apply
 * to the number of individual characters.
 *
 * @package Includes/Helper
 * @category Function
 *
 * @since 1.0
 *
 * @param string $text      Text to trim.
 * @param int    $num_words Number of words. Default 55.
 * @param string $more      Optional. What to append if $text needs to be trimmed. Default '&hellip;'.
 * @return string Trimmed text.
 */
function marketengine_trim_words($text, $num_words = 55, $more = null)
{
    if (null === $more) {
        $more = __('&hellip;');
    }

    $original_text = $text;
    //$text = wp_strip_all_tags( $text );

    /*
     * translators: If your word count is based on single characters (e.g. East Asian characters),
     * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
     * Do not translate into your own language.
     */
    if (strpos(_x('words', 'Word count type. Do not translate!'), 'characters') === 0 && preg_match('/^utf\-?8$/i', get_option('blog_charset'))) {
        //$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
        //preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice($words_array[0], 0, $num_words + 1);
        $sep         = '';
    } else {
        $words_array = preg_split("/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY);
        $sep         = ' ';
    }

    if (count($words_array) > $num_words) {
        array_pop($words_array);
        $text = implode($sep, $words_array);
        $text = $text . $more;
    } else {
        $text = implode($sep, $words_array);
    }

    /**
     * Filters the text content after words have been trimmed.
     *
     * @since 3.3.0
     *
     * @param string $text          The trimmed text.
     * @param int    $num_words     The number of words to trim the text to. Default 5.
     * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
     * @param string $original_text The text before it was trimmed.
     */
    return apply_filters('marketengine_trim_words', $text, $num_words, $more, $original_text);
}

/**
 * Format file size Unit
 *
 * @package Includes/Helper
 * @category Function
 *
 * @param int $bytes
 * @return string
 */
function marketengine_format_size_units($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' kB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
