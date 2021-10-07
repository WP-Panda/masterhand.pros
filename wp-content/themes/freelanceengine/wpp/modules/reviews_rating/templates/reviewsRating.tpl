{if $votes}
<div class="tutor-page-info-rating reviews-doc-rating">
    <div class="reviews-doc-rating-summary" title="{$lang.rating}: {$rating}/ {$stars} ({$percent_rating}%)">
        <div class="review-rating-empty"></div>
        <div class="review-rating-result" style="width: {$percent_rating}%"></div>
    </div>
    <div class="tutor-page-info-rating_tx reviews-doc-rating-info">
        {*<span>{$lang.rating}: <span class="reviews-doc-rating-rating">{$rating}</span> / {$stars} (<span class="reviews-doc-rating-votes"><a href="javascript:$(document).scrollTop(document.querySelector('#reviews').scrollHeight)">{$lang.votes} {$votes}</a></span>)</span>*}
        <a href="javascript:$(document).scrollTop(document.querySelector('#reviews').scrollHeight)">{$votes} {$lang.docReviews}</a>
    </div>
</div>
{/if}