<?php
global $user_ID;
if ( is_user_logged_in() ) {
	?>
    <!-- NOTIFICATION -->
    <div class="notification-fullscreen" id="notification_container">
        <div class="container">
            <div class="row">
                <!-- projects container -->
                <div class="col-md-12 freelancer-list-container">
                    <h4 class="notification-header">
						<?php _e( "Your Notification", ET_DOMAIN ); ?>
                    </h4>
					<?php wpp_user_notification( $user_ID ); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- NOTIFICATION / END -->
<?php }