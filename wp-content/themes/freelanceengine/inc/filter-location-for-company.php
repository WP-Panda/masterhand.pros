<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23.03.2020
 * Time: 23:44
 */

if (get_field('company_in_country')) {
    $getc = get_field('company_in_country');
    $cid = $wpdb->get_var("SELECT `id` FROM `wp_location_countries` WHERE `name` = '$getc' ");
} else {
    $cid = 0;
}
$results_countries = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->prefix}location_countries ORDER BY `name`", OBJECT);
$results_states = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->prefix}location_states WHERE `country_id` = {$cid} ORDER BY `name`", OBJECT);
?>
<div class="location-fields">
    <div class="fre-input-field col-md-12">
        <label for="country" class="fre-field-title"><?php _e('Location', ET_DOMAIN); ?></label>
    </div>
    <div class="col-md-4">
        <div class="fre-input-field">
            <select name="country" id="country" data-selected_id="<?= !empty($cid) ? $cid : '' ?>">
                <option value="">Select Country</option>
                <?php
                if ($results_countries) {
                    foreach ($results_countries as $country_item) {
                        $selected = (!empty($cid) && $country_item->id === $cid) ? ' selected' : '';
                        echo "<option value='$country_item->id' $selected>$country_item->name</option>";
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
            <select name="state" id="state" data-selected_id="">
                <option value="">Select State</option>
                <?php
                if ($results_states) {
                    foreach ($results_states as $state_item) {
                        echo "<option value='$state_item->id'>$state_item->name</option>";
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
            <select name="city" id="city" data-selected_id="">
                <option value="">Select state first</option>
            </select>
        </div>
    </div>
</div>
