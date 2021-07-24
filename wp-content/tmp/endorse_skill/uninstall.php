<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

require_once __DIR__ . '/classes/AutoloadEndorseSkill.php';
AutoloadEndorseSkill::init();
Endorseskill\Module::getInstance()->uninstallTb();