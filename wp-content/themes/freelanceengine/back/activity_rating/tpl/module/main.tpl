{include '_head.tpl'}
<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="{$MODULE_URL}">
    <i class="fas fa-sync-alt"> {$lang.refresh}</i>
</a>
<form class="tab-content" onsubmit="return false" method="POST">
    <table class="table table-hover skill-table">
        <thead class="font-weight-bold">
        <td>{$lang.name}</td>
        <td colspan="2">{$lang.value}</td>
        </thead>
        <tbody id="list_config">
        <tr class="text-center">
            <td></td>
            <td>{$lang.freelancer}</td>
            <td>{$lang.employer}</td>
        </tr>
        {include 'config.tpl'}
        </tbody>
    </table>
    <button class="btn btn-primary" type="submit" onclick="mod.saveConf(this.form)">{$lang.save}</button>
</form>

{include '_footer.tpl'}