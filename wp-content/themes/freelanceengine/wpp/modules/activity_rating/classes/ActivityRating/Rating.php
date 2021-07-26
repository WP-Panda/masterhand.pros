<?php

namespace ActivityRating;

class Rating extends Base {
	protected static $_instance = null;

	public static $defaultTimeZone = 'UTC';

	private $_dataAct = [
		'userId'         => 0,
		'type'           => '',
		'value'          => 0,
		'sourceId'       => 0,
		'additionalData' => '',
	];

	const ACTIVITY_amountPayment = 'amountPayment';
	const ACTIVITY_oneFieldProfile = 'oneFieldProfile';
	const ACTIVITY_siteVisit = 'siteVisit';
	const ACTIVITY_onePortfolio = 'onePortfolio';
	const ACTIVITY_asReferral = 'asReferral';
	const ACTIVITY_asReferrer = 'asReferrer';
	const ACTIVITY_forSkill = 'forSkill';
	const ACTIVITY_forEndorseSkill = 'forEndorseSkill';
	const ACTIVITY_forReview = 'forReview';
	const ACTIVITY_projectSuccess = 'projectSuccess';
	const ACTIVITY_bidAccepted = 'bidAccepted';
	const ACTIVITY_installmentPlan = 'installmentPlan';
	const ACTIVITY_forReward = 'forReward';

	public static function initActions() {
		add_action( 'init', [ self::getInstance(), 'doActionSiteVisit' ], 100 );
		//		add_action('after_setup_theme', [self::getInstance(), 'doActionSiteVisit'], 100);
		//
		add_action( 'activityRating_paymentEscrowProject', [
			self::getInstance(),
			'employerPaymentProject'
		], 1, 1 );
		add_action( 'activityRating_amountPayment', [ self::getInstance(), 'doActionAmountPayment' ], 1, 2 );

		add_action( 'activityRating_oneFieldProfile', [ self::getInstance(), 'doActionOneFieldProfile' ], 1 );
		add_action( 'activityRating_onePortfolio', [ self::getInstance(), 'doActionOnePortfolio' ], 1 );

		add_action( 'activityRating_asReferral', [ self::getInstance(), 'doActionAsReferral' ], 1 );
		add_action( 'activityRating_asReferrer', [ self::getInstance(), 'doActionAsReferrer' ], 1 );

		add_action( 'activityRating_forSkill', [ self::getInstance(), 'doActionForSkill' ], 1 );
		add_action( 'activityRating_forEndorseSkill', [ self::getInstance(), 'doActionForEndorseSkill' ], 1, 3 );
		add_action( 'activityRating_forReview', [ self::getInstance(), 'doActionForReview' ], 1, 2 );
		add_action( 'activityRating_projectSuccess', [ self::getInstance(), 'doActionProjectSuccess' ], 1, 2 );
		add_action( 'activityRating_projectSuccessFreelancer', [
			self::getInstance(),
			'doActionProjectSuccessFreelancer'
		], 1, 2 );

		add_action( 'init', [ self::getInstance(), 'session' ] );

		add_action( 'fre_accept_bid', [ self::getInstance(), 'doActionBidAccepted' ], 1 );
		//		add_action('activityRating_bidAccepted', [self::getInstance(), 'doActionBidAccepted'], 1, 2);

		//		add_action('activityRating_installmentPlan', [self::getInstance(), 'doActionInstallmentPlan'], 1, 2);
		//		add_action('activityRating_forReward', [self::getInstance(), 'doActionForReward'], 1, 2);
	}


	public function session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	public function doActionSiteVisit() {
		global $user_ID;
		if ( $user_ID ) {
			$currentDate = $this->getUTCDate();
			$addActivity = isset( $_SESSION['userActivityRating'] ) ? ( $_SESSION['userActivityRating'] !== $currentDate ) : true;
			if ( $addActivity ) {
				$lastUpdate = $this->getLastTypeActivity( $user_ID, self::ACTIVITY_siteVisit );
				$date       = explode( ' ', $lastUpdate['time'] );
				$lastDate   = trim( $date[0] );

				if ( $lastDate !== $currentDate ) {
					$this->logger->addLog( '__METHOD__', __METHOD__ );
					$this->dataActUid( $user_ID );
					$this->dataActType( self::ACTIVITY_siteVisit );
					$valueActivity = Config::getInstance()->getValSiteVisit();
					//$valueActivity = (userHaveProStatus($user_ID)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
					$valueActivity = ceil( $valueActivity );
					$valuePro      = ( userHaveProStatus( $user_ID ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
					$this->logger->addLog( '$valuePro', $valuePro );
					$this->dataActValue( $valueActivity );
					$this->dataActPro( $valuePro );
					if ( $this->saveDetail( 0 ) ) {
						$this->addToTotal( $valueActivity, $valuePro );
						$_SESSION['userActivityRating'] = $currentDate;
					}
				}
			}
		}
	}

	public function employerPaymentProject( $bidId = 0 ) {
		global $user_ID;

		$amount = get_post_meta( $bidId, 'bid_budget', true );

		$this->doActionAmountPayment( $user_ID, $amount );
	}

	public function doActionAmountPayment( $userId = 0, $amount = 0 ) {
		$this->logger->addLog( '__METHOD__', __METHOD__ );
		//		$this->logger->addLog('doActionAmountPayment', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
		$this->logger->addLog( 'time', date( 'Y-m-d H:i:s' ) );
		$this->logger->addLog( 'userId', $userId );
		$this->logger->addLog( 'amount', $amount );

		if ( $userId && $amount > 0 ) {
			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_amountPayment );

			$valueActivity = Config::getInstance()->getCoeffAmountPayment();
			$this->logger->addLog( '$valueActivity', $valueActivity );

			$valueActivity = ceil( $amount ) * floatval( $valueActivity );
			$this->logger->addLog( '$valueActivity', $valueActivity );

			//$valueActivity = (userHaveProStatus($userId))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;

			//			$this->logger->addLog('$valueActivity', $valueActivity);
			$this->logger->addLog( '$valuePro', $valuePro );

			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );

			$this->logger->addLog( 'getDataAct', $this->getDataAct() );

			if ( $this->saveDetail( 0 ) ) {
				$this->addToTotal( $valueActivity, $valuePro );
			}
		}

		$this->logger->addLog( 'doActionAmountPayment_REQUEST', $_REQUEST );
		$this->logger->addLog( 'doActionAmountPayment_REQUEST_URI', ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'null' );
		$this->logger->save();
	}

	public function doActionOneFieldProfile( $userId = 0 ) {
		global $user_ID;

		$userId = $user_ID ? $user_ID : $userId;
		if ( $userId ) {
			$this->logger->addLog( '__METHOD__', __METHOD__ );
			$this->logger->addLog( 'userId', $userId );
			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_oneFieldProfile );
			$valueActivity = Config::getInstance()->getValOneFieldProfile();

			$role = userRole( $userId );
			if ( $role == EMPLOYER ) {
				$isFreelancer   = '';
				$isWhFreelancer = '';
			} else {
				$isFreelancer = "
			IF(m_cert.meta_value IS NULL, 0, 1) AS certification,
			IF(m_educ.meta_value IS NULL, 0, 1) AS education,
			IF(m_w_exper.meta_value IS NULL, 0, 1) AS work_experience,";

				$isWhFreelancer = "
			LEFT JOIN {$this->tb_prefix}postmeta m_cert ON m_cert.post_id = p.ID AND m_cert.meta_key = 'certification'
			LEFT JOIN {$this->tb_prefix}postmeta m_educ ON m_educ.post_id = p.ID AND m_educ.meta_key = 'education'
			LEFT JOIN {$this->tb_prefix}postmeta m_w_exper ON m_w_exper.post_id = p.ID AND m_w_exper.meta_key = 'work_experience'
			";
			}

			$sql    = "SELECT IF(p.post_content = '' OR p.post_content IS NULL, 0, 1) AS content,
			{$isFreelancer}
			IF(m_avatar.meta_value IS NULL, 0, 1) AS avatar,
			IF(m_y_exper.meta_value IS NULL, 0, 1) AS year_experience,
			IF(rs.meta_value = 'confirm', 1, 0) AS register_status,
			IF(up.meta_value IS NULL, 0, 1) AS user_phone,
			IF(pp.meta_value IS NULL, 0, 1) AS paypal

			FROM {$this->tb_prefix}users u
			LEFT JOIN {$this->tb_prefix}posts p ON p.post_author = u.ID AND p.post_type = 'fre_profile'
			{$isWhFreelancer}
			LEFT JOIN {$this->tb_prefix}postmeta m_y_exper ON m_y_exper.post_id = p.ID AND m_y_exper.meta_key = 'et_experience'
			LEFT JOIN {$this->tb_prefix}usermeta m_avatar ON m_avatar.user_id = u.ID AND m_avatar.meta_key = 'et_avatar'
			LEFT JOIN {$this->tb_prefix}usermeta rs ON rs.user_id = u.ID AND rs.meta_key = 'register_status'
			LEFT JOIN {$this->tb_prefix}usermeta up ON up.user_id = u.ID AND up.meta_key = 'user_phone'
			LEFT JOIN {$this->tb_prefix}usermeta pp ON pp.user_id = u.ID AND pp.meta_key = 'paypal'

			WHERE u.ID = {$this->toInt($userId)}
			";
			$fields = $this->db->get_row( $sql, ARRAY_A );
			$this->logger->addLog( '$sql', $sql );
			$this->logger->addLog( '$fields', $fields );

			$countFields = 0;
			foreach ( $fields as $field => $value ) {
				if ( $value ) {
					$countFields ++;
				}
			}
			$this->logger->addLog( '$countFields', $countFields );
			$this->logger->addLog( '$valueActivity', $valueActivity );

			$valueActivity = $countFields * $valueActivity;
			$this->logger->addLog( '$valueActivity', $valueActivity );

			//$valueActivity = (userHaveProStatus($userId))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->logger->addLog( '$valueActivity', $valueActivity );
			$this->logger->addLog( '$valuePro', $valuePro );

			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			$currentValue    = $this->getValueActivity( $this->getDataActUid(), self::ACTIVITY_oneFieldProfile );
			$currentValuePro = $this->getValueActivityPro( $this->getDataActUid(), self::ACTIVITY_oneFieldProfile );
			if ( $this->saveDetail() ) {
				if ( $currentValue <= $valueActivity ) {
					$valueToTotal = $valueActivity - $currentValue;
					$proToTotal   = $valuePro - $currentValuePro;
					$this->logger->addLog( 'addToTotal', $valueToTotal );
					$this->logger->addLog( 'addToTotalPro', $proToTotal );
					$this->addToTotal( $valueToTotal, $proToTotal );
				} else {
					$valueToTotal = $currentValue - $valueActivity;
					$proToTotal   = $currentValuePro - $valuePro;
					$this->subtractFromTotal( $valueToTotal, $proToTotal );
				}
			}
			$this->logger->save();

			$this->doActionInstallmentPlan();
		}
	}

	public function doActionOnePortfolio() {
		global $user_ID;
		if ( $user_ID && userRole( $user_ID ) == FREELANCER ) {

			$this->dataActUid( $user_ID );
			$this->dataActType( self::ACTIVITY_onePortfolio );
			$valueActivity = Config::getInstance()->getFreelancer( self::ACTIVITY_onePortfolio );

			$countPortfolio = (int) $this->db->get_var( "SELECT COUNT(ID) FROM {$this->db->prefix}posts
			WHERE post_author = {$this->toInt($user_ID)} AND post_type = '" . $this->escapeStr( PORTFOLIO ) . "'" );
			$valueActivity  *= $countPortfolio;
			//$valueActivity = (userHaveProStatus($user_ID)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $user_ID ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );

			$currentValue    = $this->getValueActivity( $this->getDataActUid(), self::ACTIVITY_onePortfolio );
			$currentValuePro = $this->getValueActivityPro( $this->getDataActUid(), self::ACTIVITY_onePortfolio );
			if ( $this->saveDetail() ) {
				if ( $currentValue <= $valueActivity ) {
					$valueToTotal = $valueActivity - $currentValue;
					$proToTotal   = $valuePro - $currentValuePro;
					$this->addToTotal( $valueToTotal, $proToTotal );
				} else {
					$valueToTotal = $currentValue - $valueActivity;
					$proToTotal   = $currentValuePro - $valuePro;
					$this->subtractFromTotal( $valueToTotal, $proToTotal );
				}
			}
		}
	}

	public function doActionAsReferral( $userId = 0 ) {
		if ( $userId ) {
			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_asReferral );
			$method        = 'get' . ucfirst( userRole( $userId ) );
			$valueActivity = Config::getInstance()->$method( self::ACTIVITY_asReferral );

			//$valueActivity = (userHaveProStatus($userId))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			if ( $this->saveDetail( 0 ) ) {
				$this->addToTotal( $valueActivity, $valuePro );
			}
		}
	}

	public function doActionAsReferrer( $userId = 0 ) {
		if ( $userId ) {
			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_asReferrer );
			$method        = 'get' . ucfirst( userRole( $userId ) );
			$valueActivity = Config::getInstance()->$method( self::ACTIVITY_asReferrer );

			//$valueActivity = (userHaveProStatus($userId))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			if ( $this->saveDetail( 0 ) ) {
				$this->addToTotal( $valueActivity, $valuePro );
			}
		}
	}

	public function doActionForSkill( $count = 0 ) {
		global $user_ID;
		if ( $user_ID ) {
			$role = userRole( $user_ID );
			//if($role == FREELANCER) {
			$this->logger->addLog( '__METHOD__', __METHOD__ );
			$this->logger->addLog( '$count', $count );

			$this->dataActUid( $user_ID );
			$this->dataActType( self::ACTIVITY_forSkill );
			$valueActivity = $count * Config::getInstance()->getFreelancer( self::ACTIVITY_forSkill );
			$this->logger->addLog( '$valueActivity', $valueActivity );

			//$valueActivity = (userHaveProStatus($user_ID)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $user_ID ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->logger->addLog( '$valueActivity', $valueActivity );
			$this->logger->addLog( '$valuePro', $valuePro );

			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );

			$currentValue    = $this->getValueActivity( $this->getDataActUid(), self::ACTIVITY_forSkill );
			$currentValuePro = $this->getValueActivityPro( $this->getDataActUid(), self::ACTIVITY_forSkill );
			$this->logger->addLog( '$currentValue', $currentValue );
			$this->logger->addLog( '$currentValuePro', $currentValuePro );

			if ( $this->saveDetail() ) {
				if ( $currentValue <= $valueActivity ) {
					$valueToTotal = $valueActivity - $currentValue;
					$proToTotal   = $valuePro - $currentValuePro;
					$this->addToTotal( $valueToTotal, $proToTotal );
				} else {
					$valueToTotal = $currentValue - $valueActivity;
					$proToTotal   = $currentValuePro - $valuePro;
					$this->subtractFromTotal( $valueToTotal, $proToTotal );
				}
			}
			//}
		}

		//		$this->logger->addLog('query_er', $this->db->last_error);
		$this->logger->save();
	}

	public function doActionForEndorseSkill( $userId = 0, $skillId = 0, $endorsed = 1 ) {
		if ( $userId && $skillId ) {
			$this->logger->addLog( '__METHOD__', __METHOD__ );
			$role = userRole( $userId );
			//if($role == FREELANCER) {
			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_forEndorseSkill );
			$this->dataActSourceId( $skillId );
			$method        = 'get' . ucfirst( $role );
			$valueActivity = $endorsed * Config::getInstance()->$method( self::ACTIVITY_forEndorseSkill );
			//$valueActivity = (userHaveProStatus($userId)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );

			$currentValue    = $this->getValueActivity( $this->getDataActUid(), self::ACTIVITY_forEndorseSkill, $this->getDataActSourceId() );
			$currentValuePro = $this->getValueActivityPro( $this->getDataActUid(), self::ACTIVITY_forEndorseSkill, $this->getDataActSourceId() );
			if ( $this->saveDetail() ) {
				if ( $currentValue <= $valueActivity ) {
					$valueToTotal = $valueActivity - $currentValue;
					$proToTotal   = $valuePro - $currentValuePro;
					$this->addToTotal( $valueToTotal, $proToTotal );
				} else {
					$valueToTotal = $currentValue - $valueActivity;
					$proToTotal   = $currentValuePro - $valuePro;
					$this->subtractFromTotal( $valueToTotal, $proToTotal );
				}
			}
			//}
		}
	}

	public function doActionForReview() {
		global $user_ID;
		if ( $user_ID ) {
			$this->logger->addLog( '__METHOD__', __METHOD__ );
			$this->dataActUid( $user_ID );
			$this->dataActType( self::ACTIVITY_forReview );
			$method        = 'get' . ucfirst( userRole( $user_ID ) );
			$valueActivity = Config::getInstance()->$method( self::ACTIVITY_forReview );

			//$valueActivity = (userHaveProStatus($user_ID))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $user_ID ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			if ( $this->saveDetail( 0 ) ) {
				$this->addToTotal( $valueActivity, $valuePro );
			}
		}
	}

	public function doActionProjectSuccess( $employerId = 0, $freelancerId = 0 ) {
		$this->logger->addLog( '__METHOD__', __METHOD__ );
		$this->dataActUid( $employerId );
		$this->dataActType( self::ACTIVITY_projectSuccess );
		$valueActivity = Config::getInstance()->getEmployer( self::ACTIVITY_projectSuccess );
		$this->logger->addLog( '$employerId', $employerId );
		$this->logger->addLog( '$employerId$valueActivity', $valueActivity );

		$freelancerRating = $this->addFreelancerRating( $freelancerId );
		$this->logger->addLog( '$employerId$freelancerRating', $freelancerRating );
		$valueActivity += $freelancerRating;
		$this->logger->addLog( '$employerId$valueActivity', $valueActivity );

		//$valueActivity = (userHaveProStatus($employerId)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
		$valueActivity = ceil( $valueActivity );
		$valuePro      = ( userHaveProStatus( $employerId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;

		$this->logger->addLog( '$employerId$valueActivity', $valueActivity );
		$this->logger->addLog( '$employerId$valuePro', $valuePro );

		$this->dataActValue( $valueActivity );
		$this->dataActPro( $valuePro );
		if ( $this->saveDetail( 0 ) ) {
			$this->addToTotal( $valueActivity, $valuePro );
		}

		$this->logger->save();
	}

	public function doActionProjectSuccessFreelancer( $freelancerId = 0, $employerId = 0 ) {
		$this->logger->addLog( '__METHOD__', __METHOD__ );

		$this->dataActUid( $freelancerId );
		$this->dataActType( self::ACTIVITY_projectSuccess );
		$valueActivity = Config::getInstance()->getFreelancer( self::ACTIVITY_projectSuccess );
		$this->logger->addLog( '$freelancerId', $freelancerId );
		$this->logger->addLog( '$freelancerId$valueActivity', $valueActivity );

		$employerRating = $this->addEmployerRating( $employerId );
		$this->logger->addLog( '$freelancerId$employerRating', $employerRating );
		$valueActivity += $employerRating;
		$this->logger->addLog( '$freelancerId$valueActivity', $valueActivity );

		//$valueActivity = (userHaveProStatus($freelancerId)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
		$valueActivity = ceil( $valueActivity );
		$valuePro      = ( userHaveProStatus( $freelancerId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
		$this->logger->addLog( '$freelancerId$valueActivity', $valueActivity );
		$this->logger->addLog( '$freelancerId$valuePro', $valuePro );

		$this->dataActValue( $valueActivity );
		$this->dataActPro( $valuePro );
		if ( $this->saveDetail( 0 ) ) {
			$this->addToTotal( $valueActivity, $valuePro );
		}

		$this->logger->save();
	}

	public function doActionBidAccepted() {
		global $user_ID;
		if ( $user_ID ) {
			$this->logger->addLog( '__METHOD__', __METHOD__ );
			$this->dataActUid( $user_ID );
			$this->dataActType( self::ACTIVITY_bidAccepted );
			$valueActivity = Config::getInstance()->getEmployer( self::ACTIVITY_bidAccepted );

			//$valueActivity = (userHaveProStatus($user_ID))? $this->calcWithPRO($valueActivity) : ceil($valueActivity);

			$valueActivity = ceil( $valueActivity );
			$valuePro      = ( userHaveProStatus( $user_ID ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			if ( $this->saveDetail( 0 ) ) {
				$this->addToTotal( $valueActivity, $valuePro );
			}
		}
	}

	public static function doActionForReward()//not work
	{

	}

	public function doActionInstallmentPlan( $userId = 0 )//temporally called in doActionOneFieldProfile
	{
		global $user_ID;

		$userId = $user_ID ? $user_ID : $userId;
		if ( $userId ) {

			$sql             = "SELECT IF(inp.meta_value = 1, 1, 0)
			FROM {$this->tb_prefix}users u
			LEFT JOIN {$this->tb_prefix}posts p ON p.post_author = u.ID AND p.post_type = 'fre_profile'
			LEFT JOIN {$this->tb_prefix}postmeta inp ON inp.post_id = p.ID AND inp.meta_key = 'installmentPlan'

			WHERE u.ID = {$this->toInt($userId)}";
			$installmentPlan = $this->db->get_var( $sql );

			$this->logger->addLog( '$installmentPlan$sql', $sql );
			$this->logger->addLog( '$installmentPlan', $installmentPlan );
			$this->logger->addLog( '$installmentPlan$empty', empty( $installmentPlan ) );
			$this->logger->save();

			$this->dataActUid( $userId );
			$this->dataActType( self::ACTIVITY_installmentPlan );

			if ( empty( $installmentPlan ) ) {
				$valueActivity = 0;
				$valuePro      = 0;
			} else {
				$method        = 'get' . ucfirst( userRole( $userId ) );
				$valueActivity = Config::getInstance()->$method( self::ACTIVITY_installmentPlan );
				//$valueActivity = (userHaveProStatus($userId)) ? $this->calcWithPRO($valueActivity) : ceil($valueActivity);
				$valueActivity = ceil( $valueActivity );
				$valuePro      = ( userHaveProStatus( $userId ) ) ? $this->calcWithPRO( $valueActivity ) : 0;
			}

			$this->dataActValue( $valueActivity );
			$this->dataActPro( $valuePro );
			$currentValue    = $this->getValueActivity( $this->getDataActUid(), self::ACTIVITY_installmentPlan );
			$currentValuePro = $this->getValueActivityPro( $this->getDataActUid(), self::ACTIVITY_installmentPlan );
			if ( $this->saveDetail() ) {
				if ( $currentValue <= $valueActivity ) {
					$valueToTotal = $valueActivity - $currentValue;
					$proToTotal   = $valuePro - $currentValuePro;
					$this->addToTotal( $valueToTotal, $proToTotal );
				} else {
					$valueToTotal = $currentValue - $valueActivity;
					$proToTotal   = $currentValuePro - $valuePro;
					$this->subtractFromTotal( $valueToTotal, $proToTotal );
				}
			}
		}
	}

	private function calcWithPRO( $value ) {
		//		$value = floatval($value) + (floatval($value) * Config::getInstance()->getCoeffProStatus());
		$value = floatval( $value ) * Config::getInstance()->getCoeffProStatus();

		return ceil( $value );
	}

	private function addEmployerRating( $employerId ) {
		$employerRating = $this->getTotal( $employerId );
		$this->logger->addLog( '$employerRating', $employerRating );
		$coefficient = Config::getInstance()->getCoeffFromRatingEmployer();
		$value       = floatval( $employerRating ) * $coefficient;

		return ceil( $value );
	}

	private function addFreelancerRating( $freelancerId ) {
		$freelancerRating = $this->getTotal( $freelancerId );
		$this->logger->addLog( '$freelancerRating', $freelancerRating );
		$coefficient = Config::getInstance()->getCoeffFromRatingFreelancer();
		$value       = floatval( $freelancerRating ) * $coefficient;

		return ceil( $value );
	}

	private function saveDetail( $reWrite = true ) {
		if ( empty( $this->_dataAct ) ) {
			return false;
		}

		$addSum    = boolval( $reWrite ) ? '' : '+ value';
		$addSumPro = boolval( $reWrite ) ? '' : '+ value_pro';
		$time      = $this->getUTCTimestamp();
		$sql       = "INSERT INTO {$this->tbRatingDetail} (user_id, type_activity, time, value, value_pro, source_id, additional_data)
		VALUES (
		{$this->getDataActUid()},
		'{$this->getDataActType()}',
		'{$this->escapeStr($time)}',
		{$this->getDataActValue()},
		{$this->getDataActPro()},
		{$this->getDataActSourceId()},
		'{$this->getDataActAdditional()}'
		)
		ON DUPLICATE KEY UPDATE value = VALUES(value) {$addSum}, value_pro = VALUES(value_pro) {$addSumPro}, additional_data = VALUES(additional_data), time = VALUES(time)
		";
		$result    = $this->db->query( $sql );

		return $result;
	}

	private function addToTotal( $value = 0, $pro = 0 ) {
		$result = false;
		if ( $this->getDataActUid() ) {
			$sql    = "INSERT INTO {$this->tbRating} (user_id, rating, pro_rating, updated)
			VALUES ({$this->getDataActUid()}, {$this->toInt($value)}, {$this->toInt($pro)}, '{$this->getUTCTimestamp()}')
			ON DUPLICATE KEY UPDATE rating = VALUES(rating) + rating, pro_rating = VALUES(pro_rating) + pro_rating, updated = VALUES(updated)
			";
			$result = $this->db->query( $sql );
			$this->resetDataAct();
		}

		return $result;
	}

	private function subtractFromTotal( $value = 0, $valuePro = 0 ) {
		$result = false;
		if ( $this->getDataActUid() ) {
			$rating = $this->getRating( $this->getDataActUid() );
			$pro    = $this->getProRating( $this->getDataActUid() );

			$upd['rating']     = ( ceil( $value ) >= $rating ) ? 0 : $rating - ceil( $value );
			$upd['pro_rating'] = ( ceil( $valuePro ) >= $pro ) ? 0 : $pro - ceil( $valuePro );
			$upd['updated']    = $this->getUTCTimestamp();
			$result            = $this->db->update( $this->tbRating, $upd, [ 'user_id' => $this->getDataActUid() ] );
			$this->resetDataAct();
		}

		return $result;
	}

	private function deleteSourseActivity( $userId, $activity, $sourceId ) {

	}

	private function dataActUid( $userId ) {
		$this->_dataAct['userId'] = $this->toInt( $userId );
	}

	private function getDataActUid() {
		return $this->_dataAct['userId'];
	}

	private function dataActType( $type ) {
		$this->_dataAct['type'] = $this->escapeStr( $type );
	}

	private function getDataActType() {
		return $this->_dataAct['type'];
	}

	private function dataActValue( $value ) {
		$this->_dataAct['value'] = $this->toInt( $value );
	}

	private function getDataActValue() {
		return $this->_dataAct['value'];
	}

	private function dataActPro( $value ) {
		$this->_dataAct['valuePro'] = $this->toInt( $value );
	}

	private function getDataActPro() {
		return $this->_dataAct['valuePro'];
	}

	private function dataActSourceId( $sourceId ) {
		$this->_dataAct['sourceId'] = $this->toInt( $sourceId );
	}

	private function getDataActSourceId() {
		return $this->_dataAct['sourceId'];
	}

	private function dataActAdditional( $add ) {
		$this->_dataAct['additionalData'] = ( is_array( $add ) ) ? json_encode( $add ) : $this->escapeStr( $add );
	}

	private function getDataActAdditional() {
		return $this->_dataAct['additionalData'];
	}

	public function getDataAct() {
		return $this->_dataAct;
	}

	private function resetDataAct() {
		$this->_dataAct = [
			'userId'         => 0,
			'type'           => '',
			'value'          => 0,
			'valuePro'       => 0,
			'sourceId'       => 0,
			'additionalData' => '',
		];
	}

	public function getTotal( $userId = 0 ) {
		return $this->db->get_var( "SELECT rating FROM {$this->tbRating} WHERE user_id = {$this->toInt($userId)}" );
	}

	public function getRating( $userId = 0 ) {
		return $this->db->get_var( "SELECT rating FROM {$this->tbRating} WHERE user_id = {$this->toInt($userId)}" );
	}

	public function getProRating( $userId = 0 ) {
		return $this->db->get_var( "SELECT pro_rating FROM {$this->tbRating} WHERE user_id = {$this->toInt($userId)}" );
	}

	public function getPro( $userId = 0, $sum = 1 ) {
		$total = $this->getTotal( $userId );

		$result = floatval( $total ) * Config::getInstance()->getCoeffProStatus();
		$result += ( $sum ) ? $total : 0;

		return ceil( $result );
	}

	public function getDetail( $userId = 0 ) {
		$sql = "SELECT type_activity, SUM(value) as value FROM {$this->tbRatingDetail} WHERE user_id = {$this->toInt($userId)} GROUP BY type_activity ORDER BY type_activity ASC";

		return (array) $this->db->get_results( $sql, OBJECT_K );
	}

	public function getValueActivity( $userId = 0, $type = '', $sourceId = 0 ) {
		return (int) $this->db->get_var( "SELECT value FROM {$this->tbRatingDetail} WHERE
		user_id = {$this->toInt($userId)}
		AND type_activity = '{$this->escapeStr($type)}'
		AND source_id = {$this->toInt($sourceId)}" );
	}

	public function getValueActivityPro( $userId = 0, $type = '', $sourceId = 0 ) {
		return (int) $this->db->get_var( "SELECT value_pro FROM {$this->tbRatingDetail} WHERE
		user_id = {$this->toInt($userId)}
		AND type_activity = '{$this->escapeStr($type)}'
		AND source_id = {$this->toInt($sourceId)}" );
	}

	public function getLastTypeActivity( $userId = 0, $type = '' ) {
		return $this->db->get_row( "SELECT DISTINCT * FROM {$this->tbRatingDetail} WHERE
		user_id = {$this->toInt($userId)}
		AND type_activity = '{$this->escapeStr($type)}'
		ORDER BY time DESC", ARRAY_A );
	}

	public static function getUTCTimestamp( $asInt = 0 ) {
		$time = time();
		$now  = \DateTime::createFromFormat( 'U', $time );
		$now->setTimeZone( new \DateTimeZone( 'UTC' ) );

		return boolval( $asInt ) ? (int) $now->getTimestamp() : $now->format( 'Y-m-d H:i:s' );
	}

	public static function getUTCDate() {
		$time = time();
		$now  = \DateTime::createFromFormat( 'U', $time );
		$now->setTimeZone( new \DateTimeZone( 'UTC' ) );

		return $now->format( 'Y-m-d' );
	}

	public static function getMicroTime() {
		$time = time();
		$now  = \DateTime::createFromFormat( 'U', $time );
		$now->setTimeZone( new \DateTimeZone( 'UTC' ) );

		return $now->format( 'Y-m-d H:i:s.u' );
	}

	public static function getListActivity( $role = 'freelancer' ) {
		$list = [];
		if ( $role == EMPLOYER ) {
			$list = [
				self::ACTIVITY_amountPayment,
				self::ACTIVITY_oneFieldProfile,
				self::ACTIVITY_siteVisit,
				self::ACTIVITY_asReferral,
				self::ACTIVITY_asReferrer,
				self::ACTIVITY_forSkill,
				self::ACTIVITY_forEndorseSkill,
				self::ACTIVITY_forReview,
				self::ACTIVITY_projectSuccess,
				self::ACTIVITY_bidAccepted,
				self::ACTIVITY_installmentPlan,
				self::ACTIVITY_forReward,
			];
		} else {
			$list = [
				self::ACTIVITY_amountPayment,
				self::ACTIVITY_oneFieldProfile,
				self::ACTIVITY_siteVisit,
				self::ACTIVITY_onePortfolio,
				self::ACTIVITY_asReferral,
				self::ACTIVITY_asReferrer,
				self::ACTIVITY_forSkill,
				self::ACTIVITY_forEndorseSkill,
				self::ACTIVITY_forReview,
				self::ACTIVITY_projectSuccess,
				self::ACTIVITY_installmentPlan,
				self::ACTIVITY_forReward,
			];
		}

		return $list;
	}

	public static function getInstance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}