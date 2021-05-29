<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	define( 'WPP_TEXT_DOMAIN', 'wpp' );

	function wpp_get_setting( $data, $setting ) {
		return ! empty ( $data ) && is_object( $data ) && ! empty( $data->{$setting} ) ? $data->{$setting} : '';
	}

	function wpp_setting( $data, $setting ) {
		echo wpp_get_setting( $data, $setting );
	}

	function wpp_pages_settins( $page = null ) {

		$options = [];

		$options[ 'page_know_how' ] = (object) [
			'pro_plenty_jobs_header' => __( 'You can post articles in KNOW-HOW section on the website. Get all benefits of it.', WPP_TEXT_DOMAIN ),
			'pro_plenty_jobs_text'   => __( 'Being a blogger in the past decade has meant a variety of off-jobs that keep your virtual diary afloat, and hope that your unique view on modern society is about to be recognized. That is, if the blog has been created on one of the online platforms, and not in your mind. It is indeed a daunting mountain to climb, especially if you are not yet confident that you’d be able to fill the website with more than ten pieces about the past ten years. Many popular private blogs run by individuals and companies nowadays have tens of hundreds posts ready to entertain the wayward reader. Now, that is an expensive affair to maintain, which is why most blogs remain unwritten.', WPP_TEXT_DOMAIN ),
			'pro_text_left'          => sprintf( '<h3>%s</h3>', __( 'Any Professional can publish a blog post ON MasterHand PRO platform.', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<p><b>%s</b></p>', __( 'The real experience to test the waters before you dive in, or another way to attract new appreciative customers through your heartfelt, relatable expertise – these are just some of the incentives to take that first step. Some of the others could be:', WPP_TEXT_DOMAIN ) ) .
			                            '<ul>' .
			                            sprintf( '<li>%s</li>', __( 'Creating a Know-How article that is relevant to your business and can be discreetly related back to your own profile;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'A permanent backlink following back to the website of interest within the article;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Increase of your profile domain authority by being published and connected on an established platform;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Increasing brand recognition;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Getting referral traffic back to your profile and business;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Promoting diversity on a public site;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Creating an interest in your profile;', WPP_TEXT_DOMAIN ) ) .
			                            sprintf( '<li>%s</li>', __( 'Single out your profile of many competitors;', WPP_TEXT_DOMAIN ) ) .
			                            '</ul>',
			'pro_sign_up_header' => __( 'The more you publish, the more people learn about your business. ', WPP_TEXT_DOMAIN ),
			'pro_sign_up_text' => sprintf('<p>%s</p>',__( 'Through our initiative, we hope to gather a community of people interested in large-scale exposure and personal connection. We hope that by publishing your article in Know-How section on the website, we can reach a wider audience within similar field and interest groups, and forge a link for the like-minded.', WPP_TEXT_DOMAIN )),
			'pro_text_right_side_header' => __( 'Invest in your brand awareness constantly. What to do if you need some help?', WPP_TEXT_DOMAIN ),
			'pro_text_right_side' => sprintf('<p>%s</p>',__( 'We strongly recommend investing in your brand awareness through posting articles related to your business and area of expertise in Know-How section of MasterHand PRO platform. You can create an article for any of Life Success, Lifehacks & Tips, Work & Service categories. It is totally fine if you hire a professional writer (copywriter, rewriter) to help you out with it. You can easily find many professional copywriters on MasterHand PRO platform.', WPP_TEXT_DOMAIN )),
			'pro_sign_up_bottom_header' => __( 'How to do it effectively?', WPP_TEXT_DOMAIN ),
			'pro_sign_up_bottom_text' =>  sprintf( '<p>%s</p>', __( 'Some 2-3 articles/posts per month would work very effectively to promote your business and support your brand with potential customers.', WPP_TEXT_DOMAIN ) ) .
			                              sprintf( '<p>%s</p>', __( 'You can upload your article and supporting pictures here:', WPP_TEXT_DOMAIN ) )

		];

		return empty( $page ) ? (object) $options : (object) $options[ $page ];

	}