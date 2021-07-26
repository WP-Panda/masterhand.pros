<script type="text/template" id="ae-company_page_profile-loop">
    <div class="project-content fre-freelancer-wrap">
        <# if ( button ) { #>
        <div class="fre-input-field" style="float:right;">
            <div class="checkline is-ajax">
                <input class="get-quote-company" name="company_checked" type="checkbox" data-id="{{= ID}}"
                       data-name="{{= post_title}}">
                <label for="company_checked"></label>
            </div>
        </div>
        <# } #>
        <a class="project-name">{{= post_title}}</a>
        <div class="reviews-rating-summary">
            <div class="review-rating-result" style="width: {{= percent}}%"></div>
        </div>
        <span class="company-item_rating">{{= raiting}}</span>
        <div class="fre-location">{{= str_location}}</div>
        <div class="project-list-desc">{{= adress}}</div>
        <div class="project-list-skill">
            <span class="fre-label">{{= str_cat}}</span>
        </div>
        <div class="project-list-info project-list-adres">
            <span class="company-item_phone">{{= phone}}</span>
            <span class="company-item_site">
                <a href="{{= site}}" rel="nofollow" target="_blank">Website</a>
            </span>
            <# if ( button ) { #>
            <span class="company-item_btn" data-id="{{= ID}}" data-name="{{= post_title}}">{{= button}}</span>
            <# } else { #>
            <span class="company-item_btn"><a href="/login" class="btn-get-quote-to-login">Get a Quote</a></span>
            <# } #>
        </div>
    </div>
</script>