<div class="col-xs-12">
    <form class="col-xs-8 d-table" method="POST">
        {*<div class="d-table-row">*}
            {*<div class="d-table-cell">e-mail модератора</div>*}
            {*<div class="d-table-cell"><input name="email_moderator" value="{$config.email_moderator}" class=" form-control mx-3"></div>*}
        {*</div>*}
        <div class="d-table-row">
            <div class="d-table-cell">{$lang.confSendNotice}</div>
            <div class="d-table-cell p-3"><input type="checkbox" name="send_notice" value="1" {!$config.send_notice?: 'checked'}></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell">{$lang.confNewReviewPublish}</div>
            <div class="d-table-cell p-3"><input type="checkbox" name="new_review_publish" value="1" {!$config.new_review_publish?: 'checked'}></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell">{$lang.confPageStep}</div>
            <div class="d-table-cell"><input name="page_step" value="{$config.page_step}" class=" form-control mx-3"></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell">{$lang.confPercentPayReview}</div>
            <div class="d-table-cell"><input name="percent_pay_review" value="{$config.percent_pay_review}" class=" form-control mx-3"></div>
        </div>
        <div class="d-table-row">
            {set $ae_currency = $.call.ae_get_option('currency')}
            {set $currency = $ae_currency.code}
            <div class="d-table-cell">{$.call.sprintf($lang.confMinPayReview, $currency)}</div>
            <div class="d-table-cell"><input name="min_pay_review" value="{$config.min_pay_review}" class=" form-control mx-3"></div>
        </div>

        <div><button type="submit" class="btn btn-primary" onclick="mod.saveConf(this.form)">{$lang.save}</button></div>
    </form>
</div>
    {if $UPDATE}
        <form class="col-xs-12" action="" method="POST" style="margin:40px 0px 20px 0px">
            <input type="hidden" name="action" value="update">
            <button class="btn btn-warning btn-lg blink">{$lang.updateModule}</button>
        </form>
    {/if}

    {*<div class="col-xs-12" style="margin:20px 0px 20px 0px">*}
        {*<form method="POST">*}
            {*<input type="hidden" name="action" value="uninstall" />*}
            {*{ignore}<div class="btn btn-danger" onclick="if(confirm('Вы уверены?')){ this.parentNode.submit() }">Удалить БД модуля</div>{/ignore}*}
        {*</form>*}
    {*</div>*}