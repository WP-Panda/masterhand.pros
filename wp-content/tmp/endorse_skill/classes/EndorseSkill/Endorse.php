<?php
namespace EndorseSkill;

class Endorse extends Base
{
	protected static $_instance = null;


	public function __construct()
	{
		parent::__construct();

		$pathTpl = ENDORSE_SKILL_DIR . 'tpl/';
		$pathCache = $pathTpl . 'cache';
		if (!file_exists($pathCache)) {
			mkdir($pathCache, 0755, true);
		}

		$this->fenom = \Fenom::factory($pathTpl, $pathCache);
		$this->fenom->setOptions(\Fenom::AUTO_RELOAD);
	}

	/**
	 * get all skills of group for edit
	 * @param $userId
	 * @param string $group
	 * @return array|null|object
	 */
	public function getForEdit($userId, $group = '')
	{
		$sql = "SELECT s.id, s.title, 1 as checked,
		(SELECT COUNT(e.user_endorse) FROM {$this->tbEndorseSkill} e WHERE e.skill_id = s.id) as endorse
 		FROM {$this->tbSkill} s
		WHERE s.user_id = {$this->toInt($userId)}
		";

		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * get skills user with endorsement
	 * @param $userId
	 * @param int $currentUser
	 * @return array|null|object
	 */
	public function forUser($userId, $currentUser = 0)
	{
		$skillSelected = '';
		if($currentUser){
			$skillSelected = "IF((SELECT sel.skill_id FROM {$this->tbEndorseSkill} sel
			WHERE sel.skill_id = su.id AND sel.user_id = su.user_id AND sel.user_endorse = {$this->toInt($currentUser)}), 1, 0) as endorsed,";
		}
		$sql = "SELECT su.id, su.title,
		{$skillSelected}
		(SELECT COUNT(e.user_endorse) FROM {$this->tbEndorseSkill} e WHERE e.skill_id = su.id) as endorse
 		FROM {$this->tbSkill} su
		WHERE su.user_id = {$this->toInt($userId)}
		";

		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * bind endorsement to skill user
	 * @param $userId
	 * @param $skillId
	 * @param $endorseId
	 * @return bool
	 */
	public function bind($userId, $skillId, $endorseId)
	{
		$ids = $this->isIdExists($userId, $skillId);
		$values = '';

		if(is_array($ids)) {
			foreach ($ids as $row) {
				$values .= "({$this->toInt($userId)}, {$this->toInt($row['id'])}, {$this->toInt($endorseId)}),";
			}
		} else {
			$values = "({$this->toInt($userId)}, {$this->toInt($ids)}, {$this->toInt($endorseId)})";
		}
		if(!empty($values)) {
			$values = trim($values, ',');

//			$this->db->query("SET FOREIGN_KEY_CHECKS=0");
			$sql = "INSERT INTO {$this->tbEndorseSkill} (user_id, skill_id, user_endorse) VALUES {$values}
			ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), skill_id = VALUES(skill_id), user_endorse = VALUES(user_endorse)
			";
			$this->db->query($sql);

			return ($this->db->last_error)? false : true;
		}

		return false;
	}

	public function unBind($userId, $skillId, $endorseId)
	{
		$id = $this->isIdExists($userId, $skillId);

		if(!empty($id)) {
			$sql = "DELETE FROM {$this->tbEndorseSkill} WHERE
			user_id = {$this->toInt($userId)} AND skill_id = {$this->toInt($id)} AND user_endorse = {$this->toInt($endorseId)}";
			$this->db->query($sql);

			return ($this->db->last_error)? false : true;
		}

		return false;
	}

	/**
	 * get value endorsement skill user
	 * @param $userId
	 * @param $skillId
	 * @return int
	 */
	public function valueUserSkill($userId, $skillId)
	{
		$sql = "SELECT COUNT(user_endorse) FROM {$this->tbEndorseSkill} WHERE user_id = {$this->toInt($userId)} AND skill_id = {$this->toInt($skillId)}";

		return (int)$this->db->get_var($sql);
	}

	public function isIdExists($userId, $id)
	{
		if(is_array($id)){
			$ids = [];
			foreach($id as $item){
				$ids[] = (int)$item;
			}
			$sql = "SELECT id FROM {$this->tbSkill} WHERE user_id = {$this->toInt($userId)} AND id in (" . implode(',', $ids) . ")";
			if($result = $this->db->get_results($sql, ARRAY_A)){
				return $result;
			}
			return [];
		}

		$sql = "SELECT id FROM {$this->tbSkill} WHERE user_id = {$this->toInt($userId)} AND id = {$this->toInt($id)}";
		return (int)$this->db->get_var($sql);
	}

	public function getCount($userId = 0)
	{
		$sql = "SELECT COUNT(skill_id) FROM {$this->tbEndorseSkill} WHERE user_id = {$this->toInt($userId)}";

		return (int)$this->db->get_var($sql);
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}