<?php
if(version_compare('1.1', ReviewsRating\Base::VERSION, '=')) {
	$result = $module->db->query("
	CREATE TABLE IF NOT EXISTS `{$module->db->prefix}payments_gate` (
		`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
		`trz_id` VARCHAR(250) NULL DEFAULT '',
		`amount` DECIMAL(14,4) NULL DEFAULT NULL,
		`currency` VARCHAR(3) NULL DEFAULT NULL,
		`status` VARCHAR(20) NULL DEFAULT '',
		`type` VARCHAR(20) NULL DEFAULT '',
		`type_order` VARCHAR(20) NULL DEFAULT '',
		`gateway` VARCHAR(50) NULL DEFAULT '',
		`created` TIMESTAMP NULL DEFAULT NULL,
		`updated` TIMESTAMP NULL DEFAULT NULL,
		`source_id` BIGINT(20) NULL DEFAULT '0',
		`parent_id` BIGINT(20) NULL DEFAULT '0',
		`additional_data` TEXT NULL DEFAULT '',
		PRIMARY KEY (`id`)
	)
	COLLATE='utf8_general_ci'
	ENGINE=InnoDB
	");

	if($result) {
		$module->updParamConfig('VERSION', ReviewsRating\Base::VERSION);
	}
}