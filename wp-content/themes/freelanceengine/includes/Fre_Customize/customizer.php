<?php 

/**
 * Freelanceengine Customizer functionality
 * @author ThanhTu
 */
class Fre_Customizer extends AE_Base {
	function __construct(){
		$this->add_action('customize_register', 'fre_customize_register', 11);
		$this->add_action('customize_preview_init', 'fre_customize_js');
		$this->add_action('customize_save_after', 'ae_update_option', 10, 3);
	}
	/**
	 * register javascript 
	 * @author ThanhTu
	 */
	public function fre_customize_js() {
        wp_enqueue_script('fre-customizer',
            get_template_directory_uri() . '/includes/Fre_Customize/assets/fre_customizer.js', array('jquery', 'appengine', 'customize-preview'),
            ET_VERSION,
            true
        );
    }
    /**
	 * Update option ET 
	 * @author ThanhTu
	 */
    public function ae_update_option($wp_customize_manager){
    	// Sync site_logo
        $customize_site_logo = get_theme_mod('site_logo');
        $site_logo_id = attachment_url_to_postid($customize_site_logo);
        $attach_data = et_get_attachment_data($site_logo_id); 
        ae_update_option('site_logo', $attach_data);
    }
    /**
	 * Customize Register
	 * @author ThanhTu
	 */
	public function fre_customize_register($wp_customize){
		// Block Banner
		$wp_customize->add_panel("fre_panel", array(
			"title" => __("Title & Background", ET_DOMAIN),
			"priority" => 30,
			'capability'     => 'edit_theme_options',
		));
		$this->fre_customize_banner($wp_customize);
		$this->fre_customize_work($wp_customize);
		$this->fre_customize_freelance($wp_customize);
		$this->fre_customize_project($wp_customize);
		$this->fre_customize_story($wp_customize);
		$this->fre_customize_service($wp_customize);
		$this->fre_customize_start($wp_customize);

		$wp_customize->add_setting('site_logo', array(
			"transport" => "postMessage",
			'default'	=>  get_template_directory_uri()."/img/logo-fre.png"
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
	           $wp_customize,
	           'site_logo',
	           array(
	           		'control_id' => 'site_logo',
	               	'label'      => __('Site Logo', ET_DOMAIN ),
	               	'description' => __( 'Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.', ET_DOMAIN ),
	               	'section'    => 'title_tagline',
	               	'settings'   => 'site_logo',
                	'option_type' => 'theme_mod',
                	'field_type' => 'cropped_image',
	               	'width' => 150,
                	'height' => 50
	            )
	        )
		);
	}

	public function fre_customize_banner($wp_customize){
		// Block Banner
		$wp_customize->add_section("block_banner", array(
			"title" => __("Block Banner", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));

		$wp_customize->add_setting("desc_block_banner", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_banner",
			array(
				"description" => __("This block will display an image that is used as a banner at the top of your homepage.", ET_DOMAIN),
				"section" => "block_banner",
				"settings" => "desc_block_banner",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_banner", array(
			"default" => __("Find perfect local professionals", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_banner",
			array(
				"label" => __("Title", ET_DOMAIN),
				"section" => "block_banner",
				"settings" => "title_banner",
				"type" => "text",
			)
		));

           $wp_customize->add_setting("title_banner2", array(
			"default" => __("for your project", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_banner2",
			array(
				"label" => __("Subtitle", ET_DOMAIN),
				"section" => "block_banner",
				"settings" => "title_banner2",
				"type" => "text",
			)
		));
        
		$wp_customize->add_setting("background_banner", array(
			"default" => get_template_directory_uri().'/img/main_bg.jpg',
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
	           $wp_customize,
	           'background_banner',
	           array(
	               'label'      => __('Upload an image', ET_DOMAIN ),
	               'description' => __( 'Choose an image from your existing images in the media library or upload new ones. The min-height of the image must be greater than or equal to 623 pixel.', ET_DOMAIN ),
	               'section'    => 'block_banner',
	               'settings'   => 'background_banner',
	            )
	        )
		);
        
        $wp_customize->add_setting("background_banner_mob", array(
			"default" => get_template_directory_uri().'/img/main_bg_mob.jpg',
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
	           $wp_customize,
	           'background_banner_mob',
	           array(
	               'label'      => __('Upload an image for mob', ET_DOMAIN ),
	               'description' => __( 'Choose an image from your existing images in the media library or upload new ones. The min-height of the image must be greater than or equal to 623 pixel.', ET_DOMAIN ),
	               'section'    => 'block_banner',
	               'settings'   => 'background_banner_mob',
	            )
	        )
		);
        
        
        /**** ----------- end -----------*/
	}

	public function fre_customize_work($wp_customize){
		// Block How Work
		$wp_customize->add_section("block_work", array(
			"title" => __("Block How", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));
		$wp_customize->add_setting("desc_block_work", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_work",
			array(
				"description" => __("This block will give a quick explanation about main workflow in your website.", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "desc_block_work",
				'type' => 'hidden'
			)
		));

		// Title
		$wp_customize->add_setting("title_work", array(
             "default"=>__("YOU CAN <strong>SAVE UP TO 70%</strong><br> <strong>WITH MASTERHAND PRO</strong>.<br/> HOW IS IT POSSIBLE?", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_work",
			array(
				"label" => __("Title", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "title_work",
				"type" => "textarea",
			)
		));
        
        $wp_customize->add_setting("desc_work_4", array(
             "default"=> __("<p>Masterhand Pro is a service deals platform. Describe what work needs to be done. Masterhand Pro will invite trusted professionals near you to submit tenders.</p>
<p>Get qualified proposals with bid price. Quotes may start coming within several minutes.</p>
<p>Compare profiles, bids and rewiews to choose the best fit. Hire the Pro for your task.</p>", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_work_5",
			array(
				"label" => __("Description for left block", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "desc_work_4",
				"type" => "textarea",
			)
		));
        
        //background left
         $wp_customize->add_setting("img_back", array(
               "default"=> get_template_directory_uri() . '/img/main_bg2.jpg',
			"transport" => "postMessage",
		));
        $wp_customize->add_control(new WP_Customize_Upload_Control(
			$wp_customize,
			"img_back",
			array(
				"label" => __("Upload an image for background", ET_DOMAIN),
				"description" => __('Choose an image from your existing images in the media library or upload new ones.', ET_DOMAIN),
				"section" => "block_work",
				"settings" => "img_back",
			)
		));
        
            
        // List Item1
		
		$wp_customize->add_setting("img_work_1", array(
            "default"=> get_template_directory_uri() . '/img/work1.png',
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
			$wp_customize,
			"img_work_1",
			array(
				"label" => __("Upload an image for block 1", ET_DOMAIN),
				"description" => __('Choose an image from your existing images in the media library or upload new ones.', ET_DOMAIN),
				"section" => "block_work",
				"settings" => "img_work_1",
			)
		));
        
        $wp_customize->add_setting("title_work_1", array(
              "default"=> "FAVOURABLE PRICES",
				"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_work_1",
			array(
				"label" => __("Title for block 1", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "title_work_1",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("desc_work_1", array(
			"default" => __("With Masterhand Pro professionals save money on advertising. They compete for your project and offer you best prices.", ET_DOMAIN),
			"transport" => "postMessage",
		));
        
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_work_1",
			array(
				"label" => __("List Item1 text", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "desc_work_1",
				"type" => "textarea",
			)
		));
        
         // List Item2
		  $wp_customize->add_setting("img_work_2", array(
                "default"=> get_template_directory_uri() . '/img/work2.png',
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
			$wp_customize,
			"img_work_2",
			array(
				"label" => __("Upload an image for block 2", ET_DOMAIN),
				"description" => __('Choose an image from your existing images in the media library or upload new ones.', ET_DOMAIN),
				"section" => "block_work",
				"settings" => "img_work_2",
			)
		));
        
        $wp_customize->add_setting("title_work_2", array(
            "default" => __("VERIFIED PRO'S", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_work_2",
			array(
				"label" => __("Title for block 2", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "title_work_2",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("desc_work_2", array(
            "default" => __("All Masterhand Professionals have  an option to pass the verification procedure, we check the reviews, deal with complaints and monitor the quality of their work. You have access to their profiles and rewiews to compare and make the right choice.", ET_DOMAIN),
				"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_work_2",
			array(
				"label" => __("List Item2 text", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "desc_work_2",
				"type" => "textarea",
			)
		));
        
         // List Item3
        $wp_customize->add_setting("img_work_3", array(
              "default"=> get_template_directory_uri() . '/img/work3.png',
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Upload_Control(
			$wp_customize,
			"img_work_3",
			array(
				"label" => __("Upload an image for block 3", ET_DOMAIN),
				"description" => __('Choose an image from your existing images in the media library or upload new ones. ', ET_DOMAIN),
				"section" => "block_work",
				"settings" => "img_work_3",
			)
		));
        
        $wp_customize->add_setting("title_work_3", array(
             "default" => __("QUICK AND EASY", ET_DOMAIN),
				"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_work_3",
			array(
				"label" => __("Title for block 3", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "title_work_3",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("desc_work_3", array(
            "default" => __("On Masterhand Pro you can find a suitable professional in a few minutes. Many of them are ready to start working immediately.", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_work_3",
			array(
				"label" => __("List Item3 text", ET_DOMAIN),
				"section" => "block_work",
				"settings" => "desc_work_3",
				"type" => "textarea",
			)
		));
        
        
	}
    


	public function fre_customize_freelance($wp_customize){
		// Block Freelance
		$wp_customize->add_section("block_freelance", array(
			"title" => __("Block Freelance and categories on home", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));

        
        $wp_customize->add_setting("desc_block_cats", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_cats",
			array(
				"description" => __("This block will display categories to which freelancers belong.", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "desc_block_cats",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_prcat", array(
            "default"=>'More than 150000<br/> professionals.',
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_prcat",
			array(
				"label" => __("Title for categories at right corner", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "title_prcat",
				"type" => "textarea",
			)
		));
        
        $wp_customize->add_setting("title_ncat", array(
            "default"=>'150000',
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_ncat",
			array(
				"label" => __("Number of professionals", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "title_ncat",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("desc_block_freelance", array(
			"transport" => "postMessage",
		));
        
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_freelance",
			array(
				"description" => __("This block will display freelancers whose rating scores are highest.", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "desc_block_freelance",
				'type' => 'hidden'
			)
		));

        $wp_customize->add_setting("title_profbl", array(
            "default"=>'Professionals',
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_profbl",
			array(
				"label" => __("Title for block with professionals on homepage", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "title_profbl",
				"type" => "text",
			)
		));
        
         $wp_customize->add_setting("subtitle_profbl", array(
             "default"=>'Need perfect professionals for you projects.',
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"subtitle_profbl",
			array(
				"label" => __("Subitle for block with professionals", ET_DOMAIN),
				"section" => "block_freelance",
				"settings" => "subtitle_profbl",
				"type" => "text",
			)
		));
      
	}

	public function fre_customize_project($wp_customize){
		// Block Freelance
		$wp_customize->add_section("block_project", array(
			"title" => __("Block Project", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));

		$wp_customize->add_setting("desc_block_project", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_project",
			array(
				"description" => __("This block will display latest available projects.", ET_DOMAIN),
				"section" => "block_project",
				"settings" => "desc_block_project",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_project", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_project",
			array(
				"label" => __("Title", ET_DOMAIN),
				"section" => "block_project",
				"settings" => "title_project",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("subtitle_project", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"subtitle_project",
			array(
				"label" => __("Subtitle for block Jobs", ET_DOMAIN),
				"section" => "block_project",
				"settings" => "subtitle_project",
				"type" => "text",
			)
		));

	}

	public function fre_customize_story($wp_customize){
		// Block Stories
		$wp_customize->add_section("block_story", array(
			"title" => __("Block Testmemonials and Blog", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));
               
		$wp_customize->add_setting("desc_block_story", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_story",
			array(
				"description" => __("This block will display testimonials of your users.", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "desc_block_story",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_story", array(
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_story",
			array(
				"label" => __("Title fot block Testmemonials", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "title_story",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("subtitle_story", array(
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"subtitle_story",
			array(
				"label" => __("Subtitle", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "subtitle_story",
				"type" => "text",
			)
		));
        
        /*--blog--*/
        	$wp_customize->add_setting("desc_block_blog", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_blog",
			array(
				"description" => __("This block will display blog posts.", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "desc_block_blog",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_blog", array(
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_blog",
			array(
				"label" => __("Title fot block Testmemonials", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "title_blog",
				"type" => "text",
			)
		));
        
        $wp_customize->add_setting("subtitle_blog", array(
			"transport" => "postMessage",
            
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"subtitle_blog",
			array(
				"label" => __("Subtitle", ET_DOMAIN),
				"section" => "block_story",
				"settings" => "subtitle_blog",
				"type" => "text",
			)
		));

	}

	public function fre_customize_service($wp_customize){
		// Block Freelance
		$wp_customize->add_section("block_service", array(
			"title" => __("Block Service", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));

		$wp_customize->add_setting("desc_block_service", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_service",
			array(
				"description" => __("This block will display available package plans according to freelancer or employer role. If users are freelancers, bid packages are shown. Otherwise, packages for project posting are displayed.", ET_DOMAIN),
				"section" => "block_service",
				"settings" => "desc_block_service",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_service", array(
			"default" => __("Select the level of service you need for project posting", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_service",
			array(
				"label" => __("Title (Visitor/Employer)", ET_DOMAIN),
				"section" => "block_service",
				"settings" => "title_service",
				"type" => "text",
			)
		));

		$wp_customize->add_setting("title_service_freelancer", array(
			"default" => __("Select the level of service you need for project bidding", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_service_freelancer",
			array(
				"label" => __("Title (Freelancer)", ET_DOMAIN),
				"section" => "block_service",
				"settings" => "title_service_freelancer",
				"type" => "text",
			)
		));
	}

	public function fre_customize_start($wp_customize){
		// Block Freelance
		$wp_customize->add_section("block_start", array(
			"title" => __("Block Get Start", ET_DOMAIN),
			"priority" => 35,
			"panel"	=> 'fre_panel'
		));

		$wp_customize->add_setting("desc_block_start", array(
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"desc_block_start",
			array(
				"description" => __("This block allows you to set up a greeting sentence, displayed according to user's role.", ET_DOMAIN),
				"section" => "block_start",
				"settings" => "desc_block_start",
				'type' => 'hidden'
			)
		));

		$wp_customize->add_setting("title_start", array(
			"default" => __("Need work done? Join FreelanceEngine community!", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_start",
			array(
				"label" => __("Visitor", ET_DOMAIN),
				"section" => "block_start",
				"settings" => "title_start",
				"type" => "text",
			)
		));

		$wp_customize->add_setting("title_start_freelancer", array(
			"default" => __("It's time to start finding freelance jobs online!" , ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_start_freelancer",
			array(
				"label" => __("Freelancer", ET_DOMAIN),
				"section" => "block_start",
				"settings" => "title_start_freelancer",
				"type" => "text",
			)
		));

		$wp_customize->add_setting("title_start_employer", array(
			"default" => __("The best way to find perfect freelancers!", ET_DOMAIN),
			"transport" => "postMessage",
		));
		$wp_customize->add_control(new WP_Customize_Control(
			$wp_customize,
			"title_start_employer",
			array(
				"label" => __("Employer", ET_DOMAIN),
				"section" => "block_start",
				"settings" => "title_start_employer",
				"type" => "text",
			)
		));
	}
}
new Fre_Customizer();