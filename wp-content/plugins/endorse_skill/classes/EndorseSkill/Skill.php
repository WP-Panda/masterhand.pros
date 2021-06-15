<?php
namespace EndorseSkill;

class Skill extends Base
{
	public function bindToUser($userId, $ids, $group = '')
	{
		$idsExists = $this->isIdExists($ids);
		$values = '';
		$bindSkills = (new Endorse())->forUser($userId);
		$isUnBinded = false;
		if(!empty($bindSkills)) {
			foreach ($bindSkills as $item => $skill) {
				if (!empty($idsExists[$skill['id']])) {
					unset($bindSkills[$item]);
				}
			}

			if (!empty($bindSkills)) {
				foreach ($bindSkills as $skill) {
					if ((int)$skill['endorse'] == 0) {
						$this->unBind($userId, $skill['id']);
						$isUnBinded = true;
					}
				}
			}
		}

		foreach ($ids as $skill) {
			if(empty($idsExists[trim($skill)])){
				$values.= "({$this->toInt($userId)}, '{$this->escapeStr($this->clearStr($skill))}', '{$this->escapeStr($group)}'),";
			}
		}
		if(!empty($values)) {
			$values = trim($values, ',');

			$sql = "INSERT INTO {$this->tbSkill} (user_id, title, group_skill) VALUES {$values}
			ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), title = VALUES(title)
			";
			$this->db->query($sql);

			if($this->db->last_error){
				return false;
			} else {
				$count = $this->getCount($userId);
				do_action('activityRating_forSkill', $count);
				return true;
			}
		}
		if($isUnBinded){
			$count = $this->getCount($userId);
			do_action('activityRating_forSkill', $count);
		}

		return $isUnBinded;
	}

	public function get($id = 0)
	{
		return $this->db->get_row("SELECT * FROM {$this->tbSkill} WHERE id = {$this->toInt($id)}", ARRAY_A);
	}

	public function create($data)
	{
		$ins = [];

		if(empty($data['skill'])){
			$this->addError($this->getLang('name_skill_empty'));
			return false;
		}

		if(in_array($data['group'], $this->groupSkill)){
			$ins['group_skill'] = $this->escapeStr($data['group']);
		} else {
			$ins['group_skill'] = $this->groupSkillDefault;
		}

		if($this->isNameExists($data['skill'], $ins['group_skill'])){
			$this->addError($this->getLang('name_skill_exists'));
			return false;
		}

		$ins['title'] = $this->escapeStr($this->clearStr($data['skill']));

		$result = $this->db->insert($this->tbSkill, $ins);

		return $result? $this->db->insert_id : false;
	}

	public function update($data)
	{
		$upd = [];
		if(!$this->isIdExists($data['id'])){
			$this->addError($this->getLang('not_found_record'));
			return false;
		}

		if(empty($data['skill'])){
			$this->addError($this->getLang('name_skill_empty'));
			return false;
		}

		if(in_array($data['group_skill'], $this->groupSkill)){
			$upd['group_skill'] = $this->escapeStr($data['group_skill']);
		} else {
			$upd['group_skill'] = $this->groupSkillDefault;
		}

		if($this->isNameExists($data['skill'], $upd['group_skill'], $data['id'])){
			$this->addError($this->getLang('name_skill_exists'));
			return false;
		}

		$upd['title'] = $this->escapeStr($this->clearStr($data['skill']));

		return $this->db->update($this->tbSkill, $upd, ['id' => (int)$data['id']]);
	}

	public function delete($id)
	{
		if($this->toInt($id)){
			return $this->db->query("DELETE FROM {$this->tbSkill} WHERE id = {$this->toInt($id)}");
		}

		return false;
	}

	public function unBind($userId, $skillId)
	{
		if($this->toInt($userId)){
			return $this->db->query("DELETE FROM {$this->tbSkill}
			WHERE user_id = {$this->toInt($userId)} AND id = {$this->toInt($skillId)}");
		}

		return false;
	}

	public function isNameExists($str, $group = '', $id = 0)
	{
		$andId = $id? " AND id != {$this->toInt($id)}" : '';
		$sql = "SELECT id FROM {$this->tbSkill} WHERE group_skill = '{$this->escapeStr($group)}'
 		AND title = '{$this->escapeStr($this->clearStr($str))}' {$andId}";

		$result = $this->db->get_var($sql);

		return $result;
	}

	public function isIdExists($id, $fields = 'id, group_skill')
	{
		if(is_array($id)){
			$ids = [];
			foreach($id as $item){
				$ids[] = $this->toInt($item);
			}
			$sql = "SELECT {$fields} FROM {$this->tbSkill} WHERE id in (" . implode(',', $ids) . ")";
			if($result = $this->db->get_results($sql, OBJECT_K)){
				return (array)$result;
			}
			return [];
		}

		return (int)$this->db->get_var("SELECT id FROM {$this->tbSkill} WHERE id = {$this->toInt($id)}");
	}

	public function getCount($userId = 0)
	{
		return $this->db->get_var("SELECT COUNT(id) FROM {$this->tbSkill} WHERE user_id = {$this->toInt($userId)}");
	}

	public function clearStr($str = '')
	{
//		'/[\s|+|=|_|;|\\|\']/'

		$str = preg_replace('/[^a-zA-Z\s-]/', '', $str);
		$str = preg_replace('/\s{2,}/', '', $str);
		return $str;
	}
}