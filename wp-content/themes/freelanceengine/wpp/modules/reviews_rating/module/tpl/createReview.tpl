{include '_head.tpl'}

<script>
    $(document).ready(function(){
        $('#searchDoc').select2({
            allowClear: false,
            placeholder: '{$lang.placeholderSearchDoc}',
            data: {$listDoc|json_encode}
        });

    })
</script>

<div class="container-fluid">
    <div class="row">
        <a class="btn btn-primary btn-sm mx-1" href="{$MODULE_URL}">{$lang.back}</a>
        <a class="btn btn-primary btn-sm mx-1" href="{$.server.REQUEST_URI}">{$lang.refresh}</a>
    </div>
    <div class="row w-100">
        <div class="col-sm-12 text-center">
            <h4>{$lang.creatingReview}</h4>
        </div>
    </div>
    <form class="row form-create-review w-100" method="POST">
        <div class="col-sm-12 text-center review-select-vote">
            <input id="review_rating1" type="radio" name="vote" value="1" class="review-vote">
            <label for="review_rating1" class="review-star-vote rating-1"></label>

            <input id="review_rating2" type="radio" name="vote" value="2" class="review-vote">
            <label for="review_rating2" class="review-star-vote rating-2"></label>

            <input id="review_rating3" type="radio" name="vote" value="3" class="review-vote">
            <label for="review_rating3" class="review-star-vote rating-3"></label>

            <input id="review_rating4" type="radio" name="vote" value="4" class="review-vote">
            <label for="review_rating4" class="review-star-vote rating-4"></label>

            <input id="review_rating5" type="radio" name="vote" value="5" class="review-vote">
            <label for="review_rating5" class="review-star-vote rating-5"></label>
        </div>
        <div class="col-sm-12 review-doc">
            <select name="doc_id" id="searchDoc" class="form-control">
                <option value="" selected></option>
            </select>
        </div>
        <div class="col-sm-12 review-username">
            <label for="review_username" class="review-username-label">{$lang.username}</label>
            <input id="review_username" title="{$lang.username}" class="form-control content-review-username"
                   type="text" name="username" value="" maxlength="100">
        </div>
        <div class="col-sm-12 review-title">
            <label for="review_title" class="review-title-label">{$lang.title}</label>
            <input id="review_title" title="{$lang.title}" class="form-control content-review-title"
                   type="text" name="title" value="" maxlength="100">
        </div>
        <div class="col-sm-12 review-comment">
            <div class="review-comment-label">{$lang.review}</div>
            <textarea class="col-sm-12 form-control content-review-comment" name="comment"
                      placeholder="{$lang.textReview}" title="{$lang.textReview}"></textarea>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-success btn-md m-3 review-submit">{$lang.submit}</button>
            <a class="btn btn-primary btn-md m-3" href="{$MODULE_URL}">{$lang.cancel}</a>
        </div>
    </form>
</div>
{include '_footer.tpl'}