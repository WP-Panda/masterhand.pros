<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23.01.2019
 * Time: 23:44
 */
global $user;

$location = getLocation($user_ID);
$results = $wpdb->get_results( "SELECT `id`, `name` FROM {$wpdb->prefix}location_countries ORDER BY `name`", OBJECT );
//get user role
$user_role = ae_user_role($user_ID);


?>
<div class="location-fields">
    <input type="hidden" name="change_country">
    <input type="hidden" name="change_state">
    <input type="hidden" name="change_city">
    <div class="fre-input-field col-md-12">
        <label for="country" class="fre-field-title"><?php _e( 'Location', ET_DOMAIN ); ?></label>
    </div>
    <div class="col-md-4">
        <div class="fre-input-field">
            <select name="country" id="country" data-selected_id="<?=!empty($location['country']['id']) ? $location['country']['id'] : ''?>">
                <option value="9999999">Select Country</option>
                <?php
                if ($results) {
                    //check user role (if FREELANCER hide other countries )
                    if( $user_role == FREELANCER){
                     foreach ($results as $result) {
//                        $selected = (!empty($location['country']['id']) && $result->id === $location['country']['id']) ? ' selected' : '';
                        if($location['country']['id'] == $result->id ){
                         echo "<option value='$result->id' >$result->name</option>";
                     } 

                 }
             }else{
                foreach ($results as $result) {
//                        $selected = (!empty($location['country']['id']) && $result->id === $location['country']['id']) ? ' selected' : '';
                 
                    echo "<option value='$result->id' >$result->name</option>";
                    

                }
            }

        } else {
            echo '<option value="">Country not available</option>';
        }
        ?>
    </select>
</div>
</div>
<div class="col-md-4">
    <div class="fre-input-field">
        <select name="state" id="state" data-selected_id="<?=!empty($location['state']['id']) ? $location['state']['id'] : ''?>">
            <option value="">Select country first</option>
        </select>
    </div>
</div>
<div class="col-md-4">
    <div class="fre-input-field">
        <select name="city" id="city" data-selected_id="<?=!empty($location['city']['id']) ? $location['city']['id'] : ''?>">
            <option value="">Select state first</option>
        </select>
    </div>
</div>
</div>
