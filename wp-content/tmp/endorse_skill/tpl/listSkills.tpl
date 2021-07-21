<ul>
{foreach $items as $skill}
<li class="item-list-skills">
    {if $modeEndorse}
    <span class="item-endorse-skill mode-endorse {if $skill.endorsed}endorsed{/if}"
          data-uid="{$userId}" data-skill="{$skill.id}"
    >{$skill.title}</span> <span class="endorse-skill" title="counts of endorsement">{$skill.endorse}</span>
    {else}
    <span class="item-endorse-skill">{$skill.title}</span> <span class="endorse-skill" title="counts of endorsement">{$skill.endorse}</span>
    {/if}
</li>
{/foreach}
</ul>