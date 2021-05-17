<?php
namespace EndorseSkill;

class Module extends Base
{
	protected static $_instance = null;

	protected $varsTpl = [];

	protected $sqlOrderBy = 's.id DESC';
	protected $sqlSearch = '';
	protected $sqlLimit = '';
	protected $_pageStep = 30;

	public function __construct()
	{
		parent::__construct();

//		$pathTpl = ENDORSE_SKILL_DIR . 'module/tpl/';
		$pathTpl = ENDORSE_SKILL_DIR . 'tpl/module/';
		$pathCache = $pathTpl . 'cache';
		if(!file_exists($pathCache)){
			mkdir($pathCache, 0755, true);
		}

		$this->fenom = \Fenom::factory($pathTpl, $pathCache);
		$this->fenom->setOptions(\Fenom::AUTO_RELOAD);
		//$this->fenom->setOptions(Fenom::DISABLE_CACHE);// - откл. кэширование

		$moduleUrl = '/wp-admin/admin.php?page=endorse_skill';

		$this->setLangTag('en');
		$this->setLangPath('lang/module');

		$this->varsTpl['PATH_INC'] = ENDORSE_SKILL_RELATIVE;
		$this->varsTpl['MODULE_URL'] = $moduleUrl;
		$this->varsTpl['VERSION'] = self::VERSION;
		$this->varsTpl['lang'] = $this->getLang('ALL');
	}

	public function actionIndex()
	{

		$this->varsTpl['skills'] = $this->getListSkills();
		$this->varsTpl['pagination'] = $this->getPagination();
		$this->fenom->display('main.tpl', $this->varsTpl);

		exit;
	}

	public function actionCreateSkill()
	{
		$skill = new Skill();
		if($skill->create($_POST)){
			self::outputJSON($this->getLang('created'), 1);
		}

		$msg = $this->getLang('error') . ' ' . $skill->getError();

		self::outputJSON($msg);
	}

	public function actionEditSkill()
	{
		$skill = new Skill();
		if($skill->update($_POST)){
			self::outputJSON($this->getLang('updated'), 1);
		}

		$msg = $this->getLang('error') . ' ' . $skill->getError();

		self::outputJSON($msg);
	}

	public function actionGetSkill()
	{
		$skill = new Skill();

		$data = $skill->get($_POST['id']);
		if(!empty($data))
			self::outputJSON(['item' => $data], 1);
		else
			self::outputJSON();
	}

	public function actionDeleteSkill()
	{
		$skill = new Skill();
		if($skill->delete($_POST['skId'])){
			self::outputJSON('', 1);
		}
		$msg = $this->getLang('error') . ' ' . $skill->getError();
		self::outputJSON($msg);
	}

	public function actionGetList()
	{
		$this->setSearch($_POST['search']);
		$this->setSqlLimit($_POST['page']);
		$this->setOrderBy($_POST['orderBy']);

		$this->varsTpl['skills'] = $this->getListSkills();

		$result['list'] = $this->fenom->fetch('list_skill.tpl', $this->varsTpl);
		$result['pagination'] = $this->getPagination($_POST['page']);

		self::outputJSON($result, 1);
	}

	public function getListSkills()
	{
		$addWhere = $this->getSearch();
		$addWhere = !empty($addWhere)? "WHERE {$addWhere}" : '';

		$orderBy = "ORDER BY {$this->getOrderBy()}";
		$limit = $this->getPageStep()? "LIMIT {$this->getSqlLimit()}" : '';

		$sql = "SELECT s.*, (SELECT COUNT(e.user_endorse) FROM {$this->tbEndorseSkill} e WHERE e.skill_id = s.id) as used
		FROM {$this->tbSkill} s
		{$addWhere} {$orderBy} {$limit}";

		return $this->db->get_results($sql, ARRAY_A);
	}

	public function getPagination($currentPage = 1)
	{
		$totalItems = $this->getCountSkills();
		$itemsPerPage = $this->getPageStep();
		$currentPage = (int)$currentPage;
		$urlPattern = 'javascript:mod.getData(\'(:num)\')';

		$paginator = new Paginator($totalItems, $itemsPerPage, (int)$currentPage, $urlPattern);

		return $paginator->toHtml();
	}

	public function getCountSkills()
	{
		$addWhere = $this->getSearch();
		$addWhere = !empty($addWhere)? "WHERE {$addWhere}" : '';
		$sql = "SELECT COUNT(s.id) FROM {$this->tbSkill} s {$addWhere}";

		return $this->db->get_var($sql);
	}

	public function setOrderBy($orderBy = '')
	{
		if (!empty($orderBy)) {
			$dataOrderBy = [];
			$data_parseOrderBy = explode(',', trim($orderBy));
			$dataOrderBy['field'] = self::checkField($data_parseOrderBy[0]) ? $this->escapeStr($data_parseOrderBy[0]) : '';
			$dataOrderBy['direction'] = (trim($data_parseOrderBy[1]) == 'ASC') ? 'ASC' : 'DESC';

			if(($dataOrderBy['field'] == 'used'))
				$orderBy = "{$dataOrderBy['field']} {$dataOrderBy['direction']}";
			elseif (!empty($dataOrderBy['field'])) {
				$orderBy = "s.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
			} else {
				$orderBy = 's.id DESC';
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

//		return $this;
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

			$addSearch = " s.id = {$this->toInt($word)}
			OR s.group_skill LIKE '%{$word}%'
			OR s.title LIKE '%{$word}%'";

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
		$arr = [ 'id', 'name', 'title', 'used', ];
		return in_array($field, $arr);
	}

	public function installTb()
	{
		$this->db->query($this->sgl_tbSkill());
		$this->db->query($this->sgl_tbSkillUsers());
		$this->db->query($this->sgl_tbEndorseSkill());
	}

	public function updateTb()
	{
		if(self::VERSION == '1.1') {
			$this->db->get_var("SELECT user_id FROM {$this->tbSkill} LIMIT 1");
			if($this->db->last_error) {
				$sql = "ALTER TABLE `{$this->tbSkill}` ADD COLUMN `user_id` BIGINT(20) NULL DEFAULT '0' AFTER `order`";
				$this->db->query($sql);
			}
		}
	}

	public function uninstallTb()
	{
		$this->db->query("DROP TABLE IF EXISTS {$this->tbSkill}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbSkillUsers}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbEndorseSkill}");
	}

	private function sgl_tbSkill()
	{
		return "CREATE TABLE IF NOT EXISTS `{$this->tbSkill}` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`group_skill` VARCHAR(20) NULL DEFAULT 'freelancer',
			`title` VARCHAR(50) NULL DEFAULT NULL,
			`order` INT(11) NOT NULL DEFAULT '0',
			`user_id` BIGINT(20) NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		";
	}

	private function sgl_tbSkillUsers()
	{
		return "CREATE TABLE IF NOT EXISTS `{$this->tbSkillUsers}` (
			`skill_id` BIGINT(20) NOT NULL,
			`user_id` BIGINT(20) NOT NULL,
			PRIMARY KEY (`skill_id`, `user_id`),
			CONSTRAINT `FK_skill_users` FOREIGN KEY (`skill_id`)
				REFERENCES `{$this->tbSkill}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		";
	}

	private function sgl_tbEndorseSkill()
	{
		return "CREATE TABLE IF NOT EXISTS `{$this->tbEndorseSkill}` (
			`user_id` BIGINT(20) NOT NULL,
			`skill_id` BIGINT(20) NOT NULL,
			`user_endorse` BIGINT(20) NOT NULL,
			PRIMARY KEY (`skill_id`, `user_id`, `user_endorse`),
			CONSTRAINT `FK_skill_to_endorse` FOREIGN KEY (`skill_id`)
				REFERENCES `{$this->tbSkill}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COLLATE='utf8_general_ci'
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