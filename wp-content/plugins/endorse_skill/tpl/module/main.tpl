{include '_head.tpl'}
<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="{$MODULE_URL}">
    <i class="fas fa-sync-alt"> {$lang.refresh}</i>
</a>
<div class="tab-content" id="tabs">

    <div class="row">
        {*<form method="POST" class="form-create-skill input-group col-md-4">*}
            {*<input class="form-control" type="text" id="value_skill" name="skill" placeholder="{$lang.newSkill}...">*}
            {*<div class="input-group-append">*}
                {*<select id="group_skill" name="group_skill" style="width: 120px">*}
                    {*<option value="freelancer">{$lang.freelancer}</option>*}
                    {*<option value="employer">{$lang.employer}</option>*}
                {*</select>*}
                {*<button class="btn btn-default create-skill" type="button">{$lang.create}</button>*}
            {*</div>*}
        {*</form>*}
        <div class="input-group col-md-4 col-md-offset-4">
            <input class="form-control" type="text" id="search_field" placeholder="{$lang.search}...">
            <div class="input-group-append">
                <button class="btn btn-info run-search">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-secondary reset-search">
                    {$lang.reset}
                </button>
            </div>
        </div>
    </div>
    <table class="table table-hover skill-table">
        <thead class="font-weight-bold">
            <td>{$lang.id}</td>
            <td>{$lang.group}</td>
            <td>{$lang.name}</td>
            <td>{$lang.countEndorsed}</td>{*<td>{$lang.countUsed}</td>*}
            <td class="text-center">{$lang.action}</td>
        </thead>
        <tbody id="list_skill">
        {include 'list_skill.tpl'}
        </tbody>
        <tfoot class="pagination-skills">
        <tr>
            <td class="pagination-skills" colspan="5">
            {$pagination}
            </td>
        </tr>
        </tfoot>
    </table>
</div>

{include '_footer.tpl'}