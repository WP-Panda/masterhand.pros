<?php
//Database credentials
/*$dbHost     = 'localhost';
$dbUsername = 'masterha_wp100';
$dbPassword = 'zs$uqiW0#5qD';
$dbName     = 'masterha_wp100';*/

//Connect and select the database
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

/* изменение набора символов на utf8 */
if (!$db->set_charset("utf8")) {
  //  printf("Ошибка при загрузке набора символов utf8: %s\n", $db->error);
    exit();
} else {
   // printf("Текущий набор символов: %s\n", $db->character_set_name());
}

?>