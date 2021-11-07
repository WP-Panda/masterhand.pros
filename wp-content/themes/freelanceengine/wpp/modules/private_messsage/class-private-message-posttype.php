<?php
/**
 * Private message class
 */
class AE_Private_Message_Posttype extends AE_Posts
{
    public static $instance;

    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * The constructor
     *
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Tambh
     */
    public function __construct($post_type = '', $taxs = array() , $meta_data = array() , $localize = array()) {
        parent::__construct( 'ae_private_message', $taxs, $meta_data, $localize );
        $this->taxs = array();
        $this->meta = array(
            'from_user',
            'to_user',
            'last_sender',
            'send_date',
            'last_date',
            'is_conversation',
            'project_id',
            'project_name',
            'bid_id',
            'conversation_status',
            'archive_on_sender',
            'archive_on_receiver',
            'freelancer_latest_reply',
            'employer_latest_reply',
            FREELANCER_UNREAD,
            EMPLOYER_UNREAD

        );
        $this->convert = array(
            'post_parent',
            'post_title',
            'post_name',
            'post_content',
            'post_author',
            'post_status',
            'ID',
            'post_type',
            'comment_count',
            'guid',
            'from_user',
            'to_user',
            'last_sender',
            'send_date',
            'last_date',
            'is_conversation',
            'project_id',
            'project_name',
            'bid_id',
            'conversation_status',
            'archive_on_sender',
            'archive_on_receiver',
            'freelancer_latest_reply',
            'employer_latest_reply',
            'final_bid_asked',
            FREELANCER_UNREAD,
            EMPLOYER_UNREAD
        );
    }
    /**
      * Init for class AE_Private_Message
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function init()
    {
        $this->ae_register_post_type_private_message();
        $this->ae_private_message_custom_post_status();
    }
    /**
      * Register ae_private_message postype
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public  function ae_register_post_type_private_message(){
        $labels = array(
            'name' => __('Private message', ET_DOMAIN) ,
            'singular_name' => __('Private message', ET_DOMAIN) ,
            'add_new' => _x('Add New private message', 'ae_private_message', ET_DOMAIN) ,
            'add_new_item' => __('Add New private message', ET_DOMAIN) ,
            'edit_item' => __('Edit private message', ET_DOMAIN) ,
            'new_item' => __('New private message', ET_DOMAIN) ,
            'view_item' => __('View private message', ET_DOMAIN) ,
            'search_items' => __('Search Private messages', ET_DOMAIN) ,
            'not_found' => __('No private message found', ET_DOMAIN) ,
            'not_found_in_trash' => __('No private message found in Trash', ET_DOMAIN) ,
            'parent_item_colon' => __('Parent private message:', ET_DOMAIN) ,
            'menu_name' => __('Private message', ET_DOMAIN) ,
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'show_in_nav_menus' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => false,
            //'has_archive' => ae_get_option('ae_private_message_archive', 'ae_private_messages') ,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => array(
                'slug' => ae_get_option('ae_private_message_slug', 'ae_private_message')
            ) ,
            'capability_type' => 'post',
            'supports' => array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'trackbacks',
                'comments',
                'revisions',
                'page-attributes',
                'post-formats'
            )
        );
        register_post_type( 'ae_private_message', $args );
        global $ae_post_factory;
        $ae_post_factory->set( 'ae_private_message', new AE_Posts( 'ae_private_message', $this->taxs, $this->meta ));
    }
    /**
      * Register a new post status
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_custom_post_status(){
        register_post_status( 'unread', array(
            'label'                     => _x( 'Unread', ET_DOMAIN ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>' ),
        ) );
        register_post_status( 'archive', array(
            'label'                     => _x( 'Archive', ET_DOMAIN ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' ),
        ) );
    }
}