{include '_head.tpl'}
<script>
    $(document).ready(function(){
    })
</script>

<div class="container-fluid">
    <div class="row">
        <a class="btn btn-primary btn-sm mx-1" href="{$MODULE_URL}">{$lang.back}</a>
        <a class="btn btn-primary btn-sm mx-1" href="{$.server.REQUEST_URI}">{$lang.refresh}</a>
    </div>
    <div class="row w-100">
        <div class="col-sm-12 text-center">
            <h4>{$lang.reviewOnDoc} <a class="badge badge-info" href="{$doc.guid}" target="_blank">{$doc.post_title}</a></h4>{*viewReview*}
        </div>
    </div>
    <form class="row form-edit-review w-100" method="POST">
        <input type="hidden" name="rwId" value="{$review.id}">
        <div class="col-sm-12">
            <div class="review-rating-summary">
            <span class="review-rating-empty"></span>
            <span class="review-rating-result" style="width: {$review.vote * 20}%"></span>
            </div>
        </div>
        <div class="col-sm-12 review-doc">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect">{$lang.statusReview}</label>
                </div>
                <select id="inputGroupSelect" name="status" class="custom-select" style="height: auto">
                        <option value=""></option>
                    {foreach $listStatuses as $status}
                        {if $status == $review.status}
                        <option value="{$status}" selected>{$lang[$status]}</option>
                        {else}
                        <option value="{$status}" >{$lang[$status]}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-sm-12 review-username">
            <label for="review_username" class="review-username-label">{$lang.username}</label>
            <input id="review_username" title="{$lang.username}" class="form-control content-review-username"
                   type="text" name="username" value="{$review.username}" maxlength="100" disabled>
        </div>
        <div class="col-sm-12 review-title">
            <label for="review_title" class="review-title-label">{$lang.title}</label>
            <input id="review_title" title="{$lang.title}" class="form-control content-review-title"
                   type="text" name="title" value="{$review.title}" maxlength="100" disabled>
        </div>
        <div class="col-sm-12 review-comment">
            <div class="review-comment-label">{$lang.review}</div>
            <textarea class="col-sm-12 form-control content-review-comment" name="comment"
                      placeholder="{$lang.textReview}" title="{$lang.textReview}" disabled>{$review.comment}</textarea>
        </div>
        <div class="reviews-response-message"></div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-success btn-md m-3 edit-review">{$lang.save}</button>
            <a class="btn btn-primary btn-md m-3" href="{$MODULE_URL}">{$lang.cancel}</a>
        </div>
    </form>
</div>
{include '_footer.tpl'}