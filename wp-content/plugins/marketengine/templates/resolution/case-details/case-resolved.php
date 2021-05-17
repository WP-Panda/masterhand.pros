<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$winner = marketengine_get_message_meta($case->ID, '_case_winner', true);
$case_arbitrate = marketengine_get_message_meta($case->ID, '_case_arbitrate', true);

?>

<h4><?php printf(__("%s wins the dispute", "enginethemes"), get_the_author_meta('display_name', $winner)); ?></h4>
<p>
<?php _e("Admin arbitrates:", "enginethemes"); ?><br>
<?php 
echo esc_html( $case_arbitrate );
?>
</p>
