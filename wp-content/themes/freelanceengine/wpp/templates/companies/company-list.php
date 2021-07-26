<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

if ( ! empty( $companies ) ) :
	$company_data = [];
	foreach ( $companies as $company ) :

		$postdata[] = (object) [
			"post_title" => $company->title,
			"post_name"  => sanitize_title( $company->title ),
			"ID"         => $company->id,
			"country"    => $company->country,
			"state"      => $company->state,
			"city"       => $company->city,
			"id"         => $company->id,
			'address'    => $company->address
		];

		wpp_get_template_part( 'wpp/templates/companies/single-company-item', [ 'company' => $company ] );

	endforeach;

	printf( '<script type="data/json" class="postdata" >%s</script>', json_encode( $postdata ) );

else:

	wpp_get_template_part( 'wpp/templates/universal/not-found-results' );

endif;