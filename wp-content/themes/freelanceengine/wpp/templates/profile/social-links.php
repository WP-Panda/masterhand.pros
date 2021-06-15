<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	extract( $args );

	$follows = [
		'facebook' => [ 'title' => 'Facebook', 'link' => true ],
		'skype'    => [ 'title' => 'Skype', 'link' => false ],
		'website'  => [ 'title' => 'Website', 'link' => false ],
		'viber'    => [ 'title' => 'Viber', 'link' => false ],
		'whatsapp' => [ 'title' => 'WhatsApp', 'link' => false ],
		'telegram' => [ 'title' => 'Telegram', 'link' => false ],
		'wechat'   => [ 'title' => 'WeChat', 'link' => false ],
		'linkedin' => [ 'title' => 'Linkedin', 'link' => true ]
	];

	$link = <<<LINK
            <p class="col-sm-6 col-xs-12"><span>%s:</span>
                <a href="%s" target="_blank" rel="nofollow">
					%s
                </a>
            </p>
LINK;

	$label = <<<LABEL
            <p class="col-sm-6 col-xs-12"><span>%s</span>
				%s
            </p>
LABEL;


?>
<div class="col-sm-12 col-md-6 col-lg-7 col-xs-12">

	<?php
		foreach ( $follows as $follow_id => $data ) {

			$flag = get_post_meta( $profile_id, $follow_id, true );

			if ( ! empty( $flag ) ) {

				if ( ! empty( $data[ 'link' ] ) ) {
					printf( $link, $data[ 'title' ], $flag, $flag );
				}

			} else {
				printf( $label, $data[ 'title' ], $flag );
			}
		}
	?>

</div>