<?php

abstract class Model
{
	protected $db;
	protected $prefix;

	const
		TEXT = 'text',
		NO_ESCAPE = 'no-escape',
		IMAGE = 'image',
		INTEGER = 'int',
		FLOAT = 'float',
		DATETIME = 'datetime',
		PRIMARY_KEY_INTEGER = 'pk-int',
		AUTO_INCREMENT = 'ai',
		AUTO_INCREMENT_PK = 'pk';

	//In case a plugin wants to wrap this Class
	protected $error = array();

	public function __construct()
	{
		global $registry;

		$this->prefix = DB_PREFIX;

		$key = strtolower(get_class($this));

		$registry->set($key, $this);

		//use default database
		if (!$this->db) {
			//(Note: setting our own $db property allows us to use a different database for new Model instances)
			$this->db = $registry->get('db');
		}
	}

	public function __get($key)
	{
		global $registry;
		return $registry->get($key);
	}

	public function hasError($type = null)
	{
		if ($type) {
			return !empty($this->error[$type]);
		}

		return !empty($this->error);
	}

	public function getError($type = null)
	{
		if ($type) {
			return isset($this->error[$type]) ? $this->error[$type] : null;
		}

		return $this->error;
	}

	public function clearErrors()
	{
		$this->error = array();
	}

	public function escape($value)
	{
		return $this->db->escape($value);
	}

	public function escapeHtml($value)
	{
		return $this->db->escape($value);
	}

	public function getLastId()
	{
		return $this->db->getLastId();
	}

	public function countAffected()
	{
		return $this->db->countAffected();
	}

	protected function query($sql)
	{
		return $this->db->query($sql);
	}

	protected function queryVar($sql)
	{
		return $this->db->queryVar($sql);
	}

	protected function queryRow($sql)
	{
		return $this->db->queryRow($sql);
	}

	protected function queryRows($sql, $index = null)
	{
		return $this->db->queryRows($sql, $index);
	}

	protected function queryColumn($sql)
	{
		return $this->db->queryColumn($sql);
	}

	protected function insert($table, $data)
	{
		$this->actionFilter('insert', $table, $data);

		$values = $this->getInsertString($table, $data, false);

		$success = $this->query("INSERT INTO " . $this->prefix . "$table SET $values");

		if (!$success) {
			trigger_error("There was a problem inserting entry for $table and was not modified.");

			if ($this->hasError()) {
				trigger_error($this->getError());
			}

			return false;
		}

		return $this->getLastId();
	}

	protected function update($table, $data, $where = null)
	{
		$this->actionFilter('update', $table, $data, $where);

		$primary_key = $this->getPrimaryKey($table);

		$values = $this->getInsertString($table, $data);

		$update_id = true; //Our Return value

		if (!$where) {
			if (empty($data[$primary_key])) {
				return $this->insert($table, $data);
			}

			$update_id = (int)$data[$primary_key];

			$where = "WHERE `$primary_key` = $update_id";
		} elseif (is_integer($where) || (is_string($where) && preg_match("/[^\\d]/", $where) === 0)) {
			if (!$primary_key) {
				trigger_error("UPDATE $table " . _l("does not have an integer primary key!"));
				return null;
			}

			$update_id = (int)$where;

			$where = "WHERE `$primary_key` = $update_id";
		} elseif (is_array($where)) {
			if (isset($where[$primary_key])) {
				$update_id = (int)$where[$primary_key];
			}

			$where = "WHERE " . $this->getWhere($table, $where, '', '', true);
		}

		$success = $this->query("UPDATE " . $this->prefix . "$table SET $values $where");

		if (!$success) {
			trigger_error("There was a problem updating entry for $table and was not modified.");

			if ($this->hasError()) {
				trigger_error($this->getError());
			}

			return false;
		}

		return $update_id;
	}

	protected function delete($table, $where = null)
	{
		$this->actionFilter('delete', $table, $data);

		if (is_integer($where) || (is_string($where) && preg_match("/[^\\d]/", $where) === 0)) {
			$primary_key = $this->getPrimaryKey($table);
			if (!$primary_key) {
				trigger_error("DELETE " . _l("%s does not have an integer primary key!"));
				return null;
			}

			$where = "`$primary_key` = '$where'";
		} elseif (is_array($where)) {
			$where = $this->getWhere($table, $where, null, null, true);
		}

		$table = $this->escape($table);

		if ($where !== '1') {
			$where = "WHERE $where";
		}

		$success = $this->query("DELETE FROM " . $this->prefix . "$table $where");

		if (!$success) {
			trigger_error("There was a problem deleting entry for $table and was not modified.");

			if ($this->hasError()) {
				trigger_error($this->getError());
			}

			return false;
		}

		return true;
	}

	protected function getWhere($table, $data, $prefix = '', $glue = '', $primary_key = false)
	{
		$data = $this->getEscapedValues($table, $data, $primary_key);

		if (!$glue) {
			$glue = 'AND';
		}

		$values = '';

		foreach ($data as $key => $value) {
			$values .= ($values ? ' ' . $glue . ' ' : '') . ($prefix ? $prefix . '.' : '') . "`$key` = '$value'";
		}

		return $values ? $values : '1';
	}

	protected function getInsertString($table, $data, $primary_key = false)
	{
		$data = $this->getEscapedValues($table, $data, $primary_key);

		$values = '';

		foreach ($data as $key => $value) {
			$values .= ($values ? ',' : '') . "`$key` = '$value'";
		}

		return $values;
	}

	protected function getEscapedValues($table, $data, $auto_inc = true)
	{
		$columns = $this->getTableColumns($table);

		$data = array_intersect_key($data, $columns);

		foreach ($data as $key => &$value) {
			if (is_resource($value) || is_array($value) || is_object($value)) {
				trigger_error(_l("%s(): The field %s was given a value that was not a valid type! Value: %s.", __METHOD__, $key, gettype($value)));
				exit;
			}

			switch ((int)$columns[$key]) {
				case self::AUTO_INCREMENT_PK:
				case self::AUTO_INCREMENT:
					if ($auto_inc) {
						$value = $this->escape($value);
					} else {
						unset($data[$key]);
					}
					break;
				case self::TEXT:
					$value = $this->escape($value);
					break;
				case self::NO_ESCAPE:
					break;
				case self::IMAGE:
					$value = $this->escape(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
					break;
				case self::INTEGER:
					$value = (int)$value;
					break;
				case self::FLOAT:
					$value = (float)$value;
					break;
				case self::DATETIME:
					if (!$value) {
						$value = DATETIME_ZERO;
					}
					$value = $this->date->format($value);
					break;

				default:
					$value = $this->escape($value);
					break;
			}
		}
		unset($value);

		return $data;
	}

	protected function extractFilter($table, $filter, $columns = array())
	{
		$method = array(
			self::NO_ESCAPE           => 'equals',
			self::TEXT                => 'like',
			self::AUTO_INCREMENT      => 'int_in',
			self::AUTO_INCREMENT_PK   => 'int_in',
			self::PRIMARY_KEY_INTEGER => 'int_in',
			self::FLOAT               => 'float_in',
			self::INTEGER             => 'int_in',
			self::DATETIME            => 'date',
			self::IMAGE               => 'equals',
		);

		$columns += $this->getTableColumns($table);

		$where = '1';

		foreach ($filter as $key => $value) {
			if (!isset($columns[$key])) {
				continue;
			}

			if (is_array($columns[$key])) {
				$type = isset($method[$columns[$key]['type']]) ? $method[$columns[$key]['type']] : 'equals';
			} else {
				$type = $columns[$key];
			}

			switch ($type) {
				case 'like':
					$where .= " AND `$key` like '%" . $this->escape($value) . "%'";
					break;

				case 'equals':
					$where .= " AND `$key` = '" . $this->escape($value) . "'";
					break;

				case 'text_in':
					if (is_array($value)) {
						$where .= " AND `$key` IN ('" . implode("','", $this->escape($filter[$key])) . "')";
					} else {
						$where .= " AND `$key` = '" . $this->escape($value) . "'";
					}
					break;

				case 'float_equals':
				case 'int_equals':
					$value = $type === 'int_equals' ? (int)$value : (float)$value;
				case 'number_equals':
					$where .= " AND `$key` = " . $value;
					break;

				case 'float_in':
				case 'int_in':
					array_walk($value, function (&$a) use ($type) {
						$a = $type === 'int_in' ? (int)$a : (float)$a;
					});
				case 'number_in':
					$where .= " AND `$key` IN (" . implode(',', $value) . ")";
					break;

			}
		}

		return $where;
	}

	protected function extractOrder($data)
	{
		if (empty($data['sort'])) {
			return '';
		}
		//TODO: Legacy code to handle single column sort. Remove after Version 1.0
		if (!is_array($data['sort'])) {
			$data['sort'] = array(
				$data['sort'] => !empty($data['order']) ? $data['order'] : 'ASC',
			);
		}

		$sql = '';

		foreach ($data['sort'] as $sort => $order) {
			$sort = $this->escape($sort);

			if (!preg_match("/[^a-z0-9_]/i", $sort)) {
				$sort = "`" . $sort . "`";
			}

			$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

			$sql .= ($sql ? ',' : '') . "$sort $order";
		}

		return "ORDER BY $sql";
	}

	protected function extractLimit($data)
	{
		$limit = '';

		if (isset($data['limit'])) {
			if ((int)$data['limit'] > 0) {

				$start = (isset($data['start']) && (int)$data['start'] > 0) ? (int)$data['start'] : 0;

				$limit = " LIMIT $start," . (int)$data['limit'];
			}
		}

		return $limit;
	}

	protected function getTableColumns($table, $merge = array(), $filter = array())
	{
		$columns = cache('model.' . $table);

		if (!$columns) {
			$columns = $this->db->getTableColumns($table);

			foreach ($columns as &$column) {
				$type = strtolower(trim(preg_replace("/\\(.*$/", '', $column['Type'])));

				$cast = array(
					'bigint'    => self::INTEGER,
					'mediumint' => self::INTEGER,
					'smallint'  => self::INTEGER,
					'tinyint'   => self::INTEGER,
					'int'       => self::INTEGER,
					'decimal'   => self::FLOAT,
					'float'     => self::FLOAT,
					'double'    => self::FLOAT,
					'datetime'  => self::DATETIME,
					'timestamp' => self::INTEGER,
					'binary'    => self::NO_ESCAPE,
					'varbinary' => self::NO_ESCAPE,
				);

				$type = isset($cast[$type]) ? $cast[$type] : self::TEXT;

				if ($column['Key'] === 'PRI' && $type === self::INTEGER) {
					if ($column['Extra'] === 'auto_increment') {
						$type = self::AUTO_INCREMENT_PK;
					} else {
						$type = self::PRIMARY_KEY_INTEGER;
					}
				} elseif ($column['Extra'] === 'auto_increment') {
					$type = self::AUTO_INCREMENT;
				} elseif (strtolower($column['Field']) === 'image') {
					$type = self::IMAGE;
				}

				$column['type']     = $type;
				$column['sortable'] = true;
				$column['filter']   = true;
			}
			unset($column);

			cache('model.' . $table, $columns);
		}

		//Merge
		foreach ($merge as $field => $data) {
			if (isset($columns[$field])) {
				$columns[$field] = $data + $columns[$field];
			} elseif ($filter === false || isset($filter[$field])) {
				$columns[$field] = $data;
			}
		}

		//Filter / Sort
		if (!$filter && $filter !== false) {
			$filter = array_combine(array_keys($columns), range(0, count($columns) - 1));
		}

		if ($filter) {
			$columns = array_intersect_key($columns, $filter);
			uksort($columns, function ($a, $b) use ($filter) {
					return $filter[$a] > $filter[$b];
				});
		}

		return $columns;
	}

	private function getPrimaryKey($table)
	{
		$columns = $this->getTableColumns($table);

		$primary_key = null;
		foreach ($columns as $key => $col) {
			if ($col['type'] === self::PRIMARY_KEY_INTEGER || $col['type'] === self::AUTO_INCREMENT_PK) {
				if ($primary_key) {
					return null;
				}
				$primary_key = $key;
			}
		}

		return $primary_key;
	}

	private function actionFilter($action, $table, &$data)
	{
		$hooks = option('db_hook_' . $action . '_' . $table);

		if ($hooks) {
			foreach ($hooks as $hook) {
				if (is_array($hook['callback'])) {
					$classname = key($hook['callback']);
					$method    = current($hook['callback']);

					$class = $this->$classname;

					if (method_exists($class, $method)) {
						if (!is_array($hook['param'])) {
							$hook['param'] = array($hook['param']);
						}

						$params = array('__data__' => &$data) + $hook['param'];

						call_user_func_array(array(
							$class,
							$method
						), $params);
					} else {
						trigger_error(_l("%s(): The following method does not exist: %s::%s().", __METHOD__, $class, $method));
					}
				} else {
					if (function_exists($hook['callback'])) {
						$hook['callback']($hook['param']);
					} else {
						trigger_error(_l("%s(): The following function does not exist: %s().", __METHOD__, $hook['callback']));
					}
				}
			}
		}
	}
}
