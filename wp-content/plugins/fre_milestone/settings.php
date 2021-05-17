<?php
/**
 * AE Milestone admin settings
 */

$update_mail_template = '<p>Hello Freelancer,</p>'.
                         '<p>[employer] has updated project [project]. Please check it out.</p>'.
                         '<p>Sincerely,<br />[blogname]';
define( 'UPDATE_MAIL', $update_mail_template );

// Define resolve mail template
$resolve_mail_template = '<p>Hello [employer],</p>'.
                         '<p>[freelancer] has resolved milestone "[milestone_name]" of project [project].</p>'.
                         '<p>Sincerely,<br />[blogname]';
define( 'RESOLVE_MAIL', $resolve_mail_template );

// Define reopen mail template
$reopen_mail_template = '<p>Hello [freelancer],</p>'.
                        '<p>[employer] has re-opened milestone "[milestone_name]" of project [project].</p>'.
                        '<p>Sincerely,<br />[blogname]</p>';
define( 'REOPEN_MAIL', $reopen_mail_template );

// Define close mail template
$close_mail_template =  '<p>Hello [freelancer],</p>'.
                        '<p>[employer] has closed milestone "[milestone_name]" of project [project].</p>'.
                        '<p>Sincerely,<br />[blogname]</p>';
define( 'CLOSE_MAIL', $close_mail_template );

// // Define close mail template
// $create_mail_template =  '<p>Hello [freelancer],</p>'.
//                         '<p>[employer] has created milestone "[milestone_name]" on project [project].</p>'.
//                         '<p>Sincerely,<br />[blogname]</p>';
// define( 'CREATE_MAIL', $create_mail_template );

// // Define remove mail template
// $remove_mail_template =  '<p>Hello [freelancer],</p>'.
//                         '<p>[employer] has removed milestone "[milestone_name]" on project [project].</p>'.
//                         '<p>Sincerely,<br />[blogname]</p>';
// define( 'REMOVE_MAIL', $remove_mail_template );

/**
 * Add default option for milestone setting
 * @param object $default_setting
 * @return object $default_setting
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_default_setting_option' ) ) {
    function ae_milestone_default_setting_option( $default_setting ) {

        $default_setting = wp_parse_args( array(
            'ae_create_milestone_mail_template' => '',
            'ae_remove_milestone_mail_template' => '',
            'ae_resolve_milestone_mail_template' => RESOLVE_MAIL,
            'ae_reopen_milestone_mail_template' => REOPEN_MAIL,
            'ae_close_milestone_mail_template' => CLOSE_MAIL,
            'ae_update_milestone_mail_template' => UPDATE_MAIL,
            // 'ae_create_milestone_mail_template' => CREATE_MAIL,
            // 'ae_remove_milestone_mail_template' => REMOVE_MAIL,

        ), $default_setting );

        return $default_setting;
    }
}

add_filter( 'fre_default_setting_option', 'ae_milestone_default_setting_option' );

/**
 * Add setting options for milestone plugin
 * @param object $pages
 * @return object $pages
 *
 * @since 1.0
 * @package FREELANCEENGINE
 * @category MILESTONE
 * @author tatthien
 */
if( !function_exists( 'ae_milestone_admin_settings' ) ) {
    function ae_milestone_admin_settings( $pages ) {
        $sections = array();
        $options = AE_Options::get_instance();

        $sections[] = array(
            'args' => array(
                'title' => __( 'General settings', ET_DOMAIN ),
                'id' => 'ae-milestone-settings',
                'icon' => 'y',
                'class' => ''
            ),
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Maximum number of milestones", ET_DOMAIN) ,
                        'id' => 'max_milestone',
                        'class' => '',
                        'desc' => __("Set up the number of milestones per project", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'max_milestone',
                            'type' => 'text',
                            'title' => __("Maximum number of milestone", ET_DOMAIN) ,
                            'name' => 'max_milestone',
                            'placeholder' => __("e.g. 5", ET_DOMAIN) ,
                            'class' => 'gt_zero',
                            'default'=> 5
                        )
                    )
                )
            ),
        );

        $sections[] = array(
            'args' => array(
                'title' => __( 'Mailing', ET_DOMAIN ),
                'id' => 'ae-milestone-mailing',
                'icon' => 'M',
                'class' => ''
            ),
            'groups' => array(
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
                            'text' => __("Email templates for updating, resolving, reopening and closing actions", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [freelancer] : ' . __( "Name of freelancer", ET_DOMAIN ) . '</br>
                                                    [employer] : ' . __( "Name of employer", ET_DOMAIN ) . '</br>
                                                    [milestone_name] : ' . __( "Name of milestone", ET_DOMAIN ) . '</br>
                                                    [project] : ' . __( "Name of project that is parent of milestone", ET_DOMAIN ) . '<br>
                                                    [blogname] : ' . __( "Site name", ET_DOMAIN ) . '<br>
                                                </div>',

                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,            
                array(
                    'args' => array(
                        'title' => __("Update Milestone Template", ET_DOMAIN) ,
                        'id' => 'ae-update-milestone-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send email to all bidders when employer updates milestone", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_update_milestone_mail_template',
                            'type' => 'editor',
                            'title' => __("Update Milestone Template", ET_DOMAIN) ,
                            'name' => 'ae_update_milestone_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Resolve Milestone Template", ET_DOMAIN) ,
                        'id' => 'ae-resolve-milestone-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send email to employer when freelancer resolves a milestone.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_resolve_milestone_mail_template',
                            'type' => 'editor',
                            'title' => __("Resolve Milestone Template", ET_DOMAIN) ,
                            'name' => 'ae_resolve_milestone_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'default' => '123'
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Reopen Milestone Template", ET_DOMAIN) ,
                        'id' => 'ae-reopen-milestone-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send email to freelancer when employer reopens a milestone.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_reopen_milestone_mail_template',
                            'type' => 'editor',
                            'title' => __("Reopen Milestone Template", ET_DOMAIN) ,
                            'name' => 'ae_reopen_milestone_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Close Milestone Template", ET_DOMAIN) ,
                        'id' => 'ae-close-milestone-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send email to freelancer when employer closes a milestone.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_close_milestone_mail_template',
                            'type' => 'editor',
                            'title' => __("Mark Done Milestone Template", ET_DOMAIN) ,
                            'name' => 'ae_close_milestone_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
            ),
        );

        $temp = array();

        foreach ( $sections as $key => $section ) {
            $temp[] = new AE_Section( $section['args'], $section['groups'], $options );
        }

        $container = new AE_Container( array(
            'class' => 'field-settings',
            'id' => 'settings'
        ), $temp, $options );

        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('FrE Milestone', ET_DOMAIN) ,
                'menu_title' => __('MILESTONE', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'ae-milestone',
                'icon' => 'M',
                // 'icon_class' => 'fa fa-check',
                'desc' => __("All settings for milestone plugin", ET_DOMAIN)
            ),
            'container' => $container
        );

        return $pages;
    }
}

add_filter( 'ae_admin_menu_pages', 'ae_milestone_admin_settings' );