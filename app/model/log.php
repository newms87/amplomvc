<?php
class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getColumns($filter = array())
	{
		$columns = parent::getColumns($filter);

		$columns['message']['align'] = 'left';

		return $columns;
	}
}
