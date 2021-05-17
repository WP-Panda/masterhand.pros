<?php
    global $wpdb;
    $results = $wpdb->get_results( "SELECT `id`, `name` FROM {$wpdb->prefix}location_countries ORDER BY `name`", OBJECT );
?>
<div class="location-fields">
    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
        <label class="fre-field-title"><?php _e('Country', ET_DOMAIN) ?></label>
        <select name="country" id="country" data-selected_id="<?=!empty($location['country']['id']) ? $location['country']['id'] : ''?>">
            <option value="">Select Country</option>
            <?php
            if ($results) {
                foreach ($results as $result) {
                    $selected = (!empty($location['country']['id']) && $result->id === $location['country']['id']) ? ' selected' : '';
                    echo "<option value='$result->id' $selected>$result->name</option>";
                }
            } else {
                echo '<option value="">Country not available</option>';
            }
            ?>
        </select>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
        <label class="fre-field-title"><?php _e('Province', ET_DOMAIN) ?></label>
        <select name="state" id="state" data-selected_id="<?=!empty($location['state']['id']) ? $location['state']['id'] : ''?>">
            <option value="">Select country first</option>
        </select>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
        <label class="fre-field-title"><?php _e('City', ET_DOMAIN) ?></label>
        <select name="city" id="city" data-selected_id="<?=!empty($location['city']['id']) ? $location['city']['id'] : ''?>">
            <option value="">Select state first</option>
        </select>
    </div>
</div>    