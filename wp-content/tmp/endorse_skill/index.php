<?php
	/*
	Plugin Name: Skills & Endorsements
	Description:
	Version: 1.0.panda
	Lat Update: 01.08.2019
	Author:
	Author URI:
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	define( 'ENDORSE_SKILL_DIR', __DIR__ . '/' );
	define( 'ENDORSE_SKILL_RELATIVE', '/wp-content/plugins/' . basename( __DIR__ ) );
	add_action( 'plugins_loaded', 'endorse_skill_load', 0 );
	add_action( 'admin_menu', 'add_menu_endorse_skill', 0 );

	function endorse_skill_load() {
		require_once get_template_directory() .  '/wpp/vendor/autoload.php';
		require_once __DIR__ . '/classes/AutoloadEndorseSkill.php';
		AutoloadEndorseSkill::init();


		add_action( 'wp_ajax_getFEditESk', 'ajaxGetSkillsForEdit' );
		add_action( 'wp_ajax_saveESk', 'ajaxSaveSkillsUser' );
		add_action( 'wp_ajax_endorseSk', 'ajaxEndorseSkill' );
		add_action( 'wp_ajax_unEndorseSk', 'ajaxUnbindEndorseSkill' );

		register_activation_hook( __FILE__, 'activateEndorseSkill' );
		add_action( 'activate_' . basename( __DIR__ ) . '/index.php', 'activateEndorseSkill', 1 );
		add_action( 'wp_enqueue_scripts', 'register_assets' );

	}


	function register_assets() {
		if ( ! is_admin() ) {
			wp_register_style( 'endoSkSel', '/wp-content/plugins/endorse_skill/css/select2.min.css' );
			wp_register_style( 'endoSk', '/wp-content/plugins/endorse_skill/css/endorse_skill.css' );
			wp_register_script( 'endoSkSel', '/wp-content/plugins/endorse_skill/js/select2.full.min.js', [], '1.0', true );
			wp_register_script( 'endoSk', '/wp-content/plugins/endorse_skill/js/endorse_skill.js', [], '1.0', true );
		}
	}

	function add_menu_endorse_skill() {
		add_menu_page( __( 'Skills & Endorsements', 'endorse_skill' ), __( 'Skills & Endorsements', 'endorse_skill' ), 'administrator', 'endorse_skill', 'endorse_skill_page', null );

		createTbEndorseSkill();
	}

	function createTbEndorseSkill() {
		if ( ! \EndorseSkill\Module::getInstance()->tbIsExists() ) {
			\EndorseSkill\Module::getInstance()->installTb();
		}
	}

	function activateEndorseSkill() {
		if ( ! \EndorseSkill\Module::getInstance()->tbIsExists() ) {
			\EndorseSkill\Module::getInstance()->installTb();
		}
		\EndorseSkill\Module::getInstance()->updateTb();
	}

	function endorse_skill_page() {
		require_once ENDORSE_SKILL_DIR . 'module.php';
	}

	function ajaxGetSkillsForEdit() {
		global $user_ID;

		if ( $role = userRole( $user_ID ) ) {
			$result = EndorseSkill\Endorse::getInstance()->getForEdit( $user_ID, $role );

			EndorseSkill\Base::outputJSON( [ 'items' => $result ], 1 );
		}

		EndorseSkill\Base::outputJSON();
	}

	function ajaxSaveSkillsUser() {
		//template: wp-content/themes/freelanceengine/template-js/modal-edit-skills.php
		global $user_ID;

		if ( $user_ID && $role = userRole( $user_ID ) ) {
			$endorse = EndorseSkill\Endorse::getInstance();
			$skill   = new EndorseSkill\Skill();
			if ( $skill->bindToUser( $user_ID, $_POST[ 'skills' ], $role ) ) {
				$items  = $endorse->forUser( $user_ID );
				$result = $endorse->fenom->fetch( 'listSkills.tpl', [ 'items' => $items ] );

				$endorse::outputJSON( [ 'html' => $result ], 1 );
			}
		}
		//	if($role == FREELANCER){}
		EndorseSkill\Base::outputJSON( 'No changes detected!' );
	}

	function ajaxEndorseSkill() {
		global $user_ID;

		$uid     = (int) $_POST[ 'uid' ];
		$skillId = (int) $_POST[ 'skill' ];
		if ( $user_ID && $uid && $skillId ) {
			$endorse = \EndorseSkill\Endorse::getInstance();
			if ( $uid == $user_ID ) {
				$endorse::outputJSON( $endorse->getLang( 'endorse_dont_access' ) );
			}
			if ( $endorse->bind( $uid, $skillId, $user_ID ) ) {

				$result = $endorse->valueUserSkill( $uid, $skillId );

				do_action( 'activityRating_forEndorseSkill', $uid, $skillId, $result );

				EndorseSkill\Endorse::outputJSON( [ 'value' => $result ], 1 );
			}
		}

		EndorseSkill\Endorse::outputJSON();
	}

	function ajaxUnbindEndorseSkill() {
		global $user_ID;

		$uid     = (int) $_POST[ 'uid' ];
		$skillId = (int) $_POST[ 'skill' ];
		if ( $user_ID && $uid && $skillId ) {
			$endorse = \EndorseSkill\Endorse::getInstance();

			if ( $endorse->unBind( $uid, $skillId, $user_ID ) ) {
				$result = $endorse->valueUserSkill( $uid, $skillId );

				do_action( 'activityRating_forEndorseSkill', $uid, $skillId, $result );

				EndorseSkill\Endorse::outputJSON( [ 'value' => $result ], 1 );
			}
		}

		EndorseSkill\Endorse::outputJSON();
	}

	function renderSkillsInProfile( $userId, $modeEndorse = false, $currentUser = 0 ) {
		$vars[ 'items' ]       = EndorseSkill\Endorse::getInstance()->forUser( $userId, $currentUser );
		$vars[ 'modeEndorse' ] = boolval( $modeEndorse );
		$vars[ 'userId' ]      = $userId;
		$vars[ 'currentUser' ] = $currentUser;

		EndorseSkill\Endorse::getInstance()->fenom->display( 'listSkills.tpl', $vars );
	}

	function renderSkillsInProject( $userId, $currentUser = 0 ) {
		wp_enqueue_style( 'endoSk' );
		wp_enqueue_script( 'endoSk' );

		$vars[ 'items' ]       = EndorseSkill\Endorse::getInstance()->forUser( $userId, $currentUser );
		$vars[ 'userId' ]      = $userId;
		$vars[ 'currentUser' ] = $currentUser;

		EndorseSkill\Endorse::getInstance()->fenom->display( 'listSkillsInProject.tpl', $vars );
	}

	function checkEndorseSkills( $userId, $currentUser = 0 ) {
		wp_enqueue_style( 'endoSk' );
		wp_enqueue_script( 'endoSk' );

		$endorseSkills = EndorseSkill\Endorse::getInstance()->forUser( $userId, $currentUser );

		$endorse = false;
		foreach ( $endorseSkills as $skill ) {
			if ( $skill[ 'endorse' ] == 1 ) {
				$endorse = true;
				break;
			}
		}

		return $endorse == true ? 'Endorsed' : 'Not Endorsed';
	}

	function getSkillsUser( $userId ) {
		return EndorseSkill\Endorse::getInstance()->forUser( $userId );
	}

	function countEndorseSkillsUser( $userId ) {
		return EndorseSkill\Endorse::getInstance()->getCount( $userId );
	}
