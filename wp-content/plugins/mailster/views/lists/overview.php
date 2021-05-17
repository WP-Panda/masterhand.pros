<div class="wrap">
<h1><?php esc_html_e( 'Lists', 'mailster' ); ?>
<?php if ( current_user_can( 'mailster_add_lists' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_lists&new" class="page-title-action"><?php esc_html_e( 'Add New', 'mailster' ); ?></a>
<?php endif; ?>
<?php if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) : ?>
	<span class="subtitle"><?php printf( esc_html__( 'Search result for %s', 'mailster' ), '&quot;' . esc_html( stripslashes( $_GET['s'] ) ) . '&quot;' ); ?></span>
	<?php endif; ?>
</h1>
<?php

require_once MAILSTER_DIR . 'classes/lists.table.class.php';
$table = new Mailster_Lists_Table();

$table->prepare_items();
$table->search_box( esc_html__( 'Search Lists', 'mailster' ), 's' );
$table->views();
?>
<form method="post" action="" id="lists-overview-form">
<?php
$table->display();
?>
</form>
</div>
