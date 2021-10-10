<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; ?>

<div class="col-sm-12 col-md-4 col-lg-4 col-xs-12">
	<div class="category">
		<?php
		if ( wpp_fre_is_freelancer() ) {

			$args = [
				[
					'url'   => '/business-promotion-with-know-how/',
					'ankor' => __( 'Business promotion with Know-How', WPP_TEXT_DOMAIN ),
					'text'  => __( 'Some 2-3 articles/posts per month would work very effectively to promote your business and support your brand with potential customers.', WPP_TEXT_DOMAIN ),
					'type'  => 2
				],
				[
					'url'   => '/pro-benefits-for-pro/',
					'ankor' => __( 'Pro benefits for Pro', WPP_TEXT_DOMAIN ),
					'text'  => __( 'You are a Trusted Professional. Choose and activate your PRO plan to get benefits from it.', WPP_TEXT_DOMAIN ),
					'type'  => 2
				],
				[
					'url'   => '/why-referals-are-very-important-pro-2/',
					'ankor' => __( 'Why referals are very important for PRO', WPP_TEXT_DOMAIN ),
					'text'  => __( 'You can be in TOP Professionals. Promote your business constantly. Share your profile via email, in social networks, and even offline.', WPP_TEXT_DOMAIN ),
					'type'  => 2
				]
			];

		} else {

			$args = [
				[
					'url'   => '/pro-benefits-for-client/',
					'ankor' => __( 'Pro benefits for Client', WPP_TEXT_DOMAIN ),
					'text'  => __( 'You are a Trusted Client. Choose and activate your PRO plan to get many benefits from it.', WPP_TEXT_DOMAIN ),
					'type'  => 1
				],
				[
					'url'   => '/why-referals-are-very-important-client/',
					'ankor' => __( 'Why referals are very important for Client', WPP_TEXT_DOMAIN ),
					'text'  => __( 'You can be a highly ranked Client- uphold your reputation and increase your rating constantly. Invite new referrals using special tools via email and social networks.', WPP_TEXT_DOMAIN ),
					'type'  => 1
				]
			];

		}

		foreach ( $args as $one ) {
			wpp_get_template_part( 'wpp/templates/profile/info-side-block', $one );
		} ?>

	</div>
</div>
