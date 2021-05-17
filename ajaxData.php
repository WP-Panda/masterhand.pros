<?php
//Include the database configuration file
include 'dbConfig.php';
header('Content-type: text/html; charset=utf-8');
if (!empty($_POST["country_id"])) {
    //Fetch all state data
    $query = $db->query("SELECT * FROM _regions WHERE country_id = " . $_POST['country_id'] . " ORDER BY title_en ASC");

    //Count total number of rows
    $rowCount = $query->num_rows;

    //State option list
    if ($rowCount > 0) {
//        echo
        $state_list='<option value="">Select state</option>';
        while ($row = $query->fetch_assoc()) {
            if (!empty($_POST['state_id'])) {
                if ($row['region_id'] == $_POST['state_id']) {
//                    echo
                    $state_list.='<option value="' . $row['region_id'] . '" selected>' . $row['title_en'] . '</option>';
                }
            }
//            echo
            $state_list.='<option value="' . $row['region_id'] . '">' . htmlspecialchars($row['title_en']) . '</option>';
        }
    } else {
//        echo
        $state_list='<option value="">State not available</option>';
    }
    if(empty($_POST["state_id"])) echo $state_list;
    else $list['state']=$state_list;
}
if (!empty($_POST["state_id"])) {
    //Fetch all city data
    $query = $db->query("SELECT * FROM _cities WHERE region_id = " . $_POST["state_id"] . " ORDER BY title_en ASC");

    //Count total number of rows
    $rowCount = $query->num_rows;

    //City option list
    if ($rowCount > 0) {
//        echo
        $city_list='<option value="">Select city</option>';
        while ($row = $query->fetch_assoc()) {
            if (!empty($_POST['city_id'])) {
                if ($row['city_id'] == $_POST['city_id']) {
//                    echo
                    $city_list.='<option value="' . $row['city_id'] . '" selected>' . $row['title_en'] . '</option>';
                }
            }
//            echo
            $city_list.='<option value="' . $row['city_id'] . '">' . $row['title_en'] . '</option>';
        }
    } else {
//        echo
        $city_list='<option value="">City not available</option>';
    }
    if(empty($list)) echo $city_list;
    else $list['city']=$city_list;
}
if(!empty($list)) {
    echo json_encode($list);
}
