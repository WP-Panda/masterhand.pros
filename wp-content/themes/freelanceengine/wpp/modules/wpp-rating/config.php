<?php
function wpp_rating_config() {
	$array = [];

	$array['messages'] = [
		'settings'   => __( 'Settings', WPP_TEXT_DOMAIN ),
		'back'       => __( 'Back', WPP_TEXT_DOMAIN ),
		'refresh'    => __( 'Refresh', WPP_TEXT_DOMAIN ),
		'title'      => __( 'Headline', WPP_TEXT_DOMAIN ),
		'newSkill'   => __( 'New skill', WPP_TEXT_DOMAIN ),
		'countUsed'  => __( 'Number of uses', WPP_TEXT_DOMAIN ),
		'readMore'   => __( 'more details', WPP_TEXT_DOMAIN ),
		'status'     => __( 'Status', WPP_TEXT_DOMAIN ),
		'apply'      => __( 'To apply', WPP_TEXT_DOMAIN ),
		'save'       => __( 'Save', WPP_TEXT_DOMAIN ),
		'edit'       => __( 'Edit', WPP_TEXT_DOMAIN ),
		'submit'     => __( 'Submit', WPP_TEXT_DOMAIN ),
		'cancel'     => __( 'Cancel', WPP_TEXT_DOMAIN ),
		'search'     => __( 'Search', WPP_TEXT_DOMAIN ),
		'reset'      => __( 'Reset', WPP_TEXT_DOMAIN ),
		'send'       => __( 'Submit', WPP_TEXT_DOMAIN ),
		'create'     => __( 'Create', WPP_TEXT_DOMAIN ),
		'toSet'      => __( 'Set', WPP_TEXT_DOMAIN ),
		'action'     => __( 'Act', WPP_TEXT_DOMAIN ),
		'editSkill'  => __( 'Edit skill', WPP_TEXT_DOMAIN ),
		'group'      => __( 'Group', WPP_TEXT_DOMAIN ),
		'name'       => __( 'Name', WPP_TEXT_DOMAIN ),
		'view'       => __( 'View', WPP_TEXT_DOMAIN ),
		'detail'     => __( 'More details', WPP_TEXT_DOMAIN ),
		'created'    => __( 'Created by', WPP_TEXT_DOMAIN ),
		'open'       => __( 'Open', WPP_TEXT_DOMAIN ),
		'delete'     => __( 'Delete', WPP_TEXT_DOMAIN ),
		'deleted'    => __( 'Deleted', WPP_TEXT_DOMAIN ),
		'error'      => __( 'Error!', WPP_TEXT_DOMAIN ),
		'freelancer' => __( 'Freelancer', WPP_TEXT_DOMAIN ),
		'employer'   => __( 'Employer', WPP_TEXT_DOMAIN )
	];

	$array['fields'] = [
		'coefficient_pro_status' => [ // для про стауса плана бизнесс  % от суммы
			'label' => __( 'Coef. rating growth from Business PRO status, %', WPP_TEXT_DOMAIN ),
			'for'   => 'all',
			'def'   => 50
		],

		'coefficient_premium_pro_status' => [ // для про стауса плана премиум % от суммы
			'label' => __( 'Coef. rating growth from Premium PRO status, %', WPP_TEXT_DOMAIN ),
			'for'   => 'all',
			'def'   => 100,
		],

		'coefficient_amount_payment'                  => [ // начисление баллов рейтинга за каждый потраченный доллар
			'label' => __( 'Purchases & money transactions (for 1$)', WPP_TEXT_DOMAIN ),
			'for'   => 'all',
			'def'   => 1
		],
		'site_visit'                                  => [  // за посещение сайта раз в сутки
			'label' => __( 'Site visits', WPP_TEXT_DOMAIN ),  //
			'for'   => 'all',
			'def'   => 5
		],
		'one_field_profile'                           => [ // за заполненные поля профиля
			'label' => __( 'Completed profile info', WPP_TEXT_DOMAIN ),
			'for'   => 'all',
			'def'   => 10
		],
		'freelancer_one_portfolio'                    => [
			'label' => __( 'For Number of Portfolio Jobs', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 10
		],
		'freelancer_as_referral'                      => [//
			'label' => __( 'As referral', WPP_TEXT_DOMAIN ), //
			'for'   => 'freelancer',
			'def'   => 500
		],
		'freelancer_as_referrer'                      => [//
			'label' => __( 'As referrer', WPP_TEXT_DOMAIN ), //
			'for'   => 'freelancer',
			'def'   => 2000
		],
		'freelancer_for_reward'                       => [
			'label' => __( 'Rewards', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 1000
		],
		'freelancer_for_review'                       => [ // за оставленный отзыв
			'label' => __( 'Reviews', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 50
		],
		'freelancer_for_skill'                        => [ //
			'label' => __( 'Skills', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 10
		],
		'freelancer_for_endorse_skill'                => [
			'label' => __( 'Approved skills', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 10
		],
		'freelancer_coefficient_from_rating_employer' => [
			'label' => __( 'Coef. from rating for a successfully closed project, %', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 10
		],
		'employer_coefficient_from_rating_freelancer' => [
			'label' => __( 'Coef. from rating for a successfully closed project, %', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 5
		],
		'freelancer_project_success'                  => [
			'label' => __( 'Successfully closed project (via SafePay Deal)', WPP_TEXT_DOMAIN ),
			'for'   => 'freelancer',
			'def'   => 0
		],
		'freelancer_installment_plan'                 => [
			'label'    => __( 'Trusted Partner program participation', WPP_TEXT_DOMAIN ),
			'for'      => 'freelancer',
			'def'      => 50,
			'disabled' => true
		],
		'employer_installment_plan'                   => [
			'label'    => __( 'Trusted Partner program participation', WPP_TEXT_DOMAIN ),
			'for'      => 'employer',
			'def'      => 50,
			'disabled' => true
		],
		'employer_as_referral'                        => [
			'label' => __( 'As referral', WPP_TEXT_DOMAIN ), //
			'for'   => 'employer',
			'def'   => 500
		],
		'employer_as_referrer'                        => [
			'label' => __( 'As referrer', WPP_TEXT_DOMAIN ), //
			'for'   => 'employer',
			'def'   => 1000
		],
		'employer_for_reward'                         => [
			'label' => __( 'Rewards', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 1000
		],
		'employer_for_review'                         => [ // за оставленный отзыв
			'label' => __( 'Reviews', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 50
		],
		'employer_project_success'                    => [
			'label' => __( 'Successfully closed project (via SafePay Deal)', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 0
		],
		'employer_bid_accepted'                       => [
			'label' => __( 'For the selected project PRO executor', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 300
		],
		'employer_for_skill'                          => [
			'label' => __( 'Skills', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 10
		],
		'employer_for_endorse_skill'                  => [
			'label' => __( 'Approved skills', WPP_TEXT_DOMAIN ),
			'for'   => 'employer',
			'def'   => 10
		]
	];

	$array['errors'] = [
		'name_skill_empty'  => __( 'Empty value', WPP_TEXT_DOMAIN ),
		'name_skill_exists' => __( 'Such a value exists', WPP_TEXT_DOMAIN ),
		'not_found_record'  => __( 'Database entry not found', WPP_TEXT_DOMAIN )
	];

	$array['js'] = [
		'yes'           => __( 'Yes', WPP_TEXT_DOMAIN ),
		'no'            => __( 'No', WPP_TEXT_DOMAIN ),
		'success'       => __( 'Success', WPP_TEXT_DOMAIN ),
		'error'         => __( 'Error!', WPP_TEXT_DOMAIN ),
		'delete_record' => __( 'Delete record', WPP_TEXT_DOMAIN ),
		'sure'          => __( 'Sure?', WPP_TEXT_DOMAIN )
	];

	return apply_filters( 'wpp_rating_setting', $array );

}