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
        while ($row = $query->fetch_assoc()) {
            if (!empty($_POST['state_id'])) {
                if ($row['region_id'] == $_POST['state_id']) {
//                    echo
                    $state_list.='<li><a name=' . $row['region_id'] . '" selected>' . $row['title_en'] . '</a></li>';
                }
            }
//            echo
            $state_list.='<li><a name="' . $row['region_id'] . '">' . htmlspecialchars($row['title_en']) . '</a></li>';
        }
    } else {
//        echo
        $state_list='<li><a name="">State not available</a></li>';
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
        while ($row = $query->fetch_assoc()) {
            if (!empty($_POST['city_id'])) {
                if ($row['city_id'] == $_POST['city_id']) {
//                    echo
                    $city_list.='<li><a name="' . $row['city_id'] . '" selected>' . $row['title_en'] . '</a></li>';
                }
            }
//            echo
            $city_list.='<li><a name="' . $row['city_id'] . '">' . $row['title_en'] . '</a></li>';
        }
    } else {
//        echo
        $city_list='<li><a name="">City not available</a></li>';
    }
    if(empty($list)) echo $city_list;
    else $list['city']=$city_list;
}
if(!empty($list)) {
    echo json_encode($list);
}
