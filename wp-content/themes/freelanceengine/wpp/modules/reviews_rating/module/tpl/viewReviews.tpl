{include '_head.tpl'}
<link href="{$PATH_INC}/css/jquery.datetimepicker.min.css" rel="stylesheet">
<script src="{$PATH_INC}/js/jquery.datetimepicker.full.js"></script>
<script>
$(document).ready(function() {

})
</script>

<div class="container-fluid">
    <div class="row">
        <a class="btn btn-primary btn-sm mx-1" href="{$MODULE_URL}">{$lang.back}</a>
        <a class="btn btn-primary btn-sm mx-1" href="{$.server.REQUEST_URI}">{$lang.refresh}</a>
    </div>
    <div class="row">
        <div class="col text-center">
            <h4>{$lang.view_user} (ID: {$user.id})</h4>
        </div>
    </div>


    </div>
</div>
{include '_footer.tpl'}