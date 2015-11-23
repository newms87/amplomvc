<?php

class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$aliases = array(
			'user' => 'user_id',
		);

		$this->mapAliasToKey($aliases, $sort, $filter, $options);

		return parent::getRecords($sort, $filter, $options, $total);
	}

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
			'user'    => array(
				'type'         => 'select',
				'display_name' => _l("User"),
				'build'        => array(
					'name'  => 'user_id',
					'data'  => array(),
					'label' => 'username',
					'value' => 'user_id',
				),
				'filter'       => 'multiselect',
				'sort'         => true,
				'editable'     => false,
			),
		);

		$columns = parent::getColumns($filter, $merge);

		//Initialize User data only if necessary
		if (isset($columns['user'])) {
			$columns['user']['build']['data'] = $this->Model_User->getRecords(array('username' => 'ASC'), null, array('cache' => true));
		}

		return $columns;
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
