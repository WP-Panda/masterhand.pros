<?php
	$template = 'banner1';

	get_banner( $template );

	$path = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'SERVER_NAME' ];
?>

<button class="generate" onclick="generate('<?= $template ?>')">generate</button>


<script type="text/javascript" src="<?= $path ?>/wp-content/plugins/generate_banner/js/html2canvas.js"></script>
<script type="text/javascript" src="<?= $path ?>/wp-content/themes/_for_plugins/js/jquery-2.0.js?ver=2.0"></script>

<input id="btn-Preview-Image" type="button" value="Preview"/>
<a id="btn-Convert-Html2Image" href="#">Download</a>
<br/>
<h3>Preview :</h3>
<div id="previewImage">
</div>


<script>
    var element = $(".wrapper"); // global variable
    var getCanvas; // global variable

    $("#btn-Preview-Image").on('click', function () {
        html2canvas(element, {
            onrendered: function (canvas) {
                $("#previewImage").append(canvas);
                getCanvas = canvas;
            }
        });
    });

    $("#btn-Convert-Html2Image").on('click', function () {
        var imgageData = getCanvas.toDataURL("image/png");
        // Now browser starts downloading it instead of just showing it
        var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
        $("#btn-Convert-Html2Image").attr("download", "your_pic_name.png").attr("href", newData);
    });
</script>