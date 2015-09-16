<?php

class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getLogs()
	{
		return $this->queryColumn("SELECT DISTINCT name FROM {$this->t['log']}", 'name');
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'message' => array(
				'align' => 'left',
			),
		);

		return parent::getColumns($filter, $merge);
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
