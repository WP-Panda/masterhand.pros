{foreach $posts as $item}
	<tr class="">
		<td>{$item.ID}</td>
		<td>{$item.post_title}</td>
		<td>{$item.likes_users}</td>
		<td><a class="btn btn-primary btn-sm" href="{$item.guid}" target="_blank">{$lang.viewPost}</a></td>
		<td>
			{*{if intval($item.likes_users) > 0}*}
			{*<button class="btn btn-danger btn-sm" data-id="{$item.ID}" data-name="{$item.post_title}"*}
					{*onclick="mod.resetLikes(this)">{$lang.resetLikes}</button>*}
			{*{/if}*}
			<button class="btn btn-info btn-sm" data-id="{$item.ID}" data-name="{$item.post_title}"
					onclick="mod.moreDetail(this)">{$lang.readMore}</button>
		</td>
	</tr>
{/foreach}
