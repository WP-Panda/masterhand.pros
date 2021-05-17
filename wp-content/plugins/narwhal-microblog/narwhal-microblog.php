<?php 
/*
Plugin Name: Narwhal Microblog
Description: Adds a minimal front-end blogging form to your site.
Version: 2.2.11
Author: Billy Wilcosky
Author URI: https://wilcosky.com
License: GPL2
*/

/*
 * This plugin is nothing but Jon Smajda‘s Posthaste plugin from 2009
 * unpdated, since Posthaste appears to be abandoned. I fixed PHP warnings,
 * updated CSS, and will do my best to continue to improve this.
 *
 * This plugin reuses code from the Prologue and P2 Themes,
 * Copyright Joseph Scott, Matt Thomas, Noel Jackson, and Automattic
 * according to the terms of the GNU General Public License.
 * http://wordpress.org/extend/themes/prologue/
 * http://wordpress.org/extend/themes/p2/
 * */

defined('ABSPATH') or die('No coochie kopi!');

/***********
 * VARIABLES 
 ***********/
$posthasteVariables = array(
        "asidesCatName" => "asides"
    );



/************
 * FUNCTIONS 
 ************/

// When to display form
function posthasteDisplayCheck() {
    if(!$display = get_option('posthaste_display'))
        $display = 'front';

    switch ($display) {
        case 'front':
            if (is_home())
                $posthaste_display = true;
            break;
        case 'archive':
            if (is_archive() || is_home())
                $posthaste_display = true;
            break;
        case 'everywhere':
            if (!is_admin())
                $posthaste_display = true;
            break;
        case ((int)$display != 0):
            if (is_category($display))
                $posthaste_display = true;
            break;
        default:
            $posthaste_display = false;
    }

    return $posthaste_display ?? '';
}

// Which category to use
function posthasteCatCheck() {
    if (is_category())
        return get_cat_ID(single_cat_title('', false));
    else 
        return get_option('default_category', 1);
}

// Which tag to use
function posthasteTagCheck() {
    if (is_tag()) {
        $taxarray = get_term_by('name', single_tag_title('', false), 'post_tag', ARRAY_A);
        return $taxarray['name'];
    } else {
        return NULL;
    }
}


// Add to header
function posthasteHeader() {
    global $posthasteVariables;
    if('POST' == $_SERVER['REQUEST_METHOD']
        && !empty( $_POST['action'])
        && $_POST['action'] == 'post'
        && posthasteDisplayCheck() ) { // !is_admin() will get it on all pages

        if (!is_user_logged_in()) {
            wp_redirect( get_bloginfo( 'url' ) . '/' );
            exit;
        }

        if( !current_user_can('publish_posts')) {
            wp_redirect( get_bloginfo( 'url' ) . '/' );
            exit;
        }

        check_admin_referer( 'new-post' );
        $current_user = wp_get_current_user();
        $user_id       = $current_user->user_id;
        $post_content  = wp_kses_post($_POST['postText']);
        $post_title    = sanitize_text_field($_POST['postTitle']);
        $tags          = sanitize_text_field($_POST['tags']);
        $post_category = array($_POST['catsdd']);
        $returnUrl     = $_POST['posthasteUrl'];

        // set post_status 
        if (isset($_REQUEST[ 'postStatus' ]) && $_POST['postStatus'] == 'draft') {
            $post_status = 'draft';
        } else {
            $post_status = 'publish';    
        }

        // if title was kept empty, trim content for title 
        // & add to asides category if it exists (unless another
        // category was explicitly chosen in form)
        if (empty($post_title)) {
            $post_title      = strip_tags( $post_content );    
            $char_limit      = 40;    
            if( strlen( $post_title ) > $char_limit ) {
                $post_title = substr( $post_title, 0, $char_limit ) . ' ... ';
            }    
            // if "asides" category exists & title is empty, add to asides:
            if ($asidesCatID = get_cat_id($posthasteVariables['asidesCatName'])){
                $post_category = array($asidesCatID); 
            } 
        } 

        // create the post
        $post_id = wp_insert_post( array(
            'post_author'   => $user_id,        
            'post_title'    => $post_title,
            'post_category' => $post_category,
            'post_content'  => $post_content,
            'tags_input'    => $tags,
            'post_status'   => $post_status
        ) );
        
        // now redirect back to blog
        if ($post_status == 'draft') { 
            $postresult = '?draft=1';
        } else if ($post_status == 'publish') { 
            $postresult = '?success=1'; 
        } else { 
            $postresult = ''; 
        }
        wp_redirect( $returnUrl . $postresult );
        exit;
    }
}

// the post form
function posthasteForm() {
    // get options (if empty, fill in defaults & then get options)
    if(!$options = get_option('posthaste_fields')) { 
        posthasteAddDefaultFields(); 
        $options = get_option('posthaste_fields');
    } 
    
    if(current_user_can('publish_posts') && posthasteDisplayCheck() ) { 
        echo "\n\t".'<div id="narwhalForm">'."\n\t";
        if (isset($_GET['draft'])) { 
            echo '<div id="posthasteDraftNotice">'
                 .'<p>Post saved as draft. '
                 .'<a href="'.get_bloginfo('wpurl').'/wp-admin/edit.php?post_status=draft">'
                 .'View drafts</a>.</p></div>';
        } else if (isset($_GET['success'])) { 
            echo '<div id="posthasteSuccessNotice">'
                 .'<p>Post published.'
                 .'</p></div>';
        }
        global $current_user;
        $user = get_userdata($current_user->ID);
        $nickname = esc_attr($user->nickname);
        ?>
            <form id="new-post" name="new-post" method="post" action="">
            <input type="hidden" name="action" value="post" />
            <?php wp_nonce_field( 'new-post' ); ?>
            
            <?php if ($options['gravatar'] == "on" || $options['greeting and links'] == "on") { ?>
            <div id="posthasteIntro">

            <?php if ($options['gravatar'] == "on" && function_exists('get_avatar') ) {
                    global $current_user;
                    echo get_avatar($current_user->ID, 40); } ?>

            <?php if ($options['greeting and links'] == "on") { ?>
            <p>Hello, <?php echo $nickname; ?>! (<?php wp_loginout(); ?>)</p>
            <?php } ?>

            </div>
            <?php } ?>

            <?php if ($options['title'] == "on") { ?>
            <label for="postTitle" id="titleLabel"></label>
            <input type="text" name="postTitle" id="postTitle" placeholder="Title (optional)" tabindex="1" />
            <label for="postContent" id="contentLabel"></label>
            <?php } ?>
            <?php
            $content = '';
            $editor_id = 'postText';
            $settings = array( 'media_buttons' => true, 'tabindex' => 2 );

            wp_editor( $content, $editor_id, $settings );

            ?><br>
            <?php if ($options['tags'] == "on") { ?>
            <label for="tags" id="tagsLabel"></label>
            <input type="text" name="tags" id="tags" placeholder="Comma separated tags" tabindex="3" />
            <?php } else {
                $tagselect = posthasteTagCheck();
                echo '<input checked="checked" type="hidden" value="'
                      .$tagselect.'" name="tags" id="tags">';
            } ?>
            <div class="narwhalclear"></div>
            <?php 
            if ($options['categories'] == "on") { 
    			echo '<label for="cats" id="catsLabel"></label> ';
                $catselect = posthasteCatCheck();
                wp_dropdown_categories( array(
                    'hide_empty' => 0,
                    'name' => 'catsdd',
                    'orderby' => 'name',
                    'class' => 'catSelection',
                    'hierarchical' => 1,
                    'selected' => $catselect,
                    'tab_index' => 3
                    )
                ); 
            } else {
                $catselect = posthasteCatCheck();
                echo '<input checked="checked" type="hidden" value="'
                      .$catselect.'" name="catsdd" id="catsdd">';
            } ?>


            <?php if ($options['draft'] == "on") { ?>
            <input type="checkbox" name="postStatus" value="draft" id="postStatus">
            <label for="postStatus" id="postStatusLabel">Draft</label><br>
            <?php } ?>
            <input checked="checked" type="hidden" value="<?php echo $_SERVER['REQUEST_URI']; ?>" name="posthasteUrl" >
            
            <input id="submit" type="submit" value="Post" />

           
        </form>
        <?php
        echo '</div> <!-- close posthasteForm -->'."\n";
    }
}



// remove action if loop is in sidebar, i.e. recent posts widget
function removePosthasteInSidebar() {
    remove_action('loop_start', 'posthasteForm');
}



// add css
function addPosthasteStylesheet() {
    // for pre2.6, guess path to plugins
    if ( !defined('WP_PLUGIN_URL') ) {
        define( 'WP_PLUGIN_URL', get_option('siteurl') . '/wp-content/plugins');
    }
    // Set url to stylesheet
    $pluginStyleURL = WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/style.css';

    // echo the stylesheet if user can publish posts
	if( current_user_can('publish_posts')) {
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen" href="'.$pluginStyleURL.'">'."\n";
	}
}

// Blatant copying from p2 here
function posthaste_ajax_tag_search() {
    global $wpdb;
    $s = $_GET['q'];
    if ( false !== strpos( $s, ',' ) ) {
        $s = explode( ',', $s );
        $s = $s[count( $s ) - 1];
    }
    $s = trim( $s );
    if ( strlen( $s ) < 2 )
        die; // require 2 chars for matching

    $results = $wpdb->get_col( "SELECT t.name 
        FROM $wpdb->term_taxonomy 
        AS tt INNER JOIN $wpdb->terms 
        AS t ON tt.term_id = t.term_id 
        WHERE tt.taxonomy = 'post_tag' AND t.name 
        LIKE ('%". like_escape( $wpdb->escape( $s )  ) . "%')" );
    echo join( $results, "\n" );
    exit;
}


/*
 * SETTINGS
 *
 * - 2.7 and up can modify these in Settings -> Writing -> Unicorn Microblog Settings
 *
 * - pre-2.7, the default options are added to the db properly,
 * but the user cannot change this. (well, they can modify the array in db manually...)
 *
 */

// add default fields to db if db is empty
function posthasteAddDefaultFields() {
    
    // fields that are on by default:
    $fields = array('title', 'tags', 'categories', 'draft', 'greeting and links'); 

    // fill in options array with each field on
    $options = array();
    foreach($fields as $field) {
        $options[$field] = "on";
    }

    // add the hidden value too
    $options['hidden'] = "on";

    // now add options to the db 
    add_option('posthaste_fields', $options, '', 'yes');
}


// Only load the next three functions if using 2.7 or higher:
global $wp_version;
if ($wp_version >= '2.7') {
    // add_settings_field
    function posthasteSettingsInit() {
        // add the section
        add_settings_section(
            'posthaste_settings_section', 
            'Narwhal Settings', 
            'posthasteSettingsSectionCallback', 
            'writing'
        );

        // add 'display on' option
        add_settings_field(
            'posthaste_display', 
            'Display Narwhal on...',
            'posthasteDisplayCallback',
            'writing',
            'posthaste_settings_section'
        );
        register_setting('writing','posthaste_display');

        // add fields selection
        add_settings_field(
            'posthaste_fields', 
            'Narwhal Elements',
            'posthasteFieldsCallback',
            'writing',
            'posthaste_settings_section'
        );
        register_setting('writing','posthaste_fields');
    }

    // callback with section description for new writing section
    function posthasteSettingsSectionCallback() {
        echo "<p>The settings below affect the behavior of the "
            ."<a href=\"https://wilcosky.com\">Narwhal Microblog</a> "
            ."plugin.</p>";
    }

    // prints the options form on writing page
    function posthasteFieldsCallback() {

        // fields you want in the form
        $fields = array('title', 'tags', 'categories','draft','gravatar', 'greeting and links'); 

        // get options (if empty, fill in defaults & then get options)
        if(!$options = get_option('posthaste_fields')) { 
            posthasteAddDefaultFields(); 
            $options = get_option('posthaste_fields');
        } 

        if (!empty($options)) {
            $options = get_option('posthaste_fields');
            echo "<fieldset>\n";
            foreach ($fields as $field) {
                // see if it should be checked or not
                unset($checked);
                if ($options[$field] == 'on') { $checked = ' checked="checked" ';}

                // print the checkbox
                $fieldname = "posthaste_fields[$field]";
                echo "<label for=\"$fieldname\">\n"
                    ."<input {$checked} name=\"$fieldname\" type=\"checkbox\" id=\"$fieldname\">\n"
                    ." ".ucfirst($field)."\n</label><br />\n";
            }
            // now the hidden input (stupid hack so "all off" will work, probably a better way)
            echo '<input checked="checked" type="hidden" value="on" '
                 .'name="posthaste_fields[hidden]" id="posthaste_fields[hidden]">';
            echo "</fieldset>";
        }
    }

    function posthasteDisplayCallback() {
        // get current values
        if(!$select = get_option('posthaste_display'))
            $select = 'front';

        $options = array(
                'front' => 'Front Page', 
                'archive' => 'Front and Archive Pages',
                'everywhere' => 'Everywhere',
                'catheader' => 'Single Category Page:'
            );

        $cats = get_categories(array(
                    'hide_empty' => 0,
                    'hierarchical' => 0
                ));

        foreach($cats as $cat){
            $options[$cat->cat_ID] = $cat->cat_name;
        }


        // build the dropdown menu
        echo '<select name="posthaste_display" id="posthaste_display">';

        foreach($options as $key=>$value) {
            if ($select == $key)
                $selected = ' selected="selected"';
            if ($key == 'catheader')
                $disabled = ' disabled="disabled"';
            echo "<option value=\"$key\"$selected$disabled>$value</option>\n";
            unset($selected,$disabled);
        }   

        echo '</select>';

    }
}



/************
 * ACTIONS 
 ************/
// add header content
add_action('get_header', 'posthasteHeader');
// add form at start of loop
add_action('loop_start', 'posthasteForm'); 
// don't display form in sidebar loop (i.e. 'recent posts')
add_action('get_sidebar', 'removePosthasteInSidebar');
// add the css
add_action('wp_head', 'addPosthasteStylesheet', 10);
// add options to "Writing" admin page in 2.7 and up
if ($wp_version >= '2.7') { add_action('admin_init', 'posthasteSettingsInit'); }
