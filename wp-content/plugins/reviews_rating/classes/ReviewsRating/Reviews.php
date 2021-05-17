<?php
namespace ReviewsRating;

class Reviews extends Base
{
	protected static $_instance = null;

	const STATUS_PENDING = 'pending';
	const STATUS_APPROVED = 'approved';
	const STATUS_NOT_APPROVED = 'not_approved';
	const STATUS_HIDDEN = 'hidden';

	public $listStatuses = [
			self::STATUS_PENDING,
			self::STATUS_NOT_APPROVED,
			self::STATUS_APPROVED,
			self::STATUS_HIDDEN,
	];/*[
		'pending',
		'not_approved',
		'approved',
		'hidden',
	];*/

	protected $sqlLimit = '';

	protected $docId = 0;
	protected $userIdForRating = 0;

	public $logger = null;

	protected $_limitOffset = 20;

	public function __construct($docId = 0)
	{
		$this->docId = (int)$docId;

		Log::getInstance()->logging(1);

//		$this->logger = Log::getInstance();
		parent::__construct();
	}

	public function setDocId($docId = 0)
	{
		$this->docId = (int)$docId;

		return $this;
	}

	public function setUserIdForRating($userIdForRating = 0)
	{
		$this->userIdForRating = (int)$userIdForRating;

		return $this;
	}

	public function isAccess($data = [])
	{
		global $user_ID;

		$userId = !empty($user_ID)? (int)$user_ID : 0;

		if(!$userId){
			Log::getInstance()->addLog('document', $this->getLang('user_not_auth'));
			return false;
		}

		if($this->isResourceNotExists($this->docId)){
			Log::getInstance()->addLog('document', $this->getLang('doc_not_found'));
			return false;
		}

		if(isset($data['parent'])) return true;

		if($this->isReviewExists($this->docId, $userId)){
			Log::getInstance()->addLog('review', $this->getLang('review_exist'));
			return false;
		}

		return true;
	}

	public function create($data = [])
	{
		$ins = [];
		if(!$this->toInt($this->docId)) {
			Log::getInstance()->addLog('docId', $this->getLang('doc_id_empty'));
		}


		if(empty($data['vote'])) {
			Log::getInstance()->addLog('vote', $this->getLang('vote_empty'));
		} else {
		    // skip voting for client's reply to freelancer's review
		    if ($data['vote'] != 'skip'){
                if ($this->toInt($data['vote']) > $this->stars)
                    $data['vote'] = $this->stars;
                else
                    $data['vote'] = $this->toInt($data['vote']);
            }
		}
		if(empty($data['username'])) {
			Log::getInstance()->addLog('username', $this->getLang('username_empty'));
		}
//		if(empty($data['title'])) {
//			Log::getInstance()->addLog('title', $this->getLang('title_empty'));
//		}
//		if(empty($data['comment'])) {
//			Log::getInstance()->addLog('comment', $this->getLang('comment_empty'));
//		}

		if(Log::getInstance()->getLog()){
			return false;
		}

		$ins['for_user_id'] = $this->userIdForRating;
		$ins['doc_id'] = $this->docId;
		$ins['created'] = self::getTimestamp();
		$ins['is_admin'] = isset($data['is_admin'])? 1 : 0;
		$ins['vote'] = $data['vote'];
		$ins['user_id'] = (int)$data['user_id'];
		$ins['email'] = $this->escapeStr($data['email']);
		$ins['username'] = $this->escapeStr($data['username']);
		$ins['title'] = $this->escapeStr($data['title']);
		$ins['comment'] = $this->escapeStr($data['comment']);
		$ins['parent'] = (int)$data['parent'];
		if(empty($data['status']))
			$ins['status'] = $this->forNewIsPublish() ? self::STATUS_APPROVED : self::STATUS_PENDING;
		else
			$ins['status'] = in_array($data['status'], $this->getListStatuses())? $this->escapeStr($data['status']) : self::STATUS_PENDING;

		if(!empty($data['additional_data']))
			$ins['additional_data'] = json_encode($data['additional_data']);

		$idReview = $this->db->insert($this->tbReviewsDetails, $ins);
		if($idReview) {
			if(!$ins['parent'] && $ins['status'] == self::STATUS_APPROVED) {
                // skip voting for client's reply to freelancer's review
                if ($data['vote'] != 'skip'){
                    $this->addVote($ins['vote']);
                }
			}

			return $idReview;
		}

		return false;
	}

	public function forNewIsPublish()
	{
		return (bool) $this->getParamConfig('new_review_publish');
	}

	public function getListStatuses()
	{
		return $this->listStatuses;/*[
			self::STATUS_PENDING,
			self::STATUS_NOT_APPROVED,
			self::STATUS_APPROVED,
			self::STATUS_HIDDEN,
		];*/
	}

	public function addVote($vote = 0)
	{
		$rating = $this->getRating($this->userIdForRating);
		$vote = (int)$vote;
		if (!empty($rating)) {
			$set['total'] = (int)$rating['total'] + $vote;
			$set['votes'] = (int)$rating['votes'] + 1;

			$rating = ($set['total'] > 0 && $set['votes'] > 0)? $set['total'] / $set['votes'] : 0;
			$set['rating'] = number_format($rating, 2);

			return $this->db->update($this->tbReviews, $set, ['user_id' => $this->userIdForRating]);
		} else {
			$set['user_id'] = $this->userIdForRating;
			$set['total'] = $vote;
			$set['votes'] = 1;
			$set['rating'] = $vote;

			return $this->db->insert($this->tbReviews, $set);
		}
	}

	public function subtractVote($vote = 0, $userIdForRating = 0)
	{
		$userIdForRating = !empty($userIdForRating)? (int)$userIdForRating : $this->userIdForRating;
		$rating = $this->getRating($userIdForRating);
		$vote = (int)$vote;
		if (!empty($rating)) {
			$set['total'] = ($rating['total'] > 0)? (int)$rating['total'] - $vote : 0;
			$set['votes'] = ($rating['votes'] > 0)? (int)$rating['votes'] - 1 : 0;

			$rating = ($set['total'] > 0 && $set['votes'] > 0)? $set['total'] / $set['votes'] : 0;
			$set['rating'] = number_format($rating, 2);

			$this->db->update($this->tbReviews, $set, ['user_id' => $userIdForRating]);
		}
	}

	public function resetRating()
	{
		$upd['total'] = 0;
		$upd['votes'] = 0;
		$upd['rating'] = 0;
		if($this->db->update($this->tbReviews, $upd, ['user_id' => $this->toInt($this->userIdForRating)])){
			return $this->db->delete($this->tbReviewsDetails, ['for_user_id' => $this->toInt($this->userIdForRating)]);
		}

		return false;
	}

	public function deleteReview($id = 0)
	{
		if($rw = $this->getReview($id)){
			if($this->db->delete($this->tbReviewsDetails, ['id' => $this->toInt($id)])) {
				if($rw['status'] == self::STATUS_APPROVED) {
					$this->subtractVote($rw['vote']);
				}
				return true;
			}
		}

		return false;
	}

	public function setStatus($id = 0, $status = '')
	{
		if(empty($status)){
			Log::getInstance()->addLog('status', $this->getLang('status_empty'));
			return false;
		}

		if(in_array($status,$this->getListStatuses())){
			return $this->db->update($this->tbReviewsDetails, ['status' => $this->escapeStr($status)], ['id' => $this->toInt($id)]);
		}

		Log::getInstance()->addLog('status', $this->getLang('status_incorrect') . " - $status");
		return false;
	}

	public function getRating($userId = 0)
	{
		$userId = !empty($this->userIdForRating)? $this->userIdForRating : $userId;
		return $this->db->get_row("SELECT * FROM {$this->tbReviews} WHERE user_id = {$this->toInt($userId)}", ARRAY_A);
	}

	public function getReview($id = 0)
	{
		$rw = $this->db->get_row("SELECT * FROM {$this->tbReviewsDetails} WHERE id = {$this->toInt($id)}", ARRAY_A);

		if(empty($this->docId)){
			$this->setDocId($rw['doc_id']);
		}

		return $rw;
	}

	public function getReviewDoc($id = 0)
	{
		return $this->db->get_row("SELECT * FROM {$this->tbReviewsDetails} WHERE doc_id = {$this->toInt($id)}", ARRAY_A);
	}

    public function getReviewReply($project_id = 0)
    {
        // filter by project_id
        return $this->db->get_row("SELECT * FROM {$this->tbReviewsDetails} WHERE parent = {$this->toInt($project_id)} AND additional_data LIKE '%is_reply%'", ARRAY_A);
    }

	public function getDocReview($id = 0)
	{
		return $this->db->get_row("SELECT * FROM {$this->tbPosts} WHERE id = {$this->toInt($id)}", ARRAY_A);
	}

	public function getReviewsDoc($docId = 0, $onlyPublish = 1, $limit = '3')
	{
		$list = [];
		$status = $onlyPublish? " AND status NOT IN ('pending', 'not_approved')" : '';
		$limit = !empty($limit)? "LIMIT {$limit}" : '';
		$result = $this->db->get_results("SELECT * FROM {$this->tbReviewsDetails} WHERE doc_id = {$this->toInt($docId)} {$status} ORDER BY created DESC {$limit}", ARRAY_A);
		foreach($result as $row)
		{
			$row['additional_data'] = !empty($row['additional_data'])? json_decode($row['additional_data'], 1) : [];
			$list[] = $row;
		}

		return $list;
	}

	public function getReviewsUser($userId = 0, $onlyPublish = 1)
	{
		$list = [];
		$status = $onlyPublish? " AND status = 'approved'" : '';
		$limit = "LIMIT {$this->getSqlLimit()}";
		$sql = "SELECT users.display_name as author_project, rw.*, doc.*,
		IF(doc.post_type = '" . PROJECT . "', doc.guid, (SELECT guid FROM {$this->tbPosts} WHERE id = doc.post_parent)) as guid
		FROM {$this->tbReviewsDetails} rw
 		LEFT JOIN {$this->tb_prefix}users as users ON users.ID = rw.user_id
 		LEFT JOIN {$this->tbPosts} doc ON doc.id = rw.doc_id
		WHERE rw.for_user_id = {$this->toInt($userId)} AND comment != '' {$status} ORDER BY rw.created DESC {$limit}";

		$result = $this->db->get_results($sql, ARRAY_A);
		foreach($result as $row)
		{
			$row['additional_data'] = !empty($row['additional_data'])? json_decode($row['additional_data'], 1) : [];
			$list[] = $row;
		}

		return $list;
	}

	public function getCountReviews($userId = 0, $onlyPublish = 1)
	{
		$addWhere = !empty($userId)? " AND for_user_id = {$this->toInt($userId)}" : '';
		$status = $onlyPublish? " AND status = 'approved'" : '';
		$sql = "SELECT COUNT(id) FROM {$this->tbReviewsDetails} WHERE parent = 0 AND comment != '' {$addWhere} {$status}";
		return $this->db->get_var($sql);
	}

	public function getRwPagination($total, $currentPage = 1, $isNoWrap = false, $itemsPerPage = 0)
	{
		$totalItems = (int)$total;
		$currentPage = (int)$currentPage;
		$urlPattern = 'rwRating.getDataRw(\'(:num)\')';
		$itemsPerPage = ($itemsPerPage)? $itemsPerPage : $this->_limitOffset;

		$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
		if($isNoWrap){
			$paginator->unsetWrapUl();
		}

		$paginator->setWrapCSSClass('pagination-reviews');

		return $paginator->toHtml();
	}

	private function isResourceNotExists($id)
	{
		$id = (int) $id;

		$query = $this->db->get_var("SELECT id FROM {$this->tbPosts} WHERE id = {$id} AND post_type = 'project' OR id = {$id} AND post_type = 'bid'");

		return ((int)$query > 0) ? false : true;
	}

	public function isReviewNotExists($id = 0, $userId = 0)
	{
		$id = (int) $id;
		$sql = "SELECT id FROM {$this->tbReviewsDetails} WHERE doc_id = {$id} AND user_id = {$userId} AND parent = 0";
		$query = $this->db->get_var($sql);

		return ((int)$query > 0) ? false : true;
	}

	public function isReviewExists($id = 0, $userId = 0)
	{
		return !$this->isReviewNotExists($id, $userId);
	}

	public function setLimitOffset($limitOffset = 0)
	{
		$this->_limitOffset = $this->toInt($limitOffset)? $this->toInt($limitOffset) : 10;
	}

	public function setSqlLimit($page = 1, $offset = 0)
	{
		$dataStep = [];
		$page = (int)$page;
		if($page) {
			$pageStep = (int)$offset? $offset : (int)$this->_limitOffset;

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
		return empty($this->sqlLimit)? '0,' . (int)$this->_limitOffset : $this->sqlLimit;
	}

	public function getPercentPayReview()
	{
		return $this->toFloat($this->getParamConfig('percent_pay_review'));
	}

	public function getMinPayReview()
	{
		return $this->toFloat($this->getParamConfig('min_pay_review'));
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}