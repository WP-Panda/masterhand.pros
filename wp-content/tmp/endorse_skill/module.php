<?php
if (!defined('WP_ADMIN') && !defined('ENDORSE_SKILL_DIR')) die('LoL');

header('Cache-Control: no-cache; no-store; must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

ini_set('display_errors', 1);

$action = !empty($_REQUEST['action'])? $_REQUEST['action'] : 'index';

EndorseSkill\Module::getInstance()->run($action);
exit;
