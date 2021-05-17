<?php
if (!defined('WP_ADMIN') && !defined('LIKES_USERS_DIR')) die('LoL');

header('Cache-Control: no-cache; no-store; must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

ini_set('display_errors', 1);

$action = !empty($_REQUEST['action'])? $_REQUEST['action'] : 'index';

LikesUsers\Module::getInstance()->run($action);
exit;