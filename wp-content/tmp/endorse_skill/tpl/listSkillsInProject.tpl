<ul>
{foreach $items as $skill}
<li class="item-list-skills">
    <span class="item-endorse-skill mode-endorse {if $skill.endorsed}endorsed{/if}"
          data-uid="{$userId}" data-skill="{$skill.id}"
    >{$skill.title}</span> <span class="endorse-skill" title="counts of endorsement">{$skill.endorse}</span>
</li>
{/foreach}
</ul>