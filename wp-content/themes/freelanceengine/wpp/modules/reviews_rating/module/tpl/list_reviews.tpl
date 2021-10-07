{foreach $reviews as $review}
    <tr class="row-review {if $review.status == 'pending'}table-warning{elseif $review.status == 'not_approved'}table-secondary{/if}">
        <td>{$review.created}</td>
        <td>{$review.author_review} ({$review.username})</td>
        {*<td>{$review.title}</td>*}
        <td>{$review.vote}</td>
        <td>
            {$review.comment|truncate:50:"<a class='badge badge-secondary' href='{$MODULE_URL}&action=detailReview&rw={$review.id}'>{$lang.readMore}...</button>"}
        </td>
        <td>{$lang[$review.status]}</td>
        <td><a href="{$review.guid}" target="_blank">{$review.pagetitle}</a></td>
        <td>{*{$review.countAnswers}*}</td>
        <td>
            <div class="btn-group" role="group">
                <a class="btn btn-primary btn-sm mx-1" href="{$MODULE_URL}&action=detailReview&rw={$review.id}">{$lang.detail}</a>
                <button class="btn btn-danger btn-sm mx-1" onclick="mod.delReview({$review.id})">{$lang.delete}</button>
            </div>
        </td>
    </tr>
{/foreach}