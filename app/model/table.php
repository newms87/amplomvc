<?php

abstract class App_Model_Table extends Model
{
	protected $primary_key, $table;

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
	}

	public function save($id, $data)
	{
		if ($id) {
			return $this->update($this->table, $data, $id);
		} else {
			return $this->insert($this->table, $data);
		}
	}

	public function remove($canvass_scope_id)
	{
		return $this->delete($this->table, $canvass_scope_id);
	}

	public function getField($id, $field)
	{
		return $this->queryVar("SELECT $field FROM `" . $this->t[$this->table] . "` WHERE `$this->primary_key` = " . (int)$id);
	}

	public function getRecord($id, $select = '*')
	{
		$select = $this->extractSelect($this->table, $select);

		return $this->queryRow("SELECT $select FROM `" . $this->t[$this->table] . "` WHERE `$this->primary_key` = " . (int)$id);
	}

	public function findRecord($filter, $select = null)
	{
		$select = $select ? $this->extractSelect($this->table, $select) : $this->primary_key;
		$where  = $this->extractWhere($this->table, $filter);

		$sql = "SELECT $select FROM `" . $this->t[$this->table] . "` WHERE $where";

		return $select ? $this->queryVar($sql) : $this->queryRow($sql);
	}

	public function getRecords($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		$cache = !empty($sort['cache']);
		$tbl = $this->table[0];

		//Select
		$select = $this->extractSelect($this->table . ' ' . $tbl, $select);

		if ($cache) {
			$s     = count($sort) > 1 ? '.sort-' . md5(serialize($sort)) : '';
			$f     = $filter ? '.filter-' . md5(serialize($filter)) : '';
			$l     = $select !== '*' ? '.select-' . md5($select) : '';
			$t     = $total ? '.total' : '';
			$i     = $index ? '.index-' . $index : '';
			$cache = $this->table . '.rows' . $s . $f . $l . $t . $i;

			$records = cache($cache);

			if ($records !== null) {
				return $records;
			}
		}

		//From
		$from = $this->t[$this->table] . ' ' . $tbl;

		if (!empty($sort['join'])) {
			$from .= ' ' . implode(' ', $sort['join']);
		}

		//Where
		$where = $this->extractWhere($this->table . ' ' . $tbl, $filter);

		//Order and Limit
		list($order, $limit) = $this->extractOrderLimit($sort, $tbl);

		//The Query
		$records = $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);

		if ($cache) {
			cache($cache, $records);
		}

		return $records;
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
