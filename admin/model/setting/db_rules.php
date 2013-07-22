<?php
class Admin_Model_Setting_DbRules extends Model
{
	public function addDbRule($data)
	{
		$this->insert('db_rule', $data);
		
		$this->cache->delete('model.'.$data['table']);
	}
	
	public function editDbRule($db_rule_id, $data)
	{
		$this->update('db_rule', $data, $db_rule_id);
		
		if (isset($data['table'])) {
			$this->cache->delete('model.'.$data['table']);
		}
		else {
			$this->cache->delete('model');
		}
	}
	
	public function deleteDbRule($db_rule_id)
	{
		$this->delete('db_rule', $db_rule_id);
		
		$this->cache->delete('model');
	}
	
	public function getDbRule($db_rule_id)
	{
		$query = $this->get('db_rule', '*', $db_rule_id);
		
		return $query->row;
	}
	
	public function getDbRules()
	{
		$options = array(
		'order_by' => '`table` ASC, `column` ASC'
		);
		
		$query = $this->get('db_rule',  '*', null, $options);
		
		return $query->rows;
	}
}
