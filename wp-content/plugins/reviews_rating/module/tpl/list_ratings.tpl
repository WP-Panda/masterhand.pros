{foreach $ratings as $rating}
	<tr class="">
		<td>{$rating.user_id}</td>
		<td>{$rating.pagetitle}</td>
		<td>{$rating.votes}</td>
		<td>{$rating.rating}</td>
		<td>{$rating.countReviews}</td>
		<td><a class="btn btn-primary btn-sm" href="{$rating.guid}" target="_blank">{$lang.viewProfile}</a></td>
		<td><button class="btn btn-danger btn-sm" data-id="{$rating.doc_id}" data-name="{$rating.pagetitle}"
					onclick="mod.resetRating(this)">{$lang.reset}</button>
		</td>
	</tr>
{/foreach}
