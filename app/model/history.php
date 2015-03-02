<?php

class App_Model_History extends App_Model_Table
{
	protected $table = 'history', $primary_key = 'history_id';

	public function getColumns($filter = false)
	{
		$columns = array();

		return parent::getTableColumns($this->table, $columns, $filter);
	}
}
