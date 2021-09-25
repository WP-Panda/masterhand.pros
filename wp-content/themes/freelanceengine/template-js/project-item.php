<script type="text/template" id="ae-project-loop">

    <div class="project-content fre-freelancer-wrap" {{=str_highlight_project }}>

        <a href="{{= permalink }}" class="project-name" title="{{= post_title }}">{{= post_title }}
            <!--new2-->
            {{= str_create_project_for_all }} {{= str_priority_in_list_project }} {{= str_urgent_project }} {{= str_hidden_project }}
            <!--new2-->
        </a>

        <div class="project-list-info">
            <span class="project-posted"><?php _e( 'Posted on:', ET_DOMAIN ); ?> {{= post_date }}</span>
            <span class="fre-location">{{= str_location}}</span>
            <span class="project-bid">{{= text_total_bid}}</span>
            <span class="free-hourly-rate">{{= budget}}</span>
        </div>
        <div class="project-list-desc">
            <p>{{= post_content_trim}}</p>
        </div>
        <div class="project-list-skill">{{= project_categories}}</div>
    </div>

</script>