<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
abstract class App_Model_Table extends Model
{
	protected $primary_key, $table;
	private $orig = array();

	private static $records = array();

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
			if (isset($data['meta'])) {
				$this->Model_Meta->saveAll($this->table, $record_id, $data['meta']);
			}

			clear_cache($this->table . '.rows');
			clear_cache($this->table . '.' . $record_id);
		}

		return $record_id;
	}

	public function copy($record_id, $data = array())
	{
		$record = $this->getRecord($record_id);

		if (!$record) {
			$this->error['record_id'] = _l("The Record does not exist");

			return false;
		}

		return $this->save(null, $data + $record);

	}

	public function remove($unit_id)
	{
		if ($unit_id) {
			clear_cache($this->table . '.rows');
			clear_cache($this->table . '.' . $unit_id);

			return $this->delete($this->table, $unit_id);
		}
	}

	public function removeWhere($filter)
	{
		if (!$filter) {
			$this->error = _l("Must set filter value. Do not use removeWhere to delete all records.");

			return false;
		}

		clear_cache($this->table);

		return $this->delete($this->table, $filter);
	}

	public function getField($record_id, $field, $allow_cache = true)
	{
		if ($allow_cache && isset(self::$records[$this->table][$record_id])) {
			return isset(self::$records[$this->table][$record_id][$field]) ? self::$records[$this->table][$record_id][$field] : null;
		}

		return $this->queryVar("SELECT $field FROM `{$this->t[$this->table]}` WHERE `$this->primary_key` = " . (int)$record_id);
	}

	public function getRecord($record_id, $select = '*', $cache = true)
	{
		$record = ($cache && isset(self::$records[$this->table][$record_id])) ? self::$records[$this->table][$record_id] : false;

		if (!$record) {
			$select = $this->extractSelect($this->table, $select);

			$record = $this->queryRow("SELECT $select FROM `{$this->t[$this->table]}` WHERE `$this->primary_key` = " . (int)$record_id);

			if ($record && $select === '*') {
				self::$records[$this->table][$record_id] = $record;
			}
		}

		return $record;
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

		if (!isset($options['alias'])) {
			$options['alias'] = $this->table[0];
		}

		//Select
		$fields = $this->extractSelect($this->table, $options);

		if ($cache) {
			$records = cache($cache);

			if ($records !== null) {
				return $records;
			}
		}

		//From
		$from = $this->extractFrom($this->table, $options);

		//Where
		$where = $this->extractWhere($this->table, $filter, $options);

		//Order
		$order = $this->extractOrder($this->table, $sort, $options);

		//Limit
		$limit = $this->extractLimit($options);

		$sql_calc_found_rows = isset($options['sql_calc_found_rows']) ? $options['sql_calc_found_rows'] : null;

		//The Query
		$records = $this->queryRows("SELECT $fields FROM $from WHERE $where" . ($order ? " ORDER BY $order" : '') . ($limit ? " LIMIT $limit" : ''), !empty($options['index']) ? $options['index'] : null, $total, $sql_calc_found_rows);

		//Get Meta Data
		if (!empty($options['meta'])) {
			$inline = $options['meta'] === 'inline';

			if (!is_array($options['meta'])) {
				$options['meta'] = array($this->table => $this->primary_key);
			} elseif (isset($options['meta']['inline'])) {
				$inline = true;
				unset($options['meta']['inline']);
			}

			$total ? $rows = &$records[0] : $rows = &$records;

			foreach ($rows as &$row) {
				foreach ($options['meta'] as $type => $record_id) {
					if ($inline) {
						$row += $this->Model_Meta->get($type, $row[$record_id]);
					} else {
						$row['meta'] = $this->Model_Meta->get($type, $row[$record_id]);
					}
				}
			}
			unset($row);
		}

		if ($cache) {
			cache($cache, $records);
		}

		return $records;
	}

	public function getTotalRecords($filter = array())
	{
		$where = $this->extractWhere($this->table, $filter);

		return $this->queryVar("SELECT COUNT(*) FROM {$this->t[$this->table]} WHERE $where");
	}

	protected function getCacheName($sort, $filter, $options, $total)
	{
		$s = count($sort) > 1 ? '.sort-' . md5(serialize($sort)) : '';
		$f = $filter ? '.filter-' . md5(serialize($filter)) : '';
		$o = $options ? '.opts-' . md5(serialize($options)) : '';
		$t = $total ? '.total' : '';

		return $this->table . '.rows' . $s . $f . $o . $t;
	}

	public function getColumns($filter = array(), $merge = array())
	{
		return $this->getTableColumns($this->table, $filter, $merge);
	}

	protected function mapAliasToKey($aliases, &$sort, &$filter, &$options)
	{
		foreach ($aliases as $alias => $key) {
			if (isset($filter[$alias])) {
				$filter[$key] = $filter[$alias];
			}

			if (isset($filter['!' . $alias])) {
				$filter['!' . $key] = $filter['!' . $alias];
			}

			if (isset($sort[$alias])) {
				$sort[$key] = $sort[$alias];
				unset($sort[$alias]);
			}

			if (isset($options['columns'], $options['columns'][$alias])) {
				if (!isset($options['columns'][$key])) {
					$options['columns'][$key] = 1;
				}
			}
		}
	}

	public function history($table, $record_id, $action, $data, $message = null, $status = null)
	{
		$table = $this->t[$table];

		if ($status === null && $table !== $this->t['history'] && $this->t[$this->table] === $table) {
			$columns = $this->getColumns();

			$status = !empty($data['status']) ? $data['status'] : null;

			if ($status !== null) {
				$status = !empty($columns['status']['build']) ? get_build_value($columns['status']['build'], $data['status']) : '';
			}
		}

		return parent::history($table, $record_id, $action, $data, $message, $status);
	}
}
