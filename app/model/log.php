<?php

class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getColumns($filter = false)
	{
		$columns['message']['align']     = 'left';
		$columns['log_id']['sort_order'] = -2;
		$columns['name']['sort_order']   = -1;

		return parent::getTableColumns('log', $columns, $filter);
	}
}
