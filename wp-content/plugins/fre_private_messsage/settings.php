<?php
/**
 * Private message setting
 * @param Array $pages setting of payment gateways
 */

if( !function_exists('ae_plugin_menu_private_message' ) ){
    function ae_plugin_menu_private_message( $pages ){
        $sections = array();
        $options = AE_Options::get_instance();
        $sections = array(
            'args' => array(
                'title' => __("General setting", ET_DOMAIN) ,
                'id' => 'settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Time delay between two messages", ET_DOMAIN) ,
                        'id' => 'ae_private_message_time',
                        'class' => '',
                        'desc' => __("Set time delay between two messages sent by a user to different users.(second)", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'ae_private_message_time_delay',
                            'type' => 'text',
                            'title' => __("Time delay between two messages sent by users.", ET_DOMAIN) ,
                            'name' => 'ae_private_message_time_delay',
                            'placeholder' => __("eg:5", ET_DOMAIN) ,
                            'class' => 'positive_int',
                            'default'=> 0
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Time delay between two reply", ET_DOMAIN) ,
                        'id' => 'ae_private_message_reply_time',
                        'class' => '',
                        'desc' => __("Set time delay between two replies are sent by user.(second)", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'ae_private_message_reply_time_delay',
                            'type' => 'text',
                            'title' => __("Time delay between two reply are sent by users.", ET_DOMAIN) ,
                            'name' => 'ae_private_message_reply_time_delay',
                            'placeholder' => __("eg:15", ET_DOMAIN) ,
                            'class' => 'positive_int',
                            'default'=> 0
                        )
                    )
                ), // email time add from version 1.1.5
                array(
                    'args' => array(
                        'title' => __("Time delay between two emails", ET_DOMAIN) ,
                        'id'    => 'ae_private_message_email_time',
                        'class' => ' field-desc desc  ',
                        'desc'  => __("Set time delay between two emails are sent by system.(minutes).", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">'.__("The delay time(minutes) to send email between 2 messages.",ET_DOMAIN).'</div>',
                    ) ,

                    'fields' => array(
                        array(
                            'id'    => 'ae_private_message_email_time_delay',
                            'type'  => 'text',
                            'title' => __("Time delay between two emails are sent by system.", ET_DOMAIN) ,
                            'name'  => 'ae_private_message_email_time_delay',
                            'placeholder' => __("eg:15", ET_DOMAIN) ,
                            'class'     => 'positive_int',
                            'default'   => 15
                        )
                    )
                ), // ened email time
                array(
                    'args' => array(
                        'title' => __("Mail Template", ET_DOMAIN) ,
                        'id' => 'private-message-mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates for new messsage. You can use placeholders to include some specific content.", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [user_login],[display_name],[user_email] : ' . __("user's details you want to send mail", ET_DOMAIN) . '<br />
                                                    [dashboard] : ' . __("member dashboard url ", ET_DOMAIN) . '<br />
                                                    [title], [link], [excerpt],[desc], [author] : ' . __("project title, link, details, author", ET_DOMAIN) . ' <br />
                                                    [activate_url] : ' . __("activate link is require for user to renew their pass", ET_DOMAIN) . ' <br />
                                                    [site_url],[blogname],[admin_email] : ' . __(" site info, admin email", ET_DOMAIN) . '
                                                    [project_list] : ' . __("list projects employer send to freelancer when invite him to join", ET_DOMAIN) . '

                                                </div>',

                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Private Message Notification Mail Template", ET_DOMAIN) ,
                        'id' => 'ae-private-message-mail',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Send to user when he has new message.", ET_DOMAIN),
                        'toggle' => false
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_private_message_mail_template',
                            'type' => 'editor',
                            'title' => __("Private message notification Mail", ET_DOMAIN) ,
                            'name' => 'ae_private_message_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                )
            )
        );


        $temp = new AE_section($sections['args'], $sections['groups'], $options);

        $orderlist = new AE_container(array(
            'class' => 'field-settings',
            'id' => 'private-message-settings',
        ) , $temp, $options);

        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Private Message', ET_DOMAIN) ,
                'menu_title' => __('PRIVATE MESSAGE', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'ae-private-message',
                'icon' => 'M',
                'desc' => __("Bridging the gap between Employers and Freelancers", ET_DOMAIN)
            ) ,
            'container' => $orderlist
        );
        return $pages;
    }
}
add_filter('ae_admin_menu_pages', 'ae_plugin_menu_private_message');
/**
  * add default template to setting page
  * @param array $default
  * @return array $default
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function ae_private_message_default_option( $default ){
    $default = wp_parse_args( array( 'ae_private_message_mail_template'=> '<p>Hello [display_name],</p><p>You have a new message from [from_user] on [blogname]. </p><p> Message: [private_message]</p><p> You can view your message via the link: [message_link]</p>'), $default);
    return $default;
}
add_filter( 'fre_default_setting_option', 'ae_private_message_default_option' );