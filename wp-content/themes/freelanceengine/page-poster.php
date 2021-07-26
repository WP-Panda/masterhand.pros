<?php
$template = 'poster3';

ob_start();
get_poster( $template );
$body = ob_get_clean();
echo $body;

?>

<button class="generate" onclick="generate('<?php echo $template ?>')">generate</button>

<script>
    jQuery(function ($) {
        function generate() {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {template: <?php echo json_encode( $template ) ?>, action: 'gen'}
            }).done(function (msg) {
                if (msg !== '0') console.log(msg)
                else console.log(msg);
            });
        }
    });
</script>