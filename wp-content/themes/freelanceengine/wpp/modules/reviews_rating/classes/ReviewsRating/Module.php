<?php
namespace ReviewsRating;

class Module extends Base
{
	static $_instance = null;

	public $currentDoc = 0;

	protected $sqlRtOrderBy = 'rt.rating DESC';
	protected $sqlRwOrderBy = 'rw.created DESC';
	protected $sqlLimit = '';
	protected $sqlRtSearch = '';
	protected $sqlRwSearch = '';

	public function run($action) {
		$action = $action . 'Action';
		if (!method_exists($this, $action)) {
			return null;
		}

		$output = $this->{$action}();

		if ($output === null) {
			return null;
		}

		if (is_array($output)) {
			self::outputJSON($output, 1);
		}

		return $output;
	}

	public function searchDoc($str)
	{
		$idTplDoc = $this->getParamConfig('idTplDoc');
		$data = [];
		if(empty($str) || strlen($str)<3)
			return $data;

		$q = $this->db->query("SELECT c.id, c.pagetitle, rt.rating, rt.votes FROM {$this->tbContent} c
		LEFT JOIN {$this->tbReviews} rt ON rt.doc_id = c.id
		WHERE c.pagetitle LIKE '%{$str}%' AND c.template = {$this->toInt($idTplDoc)} AND c.isfolder = 0 AND deleted = 0");

		while($row = $this->db->getRow($q)){
			$data[] = [
				'id' => $row['id'],
				'text' => "{$row['pagetitle']}(ID:{$row['id']})"
						. "- {$this->getLang('rating')}{$row['rating']} {$this->getLang('votes')}{$row['votes']}",
			];
		}

		return $data;
	}

	public function getListDoc()
	{
		$idTplDoc = $this->getParamConfig('idTplDoc');
		$data = [];
//		$q = $this->db->query("SELECT c.id, c.pagetitle, IF(rt.rating is NULL, '0.0', rt.rating) as rating, rt.votes FROM {$this->tbContent} c
//		LEFT JOIN {$this->tbReviews} rt ON rt.doc_id = c.id
//		WHERE c.template = {$this->toInt($idTplDoc)} AND c.isfolder = 0 AND deleted = 0");
//
//		while($row = $this->db->getRow($q)){
//			$data[] = [
//				'id' => $row['id'],
//				'text' => "{$row['pagetitle']}(ID:{$row['id']})"
//						. "- {$this->getLang('rating')} {$this->toFloat($row['rating'])}, {$this->getLang('votes')} {$this->toInt($row['votes'])}",
//			];
//		}

		return $data;
	}

	public function updParamConfig($name, $value = '')
	{
		return $this->setParamConfig($name, $value);
	}

	public function updConfig($data = [])
	{
		$data['send_notice'] = isset($data['send_notice'])? 1 : 0;
		$data['new_review_publish'] = isset($data['new_review_publish'])? 1 : 0;
		$data['percent_pay_review'] = number_format(floatval($data['new_review_publish']), 2);

		return $this->setParamConfig($data);
	}

	private function setParamConfig($name, $value = '')
	{
		if(empty($name)) return false;

		if(is_array($name)){
			foreach ($name as $item => $val) {
				$item = $this->escapeStr($item);

				if(in_array($item, Config::$list)) {
					$val = $this->escapeStr($val);

					$this->db->query("INSERT {$this -> tbConfig} (name,value) VALUES ('{$item}', '{$val}')
					ON DUPLICATE KEY UPDATE value = VALUES(value)
					");
					$this->_config[$item] = $val;
				}
			}

			return true;
		} else {
			$name = $this->escapeStr($name);
			if(in_array($name, Config::$list)) {
				$val = ($name == 'send_notice' || $name == 'new_review_publish')? $this->toInt($value) : $this->escapeStr($value);
				$this->db->query("INSERT {$this -> tbConfig} (name,value) VALUES ('{$name}', '{$val}')
				ON DUPLICATE KEY UPDATE value = VALUES(value)
				");

				$this->_config[$name] = $value;

				return true;
			}
		}

		return false;
	}

	public function getListRatings()
	{
		$addWhere = $this->getRtSearch();
		$addWhere = !empty($addWhere)? "WHERE {$addWhere}" : '';

		$orderBy = "ORDER BY {$this->getRtOrderBy()}";

		$limit = "LIMIT {$this->getSqlLimit()}";

		$sql = "SELECT rt.*, p.post_title as pagetitle, p.guid,
		(SELECT COUNT(rw.id) FROM {$this->tbReviewsDetails} rw WHERE rw.for_user_id = rt.user_id AND rw.parent = 0) as countReviews
		FROM {$this->tbReviews} rt
		LEFT JOIN {$this->tbPosts} p ON p.post_author = rt.user_id AND p.post_type = 'fre_profile'

				{$addWhere} {$orderBy} {$limit}";

//		die($sql);

		$reviews = $this->db->get_results($sql, ARRAY_A);
//		$reviews = [];
//		foreach($rows as $row)
//		{
//			$row['countReviews'] = $this->getCountReviews($row['doc_id']);
//			$reviews[] = $row;
//		}

		return !empty($reviews)? $reviews : [];
	}

	public function getListReviews($docId = 0)
	{
		$addWhere = !empty($docId)? " rw.for_user_id = {$this->toInt($docId)}" : $this->getRwSearch();
//		$addWhere =  $this->getRwSearch();
		$addWhere = !empty($addWhere)? "WHERE {$addWhere}" : '';

		$orderBy = "ORDER BY {$this->getRwOrderBy()}";

		$limit = "LIMIT {$this->getSqlLimit()}";

		$sql = "SELECT rw.*, u.display_name as author_review, p.post_title as pagetitle,
 		IF(p.post_type = 'bid', (SELECT p2.guid FROM {$this->tbPosts} p2 WHERE p2.id = p.post_parent AND p2.post_type = 'project'), p.guid) as guid
		FROM {$this->tbReviewsDetails} rw
		LEFT JOIN {$this->tbPosts} p ON p.id = rw.doc_id
		LEFT JOIN {$this->tb_prefix}users u ON u.id = rw.user_id
				{$addWhere} {$orderBy} {$limit}";

//		die($sql);

		$rows = $this->db->get_results($sql, ARRAY_A);
		$reviews = [];
//		while($row = $this->db->getRow($q)){
		if(!empty($rows)) {
			foreach ($rows as $row) {

				$row['countAnswers'] = $this->getCountAnswers($row['doc_id']);
				$reviews[] = $row;
			}
		}

		return $reviews;
	}

	public function getCountRatings()
	{
		$addWhere = $this->getRtSearch();
		$addWhere = !empty($addWhere)? "WHERE {$addWhere}" : '';
		$sql = "SELECT COUNT(rt.user_id) FROM {$this->tbReviews} rt {$addWhere}";

		return $this->db->get_var($sql);
	}

	public function getCountReviews($docId = 0)
	{
		$addWhere = $this->getRwSearch();
		$addWhere.= !empty($docId)? " AND rw.user_id = {$this->toInt($docId)}" : '';
		$sql = "SELECT COUNT(rw.id) FROM {$this->tbReviewsDetails} rw WHERE rw.parent = 0 "
				. (!empty($addWhere)? " AND " . $addWhere : $addWhere);
		return $this->db->get_var($sql);
	}

	public function getCountAnswers($docId = 0)
	{
		$sql = "SELECT COUNT(id) FROM {$this->tbReviewsDetails} WHERE parent = {$this->toInt($docId)}";
		return $this->db->get_var($sql);
	}

	public function setRtOrderBy($orderBy = '')
	{
		if (!empty($orderBy)){
			$dataOrderBy = [];
			$data_parseOrderBy = explode(',', trim($orderBy));
			$dataOrderBy['field'] = self::checkRtField($data_parseOrderBy[0]) ? $this->escapeStr($data_parseOrderBy[0]) : '';
			$dataOrderBy['direction'] = (trim($data_parseOrderBy[1]) == 'ASC') ? 'ASC' : 'DESC';

			if (!empty($dataOrderBy['field'])) {
				if(($dataOrderBy['field'] == 'countReviews'))
					$orderBy = "{$dataOrderBy['field']} {$dataOrderBy['direction']}";
				elseif(($dataOrderBy['field'] == 'post_title'))
					$orderBy = "p.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
				else
					$orderBy = "rt.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
			} else {
				$orderBy = 'rt.rating DESC';
			}

			$this->sqlRtOrderBy = $orderBy;
		}

		return $this;
	}

	public function setRwOrderBy($orderBy = '')
	{
		if (!empty($orderBy)) {
			$dataOrderBy = [];
			$data_parseOrderBy = explode(',', trim($orderBy));
			$dataOrderBy['field'] = self::checkRwField($data_parseOrderBy[0]) ? $this->escapeStr($data_parseOrderBy[0]) : '';
			$dataOrderBy['direction'] = (trim($data_parseOrderBy[1]) == 'ASC') ? 'ASC' : 'DESC';

			if (!empty($dataOrderBy['field'])) {
				$orderBy = "rw.{$dataOrderBy['field']} {$dataOrderBy['direction']}";
			} else {
				$orderBy = 'rw.created DESC';
			}

			$this->sqlRwOrderBy = $orderBy;
		}

//		return $this;
	}

	protected function getRtOrderBy()
	{
		return $this->sqlRtOrderBy;
	}

	protected function getRwOrderBy()
	{
		return $this->sqlRwOrderBy;
	}

	public function setSqlLimit($page = 1, $offset = 0)
	{
		$dataStep = [];
		$page = (int)$page;
		if($page) {
			$pageStep = (int)$offset? $offset : (int)$this->getParamConfig('page_step');

			$dataStep['from'] = ($page == 1)? 0 : (($page * $pageStep) - $pageStep);
			$dataStep['offset'] = $pageStep;
		}

		if(!empty($dataStep)){
			$this->sqlLimit = "{$dataStep['from']},{$dataStep['offset']}";
		}

//		return $this;
	}

	protected function getSqlLimit()
	{
		return empty($this->sqlLimit)? '0,' . (int)$this->getParamConfig('page_step') : $this->sqlLimit;
	}

	public function setRtSearch($word = '')
	{
		if(!empty($word)) {
			$word = $this->escapeStr($word);

			$addSearch = " rt.id = {$this->toInt($word)}
			OR rt.user_id = {$this->toInt($word)}
			OR rt.votes = {$this->toInt($word)}
			OR rt.rating LIKE '%{$word}%'
			OR p.post_title LIKE '%{$word}%'";

			$this->sqlRtSearch = $addSearch;
		}

		return $this;
	}
	public function setRwSearch($word = '', $docId = 0)
	{
		$docId = $docId ? (int)$docId : (int)$_GET['doc'];
		$andDoc = $docId? " AND rw.doc_id = {$docId}" : '';

		if(!empty($word)) {
			$word = $this->escapeStr($word);

			$addSearch = " rw.user_id = {$this->toInt($word)} {$andDoc}
			OR rw.username LIKE '%{$word}%' {$andDoc}
			OR rw.title LIKE '%{$word}%' {$andDoc}
			OR p.post_title LIKE '%{$word}%' {$andDoc}
			OR rw.vote = {$this->toInt($word)} {$andDoc}"
			. (empty($andDoc)? "OR u.display_name LIKE '%{$word}%'" : '');

			$this->sqlRwSearch = $addSearch;
		}

		return $this;
	}

	public function deleteReview()
	{

	}

	protected function getRtSearch()
	{
		return $this->sqlRtSearch;
	}

	protected function getRwSearch()
	{
		return $this->sqlRwSearch;
	}

	public function getRtPagination($currentPage = 1)
	{
		$totalItems = $this->getCountRatings();
		$itemsPerPage = $this->getPageStep();
		$currentPage = (int)$currentPage;
		$urlPattern = 'javascript:mod.getDataRt(\'(:num)\')';

		$paginator = new Paginator($totalItems, $itemsPerPage, (int)$currentPage, $urlPattern);

		return $paginator->toHtml();
	}

	public function getRwPagination($currentPage = 1)
	{
		$totalItems = $this->getCountReviews();
		$itemsPerPage = $this->getPageStep();
		$urlPattern = 'javascript:mod.getDataRw(\'(:num)\')';

		$paginator = new Paginator($totalItems, $itemsPerPage, (int)$currentPage, $urlPattern);

		return $paginator->toHtml();
	}

	public function getPageStep()
	{
		return $this->getParamConfig('page_step');
	}

	public function installAction()
	{

	}

	public function updateAction()
	{

	}

	public static function checkRtField($field = '')
	{
		$arr = [
				'user_id',
				'votes',
				'rating',
				'post_title',
				'countReviews',
		];
		return in_array($field, $arr);
	}

	public static function checkRwField($field = '')
	{
		$arr = [
				'doc_id',
				'created',
				'updated',
				'title',
				'username',
				'user_id',
				'vote',
		];
		return in_array($field, $arr);
	}

	public function installTb()
	{
		$this->db->query($this->sql_tbConfig());
		$this->db->query($this->sql_tbConfigDefault());
		$this->db->query($this->sql_tbReviews());
		$this->db->query($this->sql_tbReviewsDetails());
		$this->db->query($this->sql_tbReviewsStatus());

//		$triggerReviews = 'triggerReviews';
//		$this->db->query("DROP TRIGGER IF EXISTS {$triggerReviews}");
//		$this->db->query($this->sql_triggerReviews($triggerReviews));
	}

	public function uninstallTb()
	{
		$this->db->query("DROP TABLE IF EXISTS {$this->tbConfig}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbReviews}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbReviewsDetails}");
		$this->db->query("DROP TABLE IF EXISTS {$this->tbReviewsStatus}");
//		$triggerReviews = 'triggerReviews';
//		$this->db->query("DROP TRIGGER IF EXISTS {$triggerReviews}");
	}

	public function sql_tbConfig()
	{
		$sql="
		CREATE TABLE IF NOT EXISTS `{$this->tbConfig}` (
			`name` VARCHAR(100) NOT NULL ,
			`value` TEXT NULL ,
			PRIMARY KEY (`name`)
		)
		 COLLATE = utf8_general_ci
		 ENGINE = InnoDB
		";
		return $sql;
	}

	public function sql_tbConfigDefault()
	{
		$sql="INSERT INTO `{$this->tbConfig}` (name,value)
		VALUES ('VERSION','" . self::VERSION . "'),('email_moderator',''),('new_review_publish','1'),('send_notice','1'),('page_step','20')";
		return $sql;
	}

	public function sql_tbReviews()
	{
		$sql="
		CREATE TABLE IF NOT EXISTS `{$this->tbReviews}` (
			`user_id` BIGINT(20) NOT NULL,
			`total` BIGINT(20) NULL DEFAULT '0',
			`votes` INT(11) NULL DEFAULT '0',
			`rating` FLOAT NULL DEFAULT '0',
			PRIMARY KEY (`user_id`)
		)
		COLLATE = utf8_general_ci
		ENGINE=InnoDB
		";
		return $sql;
	}

	public function sql_tbReviewsDetails()
	{
		$sql="
		CREATE TABLE IF NOT EXISTS `{$this->tbReviewsDetails}` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`for_user_id` BIGINT(20) NOT NULL DEFAULT '0',
			`doc_id` BIGINT(20) NOT NULL,
			`parent` BIGINT(20) NOT NULL DEFAULT '0',
			`user_id` BIGINT(20) NULL DEFAULT '0',
			`created` TIMESTAMP NULL DEFAULT NULL,
			`updated` TIMESTAMP NULL DEFAULT NULL,
			`is_admin` SMALLINT(1) NOT NULL DEFAULT '0',
			`status` VARCHAR(50) NULL DEFAULT 'pending',
			`vote` INT(11) NULL DEFAULT '0',
			`email` VARCHAR(50) NOT NULL DEFAULT '',
			`username` VARCHAR(100) NOT NULL DEFAULT '',
			`title` VARCHAR(100) NOT NULL DEFAULT '',
			`comment` LONGTEXT NOT NULL DEFAULT '',
			`additional_data` TEXT NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			INDEX `docid` (`for_user_id`,`doc_id`,`user_id`)
		)
		COLLATE = utf8_general_ci
		ENGINE=InnoDB
		";
		return $sql;
	}

	public function sql_tbReviewsStatus()
	{
		$sql="
		CREATE TABLE IF NOT EXISTS `{$this->tbReviewsStatus}` (
			`code` VARCHAR(50) NOT NULL,
			`title` VARCHAR(200) NULL DEFAULT '',
			`is_publish` SMALLINT(1) NULL DEFAULT '0',
			PRIMARY KEY (`code`)
		)
		COLLATE = utf8_general_ci
		ENGINE=InnoDB
		";
		return $sql;
	}

	public function sql_triggerReviews($triggerReviews = 'triggerReviews')
	{
		$sql="CREATE TRIGGER {$triggerReviews} AFTER DELETE ON {$this->tbPosts}
				FOR EACH ROW
				BEGIN
					DELETE FROM {$this->tbReviewsDetails}
					WHERE doc_id = OLD.ID;
				END;
		";
		return $sql;
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}