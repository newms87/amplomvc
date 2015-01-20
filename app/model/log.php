<?php

class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getLogs()
	{
		return $this->queryColumn("SELECT DISTINCT name FROM " . self::$tables['log'], 'name');
	}

	public function getColumns($filter = false)
	{
		$columns['message']['align']     = 'left';
		$columns['log_id']['sort_order'] = -2;
		$columns['name']['sort_order']   = -1;

		return parent::getTableColumns('log', $columns, $filter);
	}

	public function remove($log_id)
	{
		$this->delete('log', $log_id);
	}

	public function clear($name = '')
	{
		if ($name) {
			$this->delete('log', array('name' => $name));
		} else {
			$this->delete('log');
		}
	}
}
