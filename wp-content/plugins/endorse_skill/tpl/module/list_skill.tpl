{foreach $skills as $skill}
    <tr class="">
        <td>{$skill.id}</td>
        <td>{$skill.group_skill}</td>
        <td>{$skill.title}</td>
        <td>{$skill.used}</td>
        {*<td class="text-right">*}
            {*<button class="btn btn-info btn-sm" onclick="mod.openEdit({$skill.id})">{$lang.edit}</button>*}
        {*</td>*}
        <td class="text-center">
            <button class="btn btn-danger btn-sm" data-id="{$skill.id}" data-name="{$skill.title}" onclick="mod.delSkill(this)">
                {$lang.delete}
            </button>
        </td>
    </tr>
{/foreach}
