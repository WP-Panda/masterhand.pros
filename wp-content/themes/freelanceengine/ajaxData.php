<?php
//Include the database configuration file
include 'dbConfig.php';
header('Content-type: text/html; charset=utf-8');

/*if(!empty($_POST["country_id"])){
    //Fetch all state data
    $query = $db->query("SELECT * FROM _regions WHERE country_id = ".$_POST['country_id']." ORDER BY title_en ASC");
    
    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //State option list
    if($rowCount > 0){
        echo '<option value="">Select state</option>';
        while($row = $query->fetch_assoc()){ 
            echo '<option value="'.$row['region_id'].'">'.$row['title_en'].'</option>';
        }
    }else{
        echo '<option value="">State not available</option>';
    }
} elseif (!empty($_POST["state_id"])) {
    //Fetch all city data
    $query = $db->query('SELECT * FROM `_cities` WHERE region_id = "'.$_POST["state_id"].'" ORDER BY title_en ASC');

    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //City option list
    if($rowCount > 0){
        echo '<option value="">Select city</option>';
        while($row = $query->fetch_assoc()){ 
            echo '<option value="'.$row['city_id'].'">'.$row['title_en'].'</option>';
        }
    }else{
        echo '<option value="">City not available</option>';
    }
}  */




if(!empty($_POST["country_id2"])){
      $vv = $_POST["country_id2"];
	  
        if ($vv != '') {            
		$blogusers = get_users( array( 'meta_key' => 'country' ) );
		foreach ( $blogusers as $user ) {
			echo '<option>' . esc_html( $user->user_email ) . '</option>';
		}	
            //echo '<option value="'. $vv .'">'. $vv .'</option>';
        } else {
             echo '<option value=" ">No authors</option>';    
        }
    } else {
        echo '<option value=" ">No authors2</option>';
    }
?>