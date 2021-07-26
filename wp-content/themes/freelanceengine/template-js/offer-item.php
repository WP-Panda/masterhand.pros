<script type="text/template" id="ae-offer-loop">
    <div class="project-content fre-freelancer-wrap">
        <a href="{{= permalink }}" class="project-name" title="{{= post_title }}">{{= post_title }}</a>
        <div class="project-list-info">
            <span class="fre-location">{{= str_location}}</span>
        </div>
        <div class="project-list-desc">
            <p>{{= post_content_trim}}</p>
        </div>
        <div class="">
			<? _e( 'Author' ); ?>:
            <a href="{{= post_author_url}}" class="offer_author">
                {{= display_name }}
            </a>
        </div>
    </div>
</script>