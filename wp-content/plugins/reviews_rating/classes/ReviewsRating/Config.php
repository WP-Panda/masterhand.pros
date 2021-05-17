<?php
namespace ReviewsRating;

class Config
{
	protected static $_instance = null;

	public static $list = [
		'email_moderator',
		'send_notice',
		'page_step',
		'new_review_publish',
		'percent_pay_review',
		'min_pay_review',
		'VERSION',
	];

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}