{foreach $config as $item => $value}
<tr>
    <td>{$lang[$item]}</td>
    {if is_array($value)}
        <td class="text-center">
            {if $value[0]}
                {set $keyField = key($value[0])}
                <input type="text" class="" name="{$keyField}" value="{$value[0][$keyField]}">
            {/if}
        </td>
        <td class="text-center">
            {if $value[1]}
                {set $keyField = key($value[1])}
                <input type="text" class="" name="{$keyField}" value="{$value[1][$keyField]}">
            {/if}
        </td>
    {else}
        <td colspan="2" class="text-center"><input type="text" class="" name="{$item}" value="{$value}"></td>
    {/if}
</tr>
{/foreach}