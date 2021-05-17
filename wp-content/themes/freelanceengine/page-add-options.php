<?php
/**
 * Page Edit Project
 */
global $user_ID, $option_for_project;
get_header();
$post = '';

if (isset($_REQUEST['id'])) {
    $post = get_post($_REQUEST['id']);
    if ($post) {
        global $ae_post_factory;
        $post_object = $ae_post_factory->get($post->post_type);
        $post_convert = $post_object->convert($post);
        echo '<script type="data/json"  id="edit_postdata">' . json_encode($post_convert) . '</script>';
    }
}

$ae_pack = $ae_post_factory->get('pack');
$packs = $ae_pack->fetch('pack');
$pro_func = '';
foreach ($packs as $key => $package) {
    if (array_search($package->sku, $option_for_project) !== false) {
        unset($packs[$key]);
        $pro_func[] = $package;
    }
}
echo '<script type="data/json" id="pro_func">' . json_encode($pro_func) . '</script>';

$data_ex = get_post_meta($_REQUEST['id'], 'et_expired_date');

$max_days = (mktime(0, 0, 0, date('m', strtotime($data_ex[0])), date('d', strtotime($data_ex[0])), date('Y', strtotime($data_ex[0])))
        - mktime(0, 0, 0, date("m"), date("d"), date("Y"))) / 86400;
echo $max_days . '<br>';

foreach ($option_for_project as $value) {
    echo $value . '-' . $post_convert->$value . '-' . get_post_meta($_REQUEST['id'], 'et_' . $value)[0] . '<br>';
    if ($post_convert->$value==1)
        $opt[]=$value;
}

?>
<?php //foreach ($option_for_project as $value) {
//    if ($post_convert->$value == 1) { ?>

<?php //}} ?>
    <div class="fre-page-wrapper">
        <div class="fre-page-title">
            <div class="container">
                <h1><?php _e('Add options to project', ET_DOMAIN); ?></h1>
            </div>
        </div>
        <div class="fre-page-section">
            <div class="container" id="edit_project">
                <div id="fre-post-project-2 step-post" class="fre-post-project-step step-wrapper step-post active">
                    <div class="fre-post-project-box">
                        <form class="post" role="form" class="validateNumVal">
                            <div class="step-post-project" id="fre-post-project">
                                <h2><?php _e('Your Project Details', ET_DOMAIN); ?></h2>
                                <div class="fre-input-field">
                                    <label class="fre-field-title"
                                           for="fre-project-title"><?php _e('Your project title', ET_DOMAIN); ?></label>
                                    <input class="input-item text-field" id="fre-project-title" type="text"
                                           name="post_title">
                                </div>

                                <?php
                                $tab_properties = table_properties('employer', 'AND s.id = ' . get_user_pro_status($user_ID) . ' AND p.property_type <> 2 ');
                                ?>
                                <div id="pro_functions">
                                    ! написать сообщение до когда активна функция !
                                    <input type='hidden' name='days_active_project' value=''>
                                    <?php
                                    for ($key = 1; $key < count($tab_properties) - 1; $key++) {
                                        ?>
                                        <div class="fre-input-field">
                                            <div class="checkline">
                                                <input id="<?php echo $tab_properties[$key]['property_nickname'] ?>"
                                                       name="<?php echo $tab_properties[$key]['property_nickname'] ?>" type="checkbox"
                                                       value="1">
                                                <span><?php _e($tab_properties[$key][0], ET_DOMAIN) ?></span>
                                                <div class="<?php echo $tab_properties[$key]['property_nickname'] ?> tooltip_wp">
                                                    <i>?</i>
                                                    <div class="tip"></div>
                                                </div>
                                            </div>

                                            <input type="hidden" id="price_<?php echo $tab_properties[$key]['property_nickname'] ?>"
                                                   name="<?= $tab_properties[$key][1]; ?>" value="<?= $tab_properties[$key][1]; ?>">
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" id="options_name" value="">
                                    <input type="hidden" id="options_days" value="">
                                </div>
                                <?php
                                // Add hook: add more field
                                echo '<ul class="fre-custom-field">';
                                do_action('ae_submit_post_form', PROJECT, $post);
                                echo '</ul>';
                                ?>
                                <div class="fre-post-project-btn">
                                    <button class="fre-btn fre-post-project-next-btn fre-submit-btn"
                                            type="submit"><?php _e("Update", ET_DOMAIN); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        if ($('body').hasClass('page-template-page-add-options')) {
                   var postdata = <?php echo json_encode($opt) ?>;
                   $.each(postdata, function (key1, item1) {
                       var arr = $("#pro_functions input[type='checkbox']")
                       $.each(arr, function (key, item) {
                           if (item.id == item1) {
                               item.click()
                               item.disabled=true
                               $("label[for='"+item.id+"']")[0].style.cursor='default'
                               key1++
                           }
                       })
                   })
               }
    </script>
<?php
get_footer();