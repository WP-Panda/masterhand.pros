<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

require_once __DIR__ . '/classes/AutoloadReviews.php';
AutoloadReviews::init();
ReviewsRating\Module::getInstance()->uninstallTb();