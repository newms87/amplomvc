<?php

class App_Model_History extends App_Model_Table
{
	protected $table = 'history', $primary_key = 'history_id';

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'data'    => array(
				'align' => 'left',
			),
			'message' => array(
				'align' => 'left',
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
