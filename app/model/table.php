<?php

abstract class App_Model_Table extends Model
{
	protected $primary_key, $table;
	private $orig = array();

	public function __construct()
	{
		parent::__construct();

		if ($this->table) {
			if (!$this->primary_key) {
				$this->primary_key = $this->getPrimaryKey($this->table);
			}

			if (!$this->primary_key) {
				trigger_error(_l("The table %s must have a single field primary key in order to extend App_Model_Table with %s!", $this->table, get_class()));
				exit;
			}
		} else {
			trigger_error(_l("You must set the \$table attribute for %s to extend the class App_Model_Table!", get_class()));
			exit;
		}

		$this->orig['table']       = $this->table;
		$this->orig['primary_key'] = $this->primary_key;
	}

	public function setTable($table, $primary_key = null)
	{
		$this->table       = $table;
		$this->primary_key = $primary_key;
	}

	public function resetTable()
	{
		$this->table       = $this->orig['table'];
		$this->primary_key = $this->orig['primary_key'];
	}

	public function save($record_id, $data)
	{
		if ($record_id) {
			$record_id = $this->update($this->table, $data, $record_id);
		} else {
			$record_id = $this->insert($this->table, $data);
		}

		if ($record_id) {
			clear_cache($this->table . '.rows');
			clear_cache($this->table . '.' . $record_id);
		}

		return $record_id;
	}

	public function remove($record_id)
	{
		clear_cache($this->table . '.rows');
		clear_cache($this->table . '.' . $record_id);

		return $this->delete($this->table, $record_id);
	}

	public function getField($record_id, $field)
	{
		return $this->queryVar("SELECT $field FROM `{$this->t[$this->table]}` WHERE `$this->primary_key` = " . (int)$record_id);
	}

	public function getRecord($record_id, $select = '*')
	{
		$select = $this->extractSelect($this->table, $select);

		return $this->queryRow("SELECT $select FROM `{$this->t[$this->table]}` WHERE `$this->primary_key` = " . (int)$record_id);
	}

	public function findRecord($filter, $select = null)
	{
		$fields = $select ? $this->extractSelect($this->table, $select) : $this->primary_key;
		$where  = $this->extractWhere($this->table, $filter);

		$sql = "SELECT $fields FROM `{$this->t[$this->table]}` WHERE $where";

		return $select ? $this->queryRow($sql) : $this->queryVar($sql);
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$cache = !empty($options['cache']) ? $this->getCacheName($sort, $filter, $options, $total) : false;
		$tbl   = $this->table[0];

		//Select
		$fields = $this->extractSelect($this->table . ' ' . $tbl, $options);

		if ($cache) {
			$records = cache($cache);

			if ($records !== null) {
				return $records;
			}
		}

		//From
		$from = $this->extractFrom($this->table . ' ' . $tbl, $options);

		//Where
		$where = $this->extractWhere($this->table . ' ' . $tbl, $filter);

		$group_by = !empty($options['group_by']) ? $options['group_by'] : '';
		$having   = !empty($options['having']) ? $options['having'] : '';

		//Order
		$order = $this->extractOrder($sort, $tbl);

		//Limit
		$limit = $this->extractLimit($options);

		//The Query
		$records = $this->queryRows("SELECT $fields FROM $from WHERE $where $group_by $having $order $limit", !empty($options['index']) ? $options['index'] : null, $total);

		if ($cache) {
			cache($cache, $records);
		}

		return $records;
	}

	public function getTotalRecords($filter = array())
	{
		return $this->getRecords(null, $filter, array('columns' => 'COUNT(*)'));
	}

	protected function getCacheName($sort, $filter, $options, $total)
	{
		$s     = count($sort) > 1 ? '.sort-' . md5(serialize($sort)) : '';
		$f     = $filter ? '.filter-' . md5(serialize($filter)) : '';
		$o     = $options ? '.opts-' . md5(serialize($options)) : '';
		$t     = $total ? '.total' : '';
		return $this->table . '.rows' . $s . $f . $o . $t;
	}

	public function getColumns($filter = array())
	{
		$merge = array();

		return $this->getTableColumns($this->table, $merge, $filter);
	}
}
