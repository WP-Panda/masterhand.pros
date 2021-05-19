<?php
/**
 * Function to generate Rate US html content
 *
 * @package Orion SMS OTP verification
 */

if ( ! function_exists( 'ihs_get_rate_us_content' ) ) {
	function ihs_get_rate_us_content() {
		$url = esc_url( 'https://imransayed.com/orion/rate-us/' );
		$content = '<div class="d-block text-right mt-3">
			<a href="' . $url . '" target="_blank">
				<div class="ihs-rating-stars">
					<i class="fas fa-star"></i>
					<i class="fas fa-star"></i>
					<i class="fas fa-star"></i>
					<i class="fas fa-star"></i>
					<i class="fas fa-star"></i>
				</div>
				<br>
				<div class="ihs-rate-us">Rate Us Now !</div>
			</a>
		</div>';
		return $content;
	}
}