<?php
namespace LikesUsers;

class Like extends Base
{
	protected static $_instance = null;

	protected $_dataType = '';
	protected $_dataSourceId = '';
	protected $_dataUserId = '';

	public function handler($userId = 0, $sourceId = 0, $type = 'post')
	{
		$type = in_array($type, ['post', 'comment'])? $type : false;
		if($type === false){
			return false;
		}

		if(!$sourceId){
			$this->addError($this->getLang('undefined_'.$type));
			return false;
		}

		$checkSourceId = 'checkId' . ucfirst($type);
		if(method_exists($this, $checkSourceId)){
			if($this->$checkSourceId($sourceId)) {
				if ($userId) {
					$findUser = 'findFor' . ucfirst($type);
					if (!$this->$findUser($userId, $sourceId)) {
						$addLike = 'addLike' . ucfirst($type);
						if($this->$addLike($userId, $sourceId)){
							$funcCallGet = "get_{$type}_meta";
							$funcCallUpdate = "update_{$type}_meta";
							$count = $funcCallGet($sourceId, 'likes_users', 1);
							$count++;
							$funcCallUpdate($sourceId, 'likes_users', $count);

							return ['count' => $count];
						}
						$this->addError($this->getLang('failed_save_like'));
						return false;
					} else {
						$this->addError($this->getLang('like_exists'));

						return false;
					}

				} else {
					$findAnonymous = 'findAnonymousFor' . ucfirst($type);
					if (!$this->$findAnonymous($sourceId)) {
						$addLike = 'addLike' . ucfirst($type);
						if($this->$addLike(0, $sourceId)){
							$funcCallGet = "get_{$type}_meta";
							$funcCallUpdate = "update_{$type}_meta";
							$count = $funcCallGet($sourceId, 'likes_users', 1);
							$count++;
							$funcCallUpdate($sourceId, 'likes_users', $count);

							return ['count' => $count];
						}
						$this->addError($this->getLang('failed_save_like'));
						return false;
					} else {
						$this->addError($this->getLang('like_exists'));
						return false;
					}
				}
			} else {
				$this->addError($this->getLang('not_found'));
				return false;
			}
		}

		return false;
	}

	// for other variant handler
//	public function handlerPost()
//	{
//
//	}
//
//	public function handlerComment()
//	{
//
//	}

	public function addLikePost($userId = 0, $id = 0)
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "INSERT INTO {$this->tbLikePost} (post_id, user_id, ip, time)
		VALUES (
		{$this->toInt($id)},
		{$this->toInt($userId)},
		'{$this->escapeStr($ip)}',
		'{$this->escapeStr($this->getUTCTimestamp())}'
		)
		ON DUPLICATE KEY UPDATE ip = VALUES(ip), time = VALUES(time)
		";
		$result = $this->db->query($sql);

		return $result;
	}

	public function addLikeComment($userId = 0, $id = 0)
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "INSERT INTO {$this->tbLikeComment} (comment_id, user_id, ip, time)
		VALUES (
		{$this->toInt($id)},
		{$this->toInt($userId)},
		'{$this->escapeStr($ip)}',
		'{$this->escapeStr($this->getUTCTimestamp())}'
		)
		ON DUPLICATE KEY UPDATE ip = VALUES(ip), time = VALUES(time)
		";
		$result = $this->db->query($sql);

		return $result;
	}

	public function checkIdPost($id)
	{
		$sql = "SELECT ID FROM {$this->tb_prefix}posts WHERE ID = {$this->toInt($id)}";
		return $this->db->get_var($sql);
	}

	public function checkIdComment($id)
	{
		$sql = "SELECT comment_ID FROM {$this->tb_prefix}comments WHERE comment_ID = {$this->toInt($id)}";
		return $this->db->get_var($sql);
	}

	public function findAnonymousForPost($id)
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "SELECT time FROM {$this->tbLikePost} WHERE post_id = {$this->toInt($id)} AND ip = '{$this->escapeStr($ip)}' AND user_id = 0";
		return (bool)$this->db->query($sql);
	}

	public function findAnonymousForComment($id)
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "SELECT time FROM {$this->tbLikeComment} WHERE comment_id = {$this->toInt($id)} AND ip = '{$this->escapeStr($ip)}' AND user_id = 0";
		return (bool)$this->db->query($sql);
	}

	public function findForPost($userId, $id)
	{
		$sql = "SELECT time FROM {$this->tbLikePost} WHERE post_id = {$this->toInt($id)} AND user_id = {$this->toInt($userId)}";
		return (bool)$this->db->query($sql);
	}

	public function findForComment($userId, $id)
	{
		$sql = "SELECT time FROM {$this->tbLikeComment} WHERE comment_id = {$this->toInt($id)} AND user_id = {$this->toInt($userId)} ";
		return (bool)$this->db->query($sql);
	}

	public function getTotalPost($postId)
	{
		return 1;
	}

	public function getTotalComment($comId)
	{
		return 1;
	}

	public static function getUTCTimestamp($asInt = 0)
	{
		$time = time();
		$now = \DateTime::createFromFormat('U', $time);
		$now->setTimeZone(new \DateTimeZone('UTC'));

		return boolval($asInt)? (int)$now->getTimestamp() : $now->format('Y-m-d H:i:s');
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}