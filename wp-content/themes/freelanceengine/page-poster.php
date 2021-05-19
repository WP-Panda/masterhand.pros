<?php
	$template = 'poster3';

	ob_start();
	get_poster( $template );
	$body = ob_get_clean();
	echo $body;

?>

<button class="generate" onclick="generate('<?= $template ?>')">generate</button>
<script type="text/javascript"
        src="http://masterhand.loc/wp-content/themes/_for_plugins/js/jquery-2.0.js?ver=2.0"></script>

<script>
    function generate() {
        console.log('generate-' +<?= json_encode( $template ) ?>)

        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {template: <?= json_encode( $template ) ?>, action: 'gen'}
        }).done(function (msg) {
            if (msg !== '0') console.log(msg)
            else console.log(msg);
        });
    }
</script>