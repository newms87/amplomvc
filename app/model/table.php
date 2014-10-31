<?php

abstract class App_Model_Table extends Model
{
	protected $key, $table, $p_table;

	public function __construct()
	{
		parent::__construct();

		if ($this->table) {
			if (!$this->key) {
				$this->key = $this->getPrimaryKey($this->table);
			}

			if (!$this->key) {
				trigger_error(_l("The table %s must have a single field primary key in order to extend App_Model_Table with %s!", $this->table, get_class()));
				exit;
			}
		} else {
			trigger_error(_l("You must set the \$table attribute for %s to extend the class App_Model_Table!", get_class()));
			exit;
		}

		if (!$this->p_table) {
			$this->p_table = $this->prefix . $this->table;
		}
	}

	public function save($id, $data)
	{
		if ($id) {
			return $this->update($this->table, $data, $id);
		} else {
			return $this->insert($this->table, $data);
		}
	}

	public function getField($id, $field)
	{
		return $this->queryVar("SELECT $field FROM `$this->p_table` WHERE `$this->key` = " . (int)$id);
	}

	public function getRecord($id, $select = '*')
	{
		$select = $this->extractSelect($this->table, $select);

		return $this->queryRow("SELECT $select FROM `$this->p_table` WHERE `$this->key` = " . (int)$id);
	}

	public function getRecords($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect($this->table, $select);

		//From
		$from = $this->prefix . $this->table;

		//Where
		$where = $this->extractWhere($this->table, $filter);

		//Order and Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		return $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);
	}

	public function getTotalRecords($filter = array())
	{
		return $this->getRecords(null, $filter, 'COUNT(*)');
	}

	public function getColumns($filter = array())
	{
		$merge = array();

		return $this->getTableColumns($this->table, $merge, $filter);
	}
}