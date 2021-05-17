<?php
function fre_register_company()
{
    $labels = array(
        'name' => _x('Company', 'post type general name'),
        'singular_name' => _x('Company', 'post type singular name'),
        'add_new' => null, //_x('Add New', 'author'),
        'add_new_item' => null, //__('Add New Ad'),
        'new_item' => null, //__('New Ad'),
        'edit_item' => __('Edit Company'),
        'all_items' => __('All Companies'),
        'view_item' => __('View Company'),
        'search_items' => __('Search Company'),
        'not_found' => __('No Company found'),
        'menu_name' => __('Companies')
    );
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'company'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'can_export' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail')
    );

    flush_rewrite_rules(false);
    register_post_type('company', $args);

    global $ae_post_factory;
    $ae_post_factory->set(COMPANY, new AE_Posts(COMPANY, array('project_category'), array(
        'country', 'state', 'city'
    )));
}

add_action('init', 'fre_register_company');
add_action('add_meta_boxes', 'masterhand_company_add_custom_box');


function masterhand_company_add_custom_box()
{
    $screens = array('company');
    add_meta_box('company_location', 'Parameters', 'company_display_meta_box_callback', $screens, '', 'high');
}

function company_display_meta_box_callback($post, $meta)
{

    $ar_fields = ['site', 'adress', 'email', 'phone', 'raiting', 'cat', 'sub', 'country', 'state', 'city'];

    echo '<div class="company-metabox">';
    foreach ($ar_fields as $k => $f) {
        $value = get_post_meta($post->ID, $f, 1);
        company_meta_box_get_field($f, $value);
    }
    echo '</div>';
}

function company_meta_box_get_field($name, $value)
{
    $field_name = "company_" . $name . "_field";
    global $wpdb;
    if (get_field('company_in_country')) {
        $getc = get_field('company_in_country');
        $cid = $wpdb->get_var("SELECT `id` FROM `wp_location_countries` WHERE `name` = '$getc' ");
    } else {
        $cid = 0;
    }
    // get list countries
    $results_countries = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->prefix}location_countries ORDER BY `name`", OBJECT);

    // get categories list
    $subcategory_project_selected = $category_project_selected = null;
    if (!empty($post_convert->tax_input['project_category'])) {
        foreach ($post_convert->tax_input['project_category'] as $key => $value) {
            $tax = $value;
            $sub = get_term($tax->term_id, 'project_category');
            $subcategory_project_selected[] = $sub->slug;
            if ($key == 0) {
                $cat = get_term($tax->parent, 'project_category');
                $category_project_selected = $cat->slug;
            }
        }
    }
    if ($name === 'cat'): ?>  <?php {

        $subcat = get_term_by('id', $value, 'project_category', ARRAY_A);
//        $name = $subcat['name'];
        $categories = get_terms('project_category');

    }
        ?>
    <div class="category_subcategory">
        <input type="hidden" value="<?php echo $value?>" class="_category" ">
        <div>
            <label for="">Select category</label>
            <div class="select_style"><?php ae_tax_dropdown('project_category', array(
                        'attr' => 'data-selected_slug="'.$category_project_selected.'"',
                        'show_option_all' => __("$value", ET_DOMAIN),
                        'class' => 'categories',
                        'hide_empty' => false,
                        'hierarchical' => false,
                        'id' => 'cat',
                        'value' => 'slug',
                        'parent' => 0,
                        'name' => $field_name,
                    )
                ); ?></div>
        </div>
    <?php
    elseif ($name === 'sub'):
        $subcat = get_term_by('id', $value, 'project_category', ARRAY_A);
//        $name = $subcat['name'];
        $categories = get_terms('project_category');
        ?>
        <div>
            <label for="sub">Sub</label>
            <select id="" class="load-sub sub js-example-basic-multiple" name="company_sub_field[]" multiple>
                <?php
                $subNames = [];
                $parent_category_id = $wpdb->get_var("SELECT `parent` FROM {$wpdb->prefix}term_taxonomy WHERE `term_id` = $value[0]");
                $subcategoriesId = $wpdb->get_results("SELECT `term_id` FROM {$wpdb->prefix}term_taxonomy WHERE `parent` = {$parent_category_id}", ARRAY_N);
                $sub_not_selected = array_diff(call_user_func_array('array_merge',$subcategoriesId), $value);

                foreach ($value as $item) {
                     $subNames[] = $wpdb->get_results("SELECT `term_id` ,`name` FROM {$wpdb->prefix}terms WHERE `term_id` = {$item}", ARRAY_A);
                 }
                 foreach ($subNames as $sub): ?>
                     <option selected="selected" value="<?php echo $sub[0]['term_id'] ?>"><?php echo $sub[0]['name']?></option>
                 <?php endforeach; ?>
                $sub_not_selected_value = [];
                <?php foreach ($sub_not_selected as $sub ) {
                    $sub_not_selected_value[] = $wpdb->get_results("SELECT `term_id` ,`name` FROM {$wpdb->prefix}terms WHERE `term_id` = {$sub}", ARRAY_A);
                }
                foreach ($sub_not_selected_value as $item): ?>
                    <option value="<?php echo $item[0]['term_id'] ?>"><?php echo $item[0]['name']?></option>
                <?php endforeach; ?>
            </select>

        </div>
        </div>
        <?php
    elseif ($name === 'country'): ?>
    <div class="country_state_city">
        <div>
            <div class="fre-input-field">
                <form method="post">
                    <label for="countries">Country</label>
                    <select class="countries" name="<?php echo $field_name ?>">
                        <option value="<?php echo $value ?>"><?php echo $value ?></option>
                        <?php foreach ($results_countries as $country) { ?>
                            <option name="" value="<?php echo $country->name; ?>"><?php echo $country->name; ?></option>
                        <?php } ?>
                    </select>
                </form>
            </div>
        </div>
      <?php
    elseif ($name === 'state'): ?>
    <div>
        <label for="state">State</label>
        <select name="<?php echo $field_name ?>" class="load-state states" id="">
            <option value="<?php echo $value ?>"><?php echo $value ?></option>
            <?php
            $countryId = $wpdb->get_var("SELECT `country_id` FROM {$wpdb->prefix}location_states WHERE `name` = '$value'");
            $states = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->prefix}location_states WHERE `country_id` = {$countryId} ORDER BY `name`", OBJECT);
            foreach ($states as $state): ?>
                <option value="<?php echo $state->name; ?>"><?php echo $state->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    elseif ($name === 'city'): ?>
    <div>
        <label for="city">City</label>
        <select name="<?php echo $field_name ?>" class="load-city" id="">
            <option value="<?php echo $value ?>"><?php echo $value ?></option>
            <?php
            $stateId = $wpdb->get_var("SELECT `state_id` FROM {$wpdb->prefix}location_cities WHERE `name` = '$value'");
            $cities = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->prefix}location_cities WHERE `state_id` = {$stateId} ORDER BY `name`", OBJECT);
            foreach ($cities as $city): ?>
                <option value="<?php echo $city->name; ?>"><?php echo $city->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
        </div>
    <?php
    elseif ($name === 'site'): ?>
        <div class="contact_info">
            <div>
            <label for="<?php echo $field_name ?>"><?php echo __($name) ?></label>
            <input type="text" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>"
                   value="<?php echo $value ?>" size="25"/>
             </div>
    <?php
            elseif ($name === 'adress'): ?>
            <div>
                <label for="<?php echo $field_name ?>"><?php echo __($name) ?></label>
                <input type="text" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>"
                       value="<?php echo $value ?>" size="25"/>
            </div>
               <?php
            elseif ($name === 'email'): ?>
            <div>
            <label for="<?php echo $field_name ?>"><?php echo __($name) ?></label>
                <input type="text" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>"
                       value="<?php echo $value ?>" size="25"/>
            </div>
                <?php
             elseif ($name === 'phone'): ?>
             <div>
              <label for="<?php echo $field_name ?>"><?php echo __($name) ?></label>
                <input type="text" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>"
                       value="<?php echo $value ?>" size="25"/>
             </div>
                 <?php
                 elseif ($name === 'raiting'): ?>
                 <div>
                     <label for="<?php echo $field_name ?>"><?php echo __($name) ?></label>
                     <input type="text" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>"
                            value="<?php echo $value ?>" size="25"/>
                 </div>
        </div>
 <?php
    endif;
}


class Fre_CompanyAction extends AE_PostAction
{
    function __construct($post_type = 'company')
    {
        $this->post_type = COMPANY;
        $this->add_ajax('ae-fetch-companies', 'fetch_post');

//        $this->add_action('pre_get_posts', 'pre_get_company');
        $this->add_action('wp_footer', 'render_template_js');

        $this->add_filter('ae_convert_company', 'convert_company');
        //TODO fix S query issue
        //$this->add_filter('posts_join', 'fre_join_post_company', 10, 2);
        //$this->add_filter('posts_search', 'fre_posts_search_company', 10, 2);
        //$this->add_filter('posts_orderby', 'fre_order_by_company', 10, 2);

        // file_put_contents(__DIR__.'/com.txt',json_encode($query,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\r\n",FILE_APPEND);
    }

    /**
     * Override filter_query_args for action fetch_post.
     */
    public function filter_query_args($query_args)
    {
        $query = $_REQUEST['query'];

//        if (isset($query['meta_key'])) {
//            $query_args['meta_key'] = $query['meta_key'];
//            if (isset($query['meta_value'])) {
//                $query_args['meta_value'] = $query['meta_value'];
//            }
//        }

        if (isset($query['country']) && $query['country'] != '') {
            $query_args['meta_query'][] = array(
                "key" => "country",
                "value" => (int)$query['country'],
                "type" => "numeric",
                "compare" => "=",
            );
            if (isset($query['state']) && $query['state'] != '') {
                $query_args['meta_query'][] = array(
                    "key" => "state",
                    "value" => (int)$query['state'],
                    "type" => "numeric",
                    "compare" => "=",
                );
                if (isset($query['city']) && $query['city'] != '') {
                    $query_args['meta_query'][] = array(
                        "key" => "city",
                        "value" => (int)$query['city'],
                        "type" => "numeric",
                        "compare" => "=",
                    );
                }
            }
        }

        // filter project by project category
        if (isset($query['cat']) && $query['cat'] != '') {
            $term = get_term_by('slug', $query['cat'], 'project_category', ARRAY_A);
            $cat_id = $term['term_id'];
            $query_args['meta_query'][] = array(
                "key" => "cat",
                "value" => (int)$cat_id,
                "type" => "numeric",
                "compare" => "=",
            );
            if (isset($query['sub']) && $query['sub'] != '') {
                $term = get_term_by('slug', $query['sub'], 'project_category', ARRAY_A);
                $cat_id = $term['term_id'];
                $query_args['meta_query'][] = array(
                    "key" => "sub",
                    "value" => (int)$cat_id,
                    "type" => "numeric",
                    "compare" => "=",
                );
            }
        }

        return apply_filters('fre_company_query_args', $query_args, $query);
    }

    /**
     * render js template for notification
     * @since 1.2
     * @author Dakachi
     */
    function render_template_js()
    {
        get_template_part('template-js/company', 'item');
    }

    function convert_company($result)
    {
        global $user_ID;

        $result->phone = !empty(get_post_meta($result->ID, 'phone', true)) ? get_post_meta($result->ID, 'phone', true) : '';
        $result->adress = !empty(get_post_meta($result->ID, 'adress', true)) ? get_post_meta($result->ID, 'adress', true) : '';
        $result->raiting = !empty(get_post_meta($result->ID, 'raiting', true)) ? get_post_meta($result->ID, 'raiting', true) : 0;
        $result->site = !empty(get_post_meta($result->ID, 'site', true)) ? get_post_meta($result->ID, 'site', true) : '';

        $rate = str_replace(',', '.', $result->raiting);
        $result->percent = $rate / 0.05;

        if (!empty(get_post_meta($result->ID, 'cat', true))) {
            $cat = get_post_meta($result->ID, 'cat', true);
            if (!empty(get_post_meta($result->ID, 'sub', true))) {
                $cat = get_post_meta($result->ID, 'sub', true);
            }
            $term = get_term_by('id', $cat, 'project_category', ARRAY_A);
            $result->str_cat = $term['name'];
        } else $result->str_cat = '';

        $country = !empty(get_post_meta($result->ID, 'country', true)) ? get_post_meta($result->ID, 'country', true) : '';
        $state = !empty(get_post_meta($result->ID, 'state', true)) ? get_post_meta($result->ID, 'state', true) : '';
        $city = !empty(get_post_meta($result->ID, 'city', true)) ? get_post_meta($result->ID, 'city', true) : '';

        $location = getLocation(0, array('country' => $country, 'state' => $state, 'city' => $city));
        if (!empty($location['country'])) {
            $str_location = array();
            foreach ($location as $key => $item) {
                if (!empty($item['name'])) {
                    $str_location[] = $item['name'];
                }
            }
            $str_location = !empty($str_location) ? implode(' - ', $str_location) : 'Error';
        } else {
            $str_location = '<i>' . __('No country information', ET_DOMAIN) . '</i>';
        }
        $result->str_location = $str_location;

        if (!empty(get_post_meta($result->ID, 'email', true)) && (userRole($user_ID) == EMPLOYER)) {
            $result->button = '<input class="btn-get-quote" type="button" value="' . __('Get a Quote', ET_DOMAIN) . '">';
        } else {
            $result->button = '';
        }

        return $result;
    }

    function fre_join_post_company($join, $query)
    {
        if (isset($_REQUEST['query']['s']) && $_REQUEST['query']['s'] != '' && $query->query_vars['post_type'] == COMPANY) {
            $join .= " INNER JOIN wp_postmeta AS mtadres ON (wp_posts.ID = mtadres.post_id) ";
            $join .= " INNER JOIN wp_postmeta AS mtphone ON (wp_posts.ID = mtphone.post_id) ";
            $join .= " INNER JOIN wp_postmeta AS mtsite ON (wp_posts.ID = mtsite.post_id) ";
        }
        return $join;
    }

    function fre_posts_search_company($post_search, $query)
    {
        if (isset($_REQUEST['query']['s']) && $_REQUEST['query']['s'] != '' && $query->query_vars['post_type'] == COMPANY) {
            $search = $_REQUEST['query']['s'];

            $company_search = " OR mtadres.meta_key = 'adress' AND mtadres.meta_value LIKE '%" . $search . "%' ";
            $company_search .= " OR mtphone.meta_key = 'phone' AND mtphone.meta_value LIKE '%" . $search . "%' ";
            $company_search .= " OR mtsite.meta_key = 'site' AND mtsite.meta_value LIKE '%" . $search . "%' ";

            $company_search .= " )) ";
            $post_search = str_replace('))', $company_search, $post_search);
        }
        return $post_search;
    }

    function fre_order_by_company($orderby, $query)
    {
        global $wpdb;
        if ($query->query_vars['post_type'] == COMPANY) {
            $orderby = "{$wpdb->posts}.ID DESC";
        }
        return $orderby;
    }
}

add_action('wp_ajax_getQuoteCom', 'sendMessageForQuoteCompany');
function sendMessageForQuoteCompany()
{
    global $wpdb, $user_ID;

    if (!empty($_POST['companyId']) && !empty(trim($_POST['message']))) {
        $companyId = [];
        if (is_array($_POST['companyId'])) {
            foreach ($_POST['companyId'] as $id) {
                $companyId[] = (int)$id;
            }
        } else {
            $companyId[] = (int)$_POST['companyId'];
        }

//        $register_status = get_user_meta($user_ID, 'register_status', 1);
//        if($register_status !== 'confirm'){
        if (!AE_Users::is_activate($user_ID)) {
            wp_send_json(['success' => false, 'msg' => __("Your email is not confirmed. Please confirm your email address in Settings.", ET_DOMAIN)]);
        }
        $userData = get_userdata($user_ID);

//        $sql = "SELECT p.post_title as company, pm.meta_value as email FROM {$wpdb->posts} p
        $sql = "SELECT pm.meta_value as email FROM {$wpdb->posts} p 
        LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'email'
        WHERE p.ID IN(" . implode(', ', $companyId) . ")";

        $rows = $wpdb->get_results($sql, ARRAY_A);

        if (empty($rows)) {
            wp_send_json(['success' => false, 'msg' => __("Error! Not found emails", ET_DOMAIN)]);
        }

        $listEmails = array_column($rows, 'email');
        $checkListEmails = [];
        foreach ($listEmails as $email) {
            if (strpos($email, ',') !== false) {
                $em = explode(',', $email);
                foreach ($em as $e) {
                    $checkListEmails[] = trim($e);
                }
            } else {
                $checkListEmails[] = trim($email);
            }
        }

        $subject = ae_get_option('get_quote_company_subject') ?: 'Get Quote Company';
        $display_name = !empty(trim($_POST['display_name'])) ? trim($_POST['display_name']) : $userData->display_name;

        $message = ae_get_option('get_quote_company');
        $message = str_replace('[message]', trim($_POST['message']), $message);
        $message = str_replace('[display_name]', $display_name, $message);
        $message = str_replace('[user_email]', $userData->user_email, $message);

        $headerFrom = "From: " . $display_name . " < " . $userData->user_email . "> \r\n";

        $mailing = AE_Mailing::get_instance();
        $result = $mailing->wp_mail($checkListEmails, $subject, $message, [], $headerFrom);

        if ($result) {
            wp_send_json(array(
                    'success' => true,
                    'msg' => __("Message was sent successfully", ET_DOMAIN)
                )
            );
        } else {
            wp_send_json(array(
                    'success' => false,
                    'msg' => __("Sending a message was unsuccessful", ET_DOMAIN)
                )
            );
        }
    }

    wp_send_json(['success' => false, 'msg' => __("Something  went wrong", ET_DOMAIN)]);
}


/**
 * @param object $project
 */
function requestQuoteCompany($project)
{
    global $wpdb, $user_ID;

//    file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', json_encode($project, JSON_PRETTY_PRINT), FILE_APPEND);
//    file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\n"
//        . json_encode(['city' => !empty($project->city), 'request_quote_company' => !empty($project->request_quote_company)], JSON_PRETTY_PRINT), FILE_APPEND);
    if (!empty($project->city) && !empty($project->request_quote_company)) {
        $category = [];
        if (!empty($project->tax_input['project_category'])) {
            foreach ($project->tax_input['project_category'] as $key => $value) {
                $category[] = $value->parent ? $value->parent : $value->term_id;
            }

            $category = array_unique($category);

            $sql = "SELECT DISTINCT pm.meta_value as email FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'email'
			LEFT JOIN {$wpdb->postmeta} pmct ON pmct.post_id = p.ID AND pmct.meta_key = 'city'
			LEFT JOIN {$wpdb->postmeta} pmcat ON pmcat.post_id = p.ID AND pmcat.meta_key = 'cat'
			WHERE p.post_type = '" . COMPANY . "' 
			AND CAST(pmcat.meta_value as UNSIGNED) IN (" . implode(',', $category) . ")
			AND CAST(pmct.meta_value as UNSIGNED) = " . (int)$project->city;

            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\n" . $sql, FILE_APPEND);

            $rows = $wpdb->get_results($sql, ARRAY_A);

//            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\n"
//                . json_encode($rows, JSON_PRETTY_PRINT), FILE_APPEND);

            if (empty($rows)) {
                return;
            }

            $listEmails = array_column($rows, 'email');

            $subject = ae_get_option('request_quote_company_subject') ?: 'Request Quote Company';

            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\n"
                . json_encode(['$listEmails' => $listEmails, '$subject' => $subject], JSON_PRETTY_PRINT), FILE_APPEND);

            $checkListEmails = [];
            foreach ($listEmails as $email) {
                if (strpos($email, ',') !== false) {
                    $em = explode(',', $email);
                    foreach ($em as $e) {
                        $checkListEmails[] = trim($e);
                    }
                } else {
                    $checkListEmails[] = trim($email);
                }
            }

            $userData = get_userdata($user_ID);

            $message = ae_get_option('request_quote_company');
            $message = str_replace('[project_link]', $project->permalink, $message);
            $message = str_replace('[project_name]', $project->post_title, $message);
            $message = str_replace('[display_name]', $userData->display_name, $message);
            $message = str_replace('[user_email]', $userData->user_email, $message);

            $headerFrom = "From: " . $userData->display_name . " < " . $userData->user_email . " > \r\n";

            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\nmessage:\n $message", FILE_APPEND);
            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\nheaderFrom:\n $headerFrom", FILE_APPEND);

            $mailing = AE_Mailing::get_instance();
            $result = $mailing->wp_mail($checkListEmails, $subject, $message, [], $headerFrom);
//            $result = [];
//            foreach ($listEmails as $email)
//                $result[] = $mailing->wp_mail($email, $subject, $message, [], $headerFrom);

            file_put_contents(dirname(__DIR__) . '/__resSendToCompany.log', "\n\n"
                . json_encode(['$result' => $result], JSON_PRETTY_PRINT), FILE_APPEND);
        }
    }
}
