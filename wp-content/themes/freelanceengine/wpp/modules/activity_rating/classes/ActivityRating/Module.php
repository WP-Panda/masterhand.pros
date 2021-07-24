<?php

	namespace ActivityRating;

	class Module extends Base{
		protected static $_instance = null;

		protected $varsTpl = [];

		protected $sqlOrderBy = 's.id DESC';
		protected $sqlSearch = '';
		protected $sqlLimit = '';
		protected $_pageStep = 0;

		public function __construct() {
			parent::__construct();

			$pathTpl   = $this->modulePath . 'tpl/module/';
			$pathCache = $pathTpl . 'cache';
			if ( ! file_exists( $pathCache ) ) {
				mkdir( $pathCache, 0755, true );
			}

			$this->fenom = \Fenom::factory( $pathTpl, $pathCache );
			$this->fenom->setOptions( \Fenom::AUTO_RELOAD );
			//$this->fenom->setOptions(Fenom::DISABLE_CACHE);// - откл. кэширование

			$moduleUrl = '/wp-admin/admin.php?page=activity_rating';

			$this->setLangTag( 'en' );
			$this->setLangPath( 'lang/module' );

			$this->varsTpl[ 'PATH_INC' ]   = ACTIVITY_RATING_RELATIVE;
			$this->varsTpl[ 'MODULE_URL' ] = $moduleUrl;
			$this->varsTpl[ 'VERSION' ]    = self::VERSION;
			$this->varsTpl[ 'lang' ]       = $this->getLang( 'ALL' );
		}

		public function actionIndex() {

			$config       = Config::getInstance()->getAll();
			$newMap       = [];
			$newMap_tempo = [];
			foreach ( $config as $item => &$value ) {

				//			$newMap_tempo[$item] = [
				//				'freelancer' => strpos($item, 'freelancer'),
				//				'freelancerif' => strpos($item, 'freelancer') !== false,
				//				'freelancer_' => substr($item, 0, 10),
				//				'freelancer_if' => substr($item, 0, 10) == 'freelancer',
				//				'employer' => strpos($item, 'employer'),
				//				'employer_' => substr($item, 0, 8),
				//			];

				if ( strpos( $item, 'coefficient' ) !== false || strpos( $item, 'value.' ) == 0 ) {
					$newMap[ $item ] = $value;
					unset( $config[ $item ] );
				} else {
					if ( strpos( $item, 'freelancer' ) !== false ) {
						//				elseif(substr($item, 0, 10) == 'freelancer'){
						$newMap[ $item ][ 0 ] = [ $item => $value ];
						unset( $config[ $item ] );
						$tempVar = 'employer.' . ltrim( $item, 'freelancer.' );
						if ( isset( $config[ $tempVar ] ) ) {
							$newMap[ $item ][ 1 ][ $tempVar ] = $config[ $tempVar ];
							unset( $config[ $tempVar ] );
						}
					} //				if(strpos($item, 'employer') !== false){
					elseif ( substr( $item, 0, 8 ) == 'employer' ) {
						$newMap[ $item ][ 1 ] = [ $item => $value ];
						unset( $config[ $item ] );
						$tempVar = 'freelancer.' . ltrim( $item, 'employer.' );
						if ( isset( $config[ $tempVar ] ) ) {
							$newMap[ $item ][ 0 ][ $tempVar ] = $config[ $tempVar ];
							unset( $config[ $tempVar ] );
						}
					} else {
						$newMap[ $item ] = $value;
						unset( $config[ $item ] );
					}
				}
			}
			$this->varsTpl[ 'config' ]       = $newMap;
			$this->varsTpl[ 'newMap_tempo' ] = $newMap_tempo;
			//		$this->varsTpl['config'] = Config::getInstance()->getAll();
			$this->fenom->display( 'main.tpl', $this->varsTpl );

			exit;
		}

		public function actionUpdConfig() {
			$config = new Config();
			if ( $config->update( $_POST ) ) {
				self::outputJSON( $this->getLang( 'created' ), 1 );
			}

			$msg = $this->getLang( 'error' ) . ' ' . $config->getError();

			self::outputJSON( $msg );
		}

		public function actionGetList() {
			$this->setSearch( $_POST[ 'search' ] );
			$this->setSqlLimit( $_POST[ 'page' ] );
			$this->setOrderBy( $_POST[ 'orderBy' ] );

			$this->varsTpl[ 'skills' ] = $this->getList();

			$result[ 'list' ] = $this->fenom->fetch( 'list_skill.tpl', $this->varsTpl );

			self::outputJSON( $result, 1 );
		}

		public function getList() {
			$addWhere = $this->getSearch();
			$addWhere = ! empty( $addWhere ) ? "WHERE {$addWhere}" : '';

			$orderBy = "ORDER BY {$this->getOrderBy()}";
			$limit   = $this->getPageStep() ? "LIMIT {$this->getSqlLimit()}" : '';

			$sql = "SELECT * FROM {$this->tbRating} s {$addWhere} {$orderBy} {$limit}";

			return $this->db->get_results( $sql, ARRAY_A );
		}

		public function setOrderBy( $orderBy = '' ) {
			if ( ! empty( $orderBy ) ) {
				$dataOrderBy                = [];
				$data_parseOrderBy          = explode( ',', trim( $orderBy ) );
				$dataOrderBy[ 'field' ]     = self::checkField( $data_parseOrderBy[ 0 ] ) ? $this->escapeStr( $data_parseOrderBy[ 0 ] ) : '';
				$dataOrderBy[ 'direction' ] = ( trim( $data_parseOrderBy[ 1 ] ) == 'ASC' ) ? 'ASC' : 'DESC';

				if ( ( $dataOrderBy[ 'field' ] == 'used' ) ) {
					$orderBy = "{$dataOrderBy['field']} {$dataOrderBy['direction']}";
				} elseif ( ! empty( $dataOrderBy[ 'field' ] ) ) {
					$orderBy = "s.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
				} else {
					$orderBy = 's.id DESC';
				}

				$this->sqlOrderBy = $orderBy;
			}
		}

		protected function getOrderBy() {
			return $this->sqlOrderBy;
		}

		public function setSqlLimit( $page = 1, $offset = 0 ) {
			$dataStep = [];
			$page     = (int) $page;
			if ( $page ) {
				$pageStep = (int) $offset ? $offset : (int) $this->getPageStep();

				$dataStep[ 'from' ]   = ( $page == 1 ) ? 0 : ( ( $page * $pageStep ) - $pageStep );
				$dataStep[ 'offset' ] = $pageStep;
			}

			if ( ! empty( $dataStep ) ) {
				$this->sqlLimit = "{$dataStep['from']},{$dataStep['offset']}";
			}

			return $this;
		}

		public function getPageStep() {
			return $this->_pageStep;
		}

		protected function getSqlLimit() {
			return empty( $this->sqlLimit ) ? '0,' . (int) $this->getPageStep() : $this->sqlLimit;
		}

		public function setSearch( $word = '' ) {
			if ( ! empty( $word ) ) {
				$word = $this->escapeStr( $word );

				$addSearch = " s.user_id = {$this->toInt($word)}
			OR s.name LIKE '%{$word}%'";

				$this->sqlSearch = $addSearch;
			}

			return $this;
		}

		protected function getSearch() {
			return $this->sqlSearch;
		}

		public static function checkField( $field = '' ) {
			$arr = [ 'user_id', 'updated', ];

			return in_array( $field, $arr );
		}

		public function installTb() {
			$this->db->query( $this->sgl_tbRating() );
			$this->db->query( $this->sgl_tbRatingDetail() );
			$this->db->query( $this->sgl_tbRatingConfig() );
			$this->db->query( $this->sgl_defConfig() );
		}

		public function uninstallTb() {
			$this->db->query( "DROP TABLE IF EXISTS {$this->tbConfig}" );
			$this->db->query( "DROP TABLE IF EXISTS {$this->tbRating}" );
			$this->db->query( "DROP TABLE IF EXISTS {$this->tbRatingDetail}" );
		}

		public function updateTb() {
			$exist_column = $this->db->query( "SELECT pro_rating FROM {$this->tbRating} LIMIT 1" );
			if ( $this->db->last_error != '' ) {
				$this->db->query( $this->updatetbRating() );
			}

			$exist_column = $this->db->query( "SELECT value_pro FROM {$this->tbRatingDetail} LIMIT 1" );
			if ( $this->db->last_error != '' ) {
				$this->db->query( "ALTER TABLE `{$this->tbRatingDetail}` ADD `value_pro` BIGINT NOT NULL DEFAULT '0' AFTER `value`" );
			}

			if ( self::VERSION == '1.1' ) {
				$var = $this->db->get_var( "SELECT value FROM {$this->tbConfig} WHERE name = 'employer.value.fromRatingFreelancer'" );
				if ( empty( $var ) ) {
					$this->db->query( "INSERT INTO {$this->tbConfig} (name, value) VALUES ('employer.value.fromRatingFreelancer', 1)" );
				}

				$var = $this->db->get_var( "SELECT value FROM {$this->tbConfig} WHERE name = 'employer.value.forEndorseSkill'" );
				if ( empty( $var ) ) {
					$this->db->query( "INSERT INTO {$this->tbConfig} (name, value) VALUES ('employer.value.forEndorseSkill', 1)" );
				}
			}
		}

		private function sgl_tbRating() {
			return "CREATE TABLE IF NOT EXISTS `{$this->tbRating}` (
			`user_id` BIGINT(20) NOT NULL,
			`rating` BIGINT(20) NULL DEFAULT '0',
			`updated` TIMESTAMP NULL DEFAULT NULL,
			PRIMARY KEY (`user_id`)
		)
		COLLATE=utf8_general_ci
		ENGINE=InnoDB
		";
		}

		private function updatetbRating() {
			return "ALTER TABLE `{$this->tbRating}` ADD `pro_rating` BIGINT NOT NULL DEFAULT '0' AFTER `rating`";
		}

		private function sgl_tbRatingDetail() {
			return "CREATE TABLE IF NOT EXISTS `{$this->tbRatingDetail}` (
			`user_id` BIGINT(20) NOT NULL,
			`type_activity` VARCHAR(50) NOT NULL,
			`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`value` BIGINT(20) NULL DEFAULT '0',
			`source_id` BIGINT(20) NULL DEFAULT '0',
			`additional_data` TEXT NULL DEFAULT NULL,
			PRIMARY KEY (`user_id`, `type_activity`, `source_id`)
		)
		COLLATE=utf8_general_ci
		ENGINE=InnoDB
		";
		}

		private function sgl_tbRatingConfig() {
			return "CREATE TABLE IF NOT EXISTS `{$this->tbConfig}` (
			`name` VARCHAR(50) NOT NULL,
			`value` VARCHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`name`)
		)
		COLLATE=utf8_general_ci
		ENGINE=InnoDB
		";
		}

		private function sgl_defConfig() {
			return "INSERT INTO {$this->tbConfig} (name, value) VALUES
		('coefficient.proStatus', 1),
		('coefficient.amountPayment', 1),
		('value.siteVisit', 1),
		('value.oneFieldProfile', 1),
		('freelancer.value.installmentPlan', 1),
		('freelancer.value.onePortfolio', 1),
		('freelancer.value.asReferral', 1),
		('freelancer.value.asReferrer', 1),
		('freelancer.value.forReward', 1),
		('freelancer.value.forSkill', 1),
		('freelancer.value.forReview', 1),
		('freelancer.value.forEndorseSkill', 1),
		('freelancer.value.fromRatingEmployer', 1),
		('freelancer.value.projectSuccess', 1),
		('employer.value.fromRatingFreelancer', 1)
		('employer.value.installmentPlan', 1),
		('employer.value.asReferrer', 1),
		('employer.value.asReferral', 1),
		('employer.value.forReward', 1),
		('employer.value.forReview', 1),
		('employer.value.projectSuccess', 1),
		('employer.value.bidAccepted', 1),
		('employer.value.forEndorseSkill', 1),
		('employer.value.forSkill', 1)
		";
		}

		public static function getInstance() {
			if ( self::$_instance === null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}