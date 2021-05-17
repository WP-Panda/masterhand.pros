<?php
namespace ReviewsRating;

class Messages extends Base
{
	protected static $_instance = null;

	protected $isAdmin = 0;
	protected $userId = 0;
	protected $parentId = 0;
	protected $subject = '';
	protected $message = '';

	public function setIsAdmin()
	{
		$this->isAdmin = 1;

		return $this;
	}

	public function setUserId($userId = 0)
	{
		$this->userId = (int)$userId;

		return $this;
	}

	public function setSubject($subj = '')
	{
		$this->subject = $this->escapeStr($subj);

		return $this;
	}

	public function setMessage($msg = '')
	{
		$this->message = $this->escapeStr(htmlspecialchars($msg));

		return $this;
	}

	public function setParent($parentId = '')
	{
		$this->parentId = (int)$parentId;

		return $this;
	}

	public function create()
	{

	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}