<?php
if (!defined('WP_ADMIN') && !defined('REVIEW_RATING_DIR')) die('LoL');

header('Cache-Control: no-cache; no-store; must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

ini_set('display_errors', 1);

define( 'RW_RT_MODULE_TPL_PATH', REVIEW_RATING_DIR . 'module/tpl/' );

require_once ABSPATH . 'vendor/autoload.php';
require_once(REVIEW_RATING_DIR . 'classes/AutoloadReviews.php');
AutoloadReviews::init();

$module = ReviewsRating\Module::getInstance();
$module->setLangTag('en');
$module->setLangPath('module/lang');

$langMsg = $module->getLang('ALL');

$tplCache = RW_RT_MODULE_TPL_PATH . 'cache';
if(!file_exists($tplCache)){
	mkdir($tplCache, 0755, true);
}

$fenom = Fenom::factory(RW_RT_MODULE_TPL_PATH, RW_RT_MODULE_TPL_PATH.'cache');
$fenom->setOptions(Fenom::AUTO_RELOAD);
//$fenom->setOptions(Fenom::DISABLE_CACHE);// - откл. кэширование

$moduleUrl = '/wp-admin/admin.php?page=reviews_rating';

//sleep(2);

$vars = [];
$vars['PATH_INC'] = REVIEW_RATING_RELATIVE;
$vars['MODULE_URL'] = $moduleUrl;
$vars['VERSION'] = ReviewsRating\Base::VERSION;
$vars['lang'] = $langMsg;
$vars['default_set'] = '<script type="text/javascript">
			//mod.url_send = "' . $moduleUrl . '";
		</script>';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch($action) {
	case 'install':
		$module->installTb();

		include __DIR__ . '/update.php';

		header("Location: {$_SERVER['REQUEST_URI']}");
		break;
	case 'uninstall':
		$module->uninstallTb();

		header("Location: {$_SERVER['REQUEST_URI']}");
		break;
	case 'update':
		include __DIR__ . '/update.php';

		$fenom->display('update.tpl', $vars);
		break;
	case 'onCreateReview':

		$vars['listDoc'] = $module->getListDoc();

		$fenom->display('createReview.tpl', $vars);
		break;
	case 'createReview':

		$postData = $_POST;

		$postData['status'] = 'approved';
		$review = new ReviewsRating\Reviews($postData['docId']);
		$id = $review->create($postData);
		if ($id !== false) {
			$out['msg'] = $review->getLang('review_publish');
			$out['reload'] = 1;
			\ReviewsRating\Base::outputJSON($out, 1);
		}

		$log = ReviewsRating\Log::getInstance()->getLog();
		$msg = empty($log)? $review->getLang('something_went_wrong') : implode(', ', $log);
		\ReviewsRating\Base::outputJSON($msg);

		break;
	case 'detailReview':

		$rwId = (int)$_GET['rw'];

		$review = \ReviewsRating\Reviews::getInstance();
		$vars['review'] = $review->getReview($rwId);
		$vars['doc'] = $review->getDocReview($vars['review']['doc_id']);
		$vars['listStatuses'] = $review->getListStatuses();

		$fenom->display('viewReview.tpl', $vars);

		break;
	case 'deleteReview':

		$rwId = (int)$_POST['rwId'];
		$review = \ReviewsRating\Reviews::getInstance();
		if($review->deleteReview($rwId)) {

			\ReviewsRating\Base::outputJSON([], 1);
		} else {
			\ReviewsRating\Base::outputJSON();
		}
		break;
	case 'resetRating':

		$docId = (int)$_POST['docId'];
		$review = new \ReviewsRating\Reviews($docId);
		if($review->resetRating()) {
			\ReviewsRating\Base::outputJSON([], 1);
		} else {
			\ReviewsRating\Base::outputJSON();
		}
		break;
	case 'changeStatus':
		$status = $_POST['status'];
		$rwId = (int)$_POST['rwId'];

		$review = \ReviewsRating\Reviews::getInstance();
		$dataReview = $review->getReview($rwId);
		if($dataReview['status'] !== $status) {
			if ($review->setStatus($rwId, $status)) {
				if ($status == \ReviewsRating\Reviews::STATUS_APPROVED) {
					$review->setDocId($dataReview['doc_id'])->addVote($dataReview['vote']);
				}
				elseif ($status != \ReviewsRating\Reviews::STATUS_APPROVED
						&& $dataReview['status'] == \ReviewsRating\Reviews::STATUS_APPROVED) {
					$review->setDocId($dataReview['doc_id'])->subtractVote($dataReview['vote']);
				}
				\ReviewsRating\Base::outputJSON([], 1);
			} else {
				$log = ReviewsRating\Log::getInstance()->getLog();
				$msg = empty($log) ? $review->getLang('something_went_wrong') : implode(', ', $log);
				\ReviewsRating\Base::outputJSON($msg);
			}
		}
		\ReviewsRating\Base::outputJSON($module->getLang('status_no_change'));
		break;
//	case 'viewReviews':
//
//		$docId = (int)$_GET['doc'];
//
//		$vars['listReviews'] = $module->getListReviews($docId);
//
//		$fenom->display('viewReviews.tpl', $vars);
//
//		break;
	case 'getRtList':

		$module->setRtSearch($_POST['search']);
		$module->setSqlLimit($_POST['page']);
		$module->setRtOrderBy($_POST['orderBy']);

		$vars['ratings'] = $module->getListRatings();

		$data['list'] = $fenom->fetch('list_ratings.tpl', $vars);
		$data['pagination'] = $module->getRtPagination($_POST['page']);

		\ReviewsRating\Base::outputJSON($data, 1);
		break;
	case 'getRwList':

		$module->setRwSearch($_POST['search']);
		$module->setRwOrderBy($_POST['orderBy']);
		$module->setSqlLimit($_POST['page']);

		$vars['reviews'] = $module->getListReviews();

		$data['list'] = $fenom->fetch('list_reviews.tpl', $vars);
		$data['pagination'] = $module->getRwPagination($_POST['page']);

		\ReviewsRating\Base::outputJSON($data, 1);
		break;

	case 'updConfig':
		if($module->updConfig($_POST)){
			\ReviewsRating\Base::outputJSON('ok',1);
		} else {
			\ReviewsRating\Base::outputJSON();
		};
		break;


default:

	if(!$module->tbIsExists()){
		$fenom -> display( 'install.tpl', $vars );
	} else {

		$config = $module->getConfig();
		$pageStep = !empty($config['page_step']) ? $config['page_step'] : 15;


		if (version_compare(ReviewsRating\Base::VERSION, $config['VERSION'], '>')) {
			$vars['UPDATE'] = true;
		}

		$vars['config'] = $config;
		$vars['ratings'] = $module->getListRatings();
		$vars['reviews'] = $module->getListReviews();
		$vars['rtPagination'] = $module->getRtPagination();
		$vars['rwPagination'] = $module->getRwPagination();

		$fenom->display('main.tpl', $vars);
	}
	break;
}

exit;