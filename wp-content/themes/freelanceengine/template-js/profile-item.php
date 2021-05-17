<script type="text/template" id="ae-profile-loop">
<?php if (is_tax()) {?>
<div <?php post_class( 'fre-profiles-list-item cl-' . $current->ID);?> >
    <div class="profile-content fre-freelancer-wrap">
       <div class="row">
           <div class="col-sm-8 col-xs-12">
                <div class="fre-info">
<?php } else {?>
<div <?php post_class( 'fre-profiles-list-item cl-' . $current->ID);?> >
    <div class="profile-content fre-freelancer-wrap">
<?php } ?>   
        <a class="free-avatar" href="{{= author_link }}">{{= et_avatar}}</a>
        <div class="row">
            <div class="col-sm-8 col-xs-8">
                <div class="clearfix">
                    <a class="free-name" href="{{= author_link }}">
                        {{= author_name }} 
                    </a>
                    <span class="status">{{=str_pro_account}}</span>
                    {{=str_status}}
                </div>    
                <div class='free-hourly-rate'>{{= hourly_rate_price }}</div>   
            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="free-rating-new">+{{= activity_rating }}</div>
                <div class="free-rating">{{= reviews_rating }}</div>
            </div>
        </div>
        <div class="free-category">
         {{= profile_categories}} 
        </div>

        <div class="free-experience">
            <span>{{= experience }}</span>
            <span>{{= project_worked}}</span>
        </div>
        
         <?php if (is_tax()) {?>
                <div class="fre-location">{{= str_location}}</div>
            </div><!--fre-info-->    
            <?php $smcount0 = strlen($current->excerpt); 
                if ($smcount0 > 0 ) {?>
                <div class="profile-list-desc">
                    <?php if ($smcount0 > 270 ) {?>
                            <div class="excp-txt">{{= author_excp2}}</div>
                            <div class="scroll-pane">{{= excerpt}}</div>
                       <?php } else {?>
                          {{= excerpt}}
                       <?php }?>
                </div>     
             <?php }?>    
            </div><!--col-sm-8-->     
            <div class="col-sm-4 col-xs-12">
                {{= portfolio_img}}
                <a class="fre-submit-btn fre-view-profile" href="{{= author_link }}"><?php echo _e('View Profile', ET_DOMAIN);?></a>   
             </div>   
            </div><!--row--> 

        </div><!--profile-content-->
    </div>    
        <?php } else {?>
          <div class="profile-list-desc">
              {{= author_excp}}
          </div>      
    </div>
</div>
<?php } ?>    
</script>