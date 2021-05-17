<?php
namespace LikesUsers;


class Module extends Base
{
	protected static $_instance = null;

	protected $varsTpl = [];

	protected $sqlOrderBy = 'p.ID DESC';
	protected $sqlSearch = '';
	protected $sqlLimit = '';
	protected $_pageStep = 3;

	public function __construct()
	{
		parent::__construct();

		$pathTpl = $this->modulePath . 'tpl/module/';
		$pathCache = $pathTpl . 'cache';
		if(!file_exists($pathCache)){
			mkdir($pathCache, 0755, true);
		}

		$this->fenom = \Fenom::factory($pathTpl, $pathCache);
		$this->fenom->setOptions(\Fenom::AUTO_RELOAD);
		//$this->fenom->setOptions(Fenom::DISABLE_CACHE);// - откл. кэширование

		$moduleUrl = '/wp-admin/admin.php?page=likes_users';

		$this->setLangTag('en');
		$this->setLangPath('lang/module');

		$this->varsTpl['PATH_INC'] = LIKES_USERS_RELATIVE;
		$this->varsTpl['PATH_INC_OTHER'] = '/wp-content/themes/_for_plugins';
		$this->varsTpl['MODULE_URL'] = $moduleUrl;
		$this->varsTpl['VERSION'] = self::VERSION;
		$this->varsTpl['lang'] = $this->getLang('ALL');

		$this->tbPost = $this->tb_prefix . 'posts';
		$this->tbPostMeta = $this->tb_prefix . 'postmeta';
	}

	public function actionIndex()
	{
		$this->varsTpl['posts'] = $this->getList();
		$this->varsTpl['postPagination'] = $this->getPagination();

		$this->fenom->display('main.tpl', $this->varsTpl);

		exit;
	}

	public function actionGetList()
	{
		$this->setSearch($_POST['search']);
		$this->setSqlLimit($_POST['page']);
		$this->setOrderBy($_POST['orderBy']);

		$this->varsTpl['posts'] = $this->getList();

		$result['list'] = $this->fenom->fetch('list_post.tpl', $this->varsTpl);
		$result['pagination'] = $this->getPagination($_POST['page']);

		self::outputJSON($result, 1);
	}

	public function actionMoreDetail()
	{
		$postId = $this->toInt($_POST['docId']);
		$data = [];
		if($postId) {
			$sql = "SELECT
			(SELECT COUNT(post_id) FROM wp_likes_posts WHERE post_id = {$postId} AND user_id > 0) AS users,
			(SELECT COUNT(post_id) FROM wp_likes_posts WHERE post_id = {$postId} AND user_id = 0) AS anonymous
		";
			$result = $this->db->get_row($sql, ARRAY_A);

			$data['pie'] = $result;

			self::outputJSON($data, 1);
		}
		self::outputJSON();
	}

	public function actionResetLikes()
	{

		self::outputJSON([$_REQUEST]);
	}

	public function getList()
	{
		$addWhere = $this->getSearch();
		$addWhere = !empty($addWhere)? "AND ({$addWhere})" : '';

		$orderBy = "ORDER BY {$this->getOrderBy()}";
		$limit = $this->getPageStep()? "LIMIT {$this->getSqlLimit()}" : '';

		$sql = "SELECT p.ID, p.post_title, p.guid, pm.meta_value as likes_users FROM {$this->tbPost} p
		LEFT JOIN {$this->tbPostMeta} pm ON pm.post_id = p.ID AND meta_key = 'likes_users'
		WHERE p.post_type = 'post' AND p.post_status = 'publish' {$addWhere} {$orderBy} {$limit}";

		$result = $this->db->get_results($sql, ARRAY_A);

		return $result;
	}

	public function getPagination($currentPage = 1)
	{
		$totalItems = $this->getCountPosts();
		$itemsPerPage = $this->getPageStep();
		$currentPage = (int)$currentPage;
		$urlPattern = 'javascript:mod.getData(\'(:num)\')';

		$paginator = new Paginator($totalItems, $itemsPerPage, (int)$currentPage, $urlPattern);

		return $paginator->toHtml();
	}

	public function getCountPosts()
	{
		$addWhere = $this->getSearch();
		$addWhere = !empty($addWhere)? "AND ({$addWhere})" : '';
		$sql = "SELECT COUNT(p.ID) FROM {$this->tbPost} p WHERE p.post_type = 'post' AND p.post_status = 'publish' {$addWhere}";

		return $this->db->get_var($sql);
	}

	public function setOrderBy($orderBy = '')
	{
		if (!empty($orderBy)) {
			$dataOrderBy = [];
			$data_parseOrderBy = explode(',', trim($orderBy));
			$dataOrderBy['field'] = self::checkField($data_parseOrderBy[0]) ? $this->escapeStr($data_parseOrderBy[0]) : '';
			$dataOrderBy['direction'] = (trim($data_parseOrderBy[1]) == 'ASC') ? 'ASC' : 'DESC';

			if(($dataOrderBy['field'] == 'likes'))
				$orderBy = "pm.meta_value {$dataOrderBy['direction']}";
			elseif (!empty($dataOrderBy['field'])) {
				$orderBy = "p.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
			} else {
				$orderBy = 'p.ID DESC';
			}

			$this->sqlOrderBy = $orderBy;
		}
	}

	protected function getOrderBy()
	{
		return $this->sqlOrderBy;
	}

	public function setSqlLimit($page = 1, $offset = 0)
	{
		$dataStep = [];
		$page = (int)$page;
		if($page) {
			$pageStep = (int)$offset? $offset : (int)$this->getPageStep();

			$dataStep['from'] = ($page == 1)? 0 : (($page * $pageStep) - $pageStep);
			$dataStep['offset'] = $pageStep;
		}

		if(!empty($dataStep)){
			$this->sqlLimit = "{$dataStep['from']},{$dataStep['offset']}";
		}

		return $this;
	}

	public function getPageStep()
	{
		return $this->_pageStep;
	}

	protected function getSqlLimit()
	{
		return empty($this->sqlLimit)? '0,' . (int)$this->getPageStep() : $this->sqlLimit;
	}

	public function setSearch($word = '')
	{
		if(!empty($word)) {
			$word = $this->escapeStr($word);

			$addSearch = " p.ID = {$this->toInt($word)}
			OR p.post_title LIKE '%{$word}%'";

			$this->sqlSearch = $addSearch;
		}

		return $this;
	}

	protected function getSearch()
	{
		return $this->sqlSearch;
	}

	public static function checkField($field = '')
	{
		$arr = [ 'ID', 'post_title', 'likes'];
		return in_array($field, $arr);
	}

	public function installTb()
	{
		$this->db->query($this->sgl_tbLikePost());
		$this->db->query($this->sgl_tbLikeComment());
	}

	public function uninstallTb()
	{
		$this->db->query("DROP TABLE IF EXISTS {$this->tbLikePost}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbLikeComment}");
	}

	private function sgl_tbLikePost()
	{
		return "CREATE TABLE IF NOT EXISTS `{$this->tbLikePost}` (
			`post_id` BIGINT(20) NOT NULL DEFAULT '0',
			`user_id` BIGINT(20) NOT NULL DEFAULT '0',
			`ip` VARCHAR(50) NOT NULL DEFAULT '',
			`time` TIMESTAMP NULL DEFAULT NULL,
			PRIMARY KEY (`post_id`, `user_id`, `ip`)
		)
		COLLATE=utf8_general_ci
		ENGINE=InnoDB
		";
	}
	private function sgl_tbLikeComment()
	{
		return "CREATE TABLE IF NOT EXISTS `{$this->tbLikeComment}` (
			`comment_id` BIGINT(20) NOT NULL DEFAULT '0',
			`user_id` BIGINT(20) NOT NULL DEFAULT '0',
			`ip` VARCHAR(50) NOT NULL DEFAULT '',
			`time` TIMESTAMP NULL DEFAULT NULL,
			PRIMARY KEY (`comment_id`, `user_id`, `ip`)
		)
		COLLATE=utf8_general_ci
		ENGINE=InnoDB
		";
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}