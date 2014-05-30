<?php

abstract class Model
{
	protected $error;
	protected $prefix;

	private $synctime = false;

	public function __construct()
	{
		global $registry, $ac_time_offset;

		if ($ac_time_offset) {
			$this->synctime = true;
		}

		$this->prefix = DB_PREFIX;

		$key = strtolower(get_class($this));

		if (!$registry->has($key)) {
			$registry->set($key, $this);
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

	protected function countAffected()
	{
		return $this->db->countAffected();
	}

	protected function query($sql)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		$resource = $this->db->query($sql);

		if (!$resource) {
			if (option('config_error_display')) {
				$this->message->add("warning", _l("The Database Query Failed!"));

				if ($this->db->hasError()) {
					$this->message->add('warning', $this->db->getError());
				}
			}
		}

		return $resource;
	}

	protected function queryRows($sql, $key_column = null)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		return $this->db->queryRows($sql, $key_column);
	}

	protected function queryRow($sql)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		return $this->db->queryRow($sql);
	}

	protected function queryColumn($sql)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		return $this->db->queryColumn($sql);
	}

	protected function queryVar($sql)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		return $this->db->queryVar($sql);
	}

	private function synctime($sql)
	{
		$now = new DateTime("@" . _time(), new DateTimeZone(DEFAULT_TIMEZONE));
		return str_replace("NOW()", "'" . $now->format("Y-m-d H:i:s") . "'", $sql);
	}

	protected function escape($value)
	{
		return $this->db->escape($value);
	}

	protected function insert($table, $data)
	{
		$this->action_filter('insert', $table, $data);

		$values = $this->getInsertString($table, $data, false);

		$success = $this->query("INSERT INTO " . $this->prefix . "$table SET $values");

		if (!$success) {
			trigger_error("There was a problem inserting entry for $table and was not modified.");

			if ($this->db->hasError()) {
				trigger_error($this->db->getError());
			}

			return false;
		}

		return $this->db->getLastId();
	}

	protected function update($table, $data, $where = null)
	{
		$this->action_filter('update', $table, $data, $where);

		$primary_key = $this->get_primary_key($table);

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

			if ($this->db->hasError()) {
				trigger_error($this->db->getError());
			}

			return false;
		}

		return $update_id;
	}

	protected function delete($table, $where = null)
	{
		$this->action_filter('delete', $table, $data);

		if (is_integer($where) || (is_string($where) && preg_match("/[^\\d]/", $where) === 0)) {
			$primary_key = $this->get_primary_key($table);
			if (!$primary_key) {
				trigger_error("DELETE " . _l("%s does not have an integer primary key!"));
				return null;
			}

			$where = "`$primary_key` = '$where'";
		} elseif (is_array($where)) {
			$where = $this->getWhere($table, $where, null, null, true);
		}

		$table = $this->db->escape($table);

		if ($where !== '1') {
			$where = "WHERE $where";
		}

		$success = $this->query("DELETE FROM " . $this->prefix . "$table $where");

		if (!$success) {
			trigger_error("There was a problem deleting entry for $table and was not modified.");

			if ($this->db->hasError()) {
				trigger_error($this->db->getError());
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
		$table_model = $this->get_table_model($table);

		$data = array_intersect_key($data, $table_model);

		foreach ($data as $key => &$value) {
			if (is_resource($value) || is_array($value) || is_object($value)) {
				trigger_error(_l("%s(): The field %s was given a value that was not a valid type! Value: %s.", __METHOD__, $key, gettype($value)));
				exit;
			}

			switch ((int)$table_model[$key]) {
				case DB_AUTO_INCREMENT_PK:
				case DB_AUTO_INCREMENT:
					if ($auto_inc) {
						$value = $this->db->escape($value);
					} else {
						unset($data[$key]);
					}
					break;
				case DB_ESCAPE:
					$value = $this->db->escape($value);
					break;
				case DB_NO_ESCAPE:
					break;
				case DB_IMAGE:
					$value = $this->db->escape(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
					break;
				case DB_INTEGER:
					$value = (int)$value;
					break;
				case DB_FLOAT:
					$value = (float)$value;
					break;
				case DB_DATETIME:
					if (!$value) {
						$value = DATETIME_ZERO;
					}
					$value = $this->date->format($value);
					break;

				default:
					$value = $this->db->escape($value);
					break;
			}
		}
		unset($value);

		return $data;
	}

	protected function extractFilter($columns, $filter)
	{
		$where = '1';

		foreach ($columns as $key => $type) {
			if (isset($filter[$key])) {
				$value = $filter[$key];

				switch ($type) {
					case 'text_like':
						$where .= " AND `$key` like '%" . $this->escape($value) . "%'";
						break;

					case 'text_equals':
						$where .= " AND `$key` = '" . $this->escape($value) . "'";
						break;

					case 'text_in':
						if (is_array($value)) {
							$where .= " AND `$key` IN ('" . implode("','", $this->escape($filter[$key])) . "')";
						} else {
							$where .= " AND `$key` = '" . $this->escape($value) . "'";
						}
						break;



				}
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
			$sort = $this->db->escape($sort);

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

	private function get_table_model($table)
	{
		$table_model = $this->cache->get('model.' . $table);

		if (!$table_model) {
			$table = $this->db->escape($table);

			$columns = $this->db->queryRows("SHOW COLUMNS FROM `" . $this->prefix . "$table`");

			$table_model = array();

			foreach ($columns as $col) {
				$type = strtolower(trim(preg_replace("/\\(.*$/", '', $col['Type'])));

				//we only care about ints and floats because only these we will do something besides escape
				$ints   = array(
					'bigint',
					'mediumint',
					'smallint',
					'tinyint',
					'int'
				);
				$floats = array(
					'decimal',
					'float',
					'double'
				);

				if ($col['Key'] == 'PRI' && in_array($type, $ints)) {
					if ($col['Extra'] == 'auto_increment') {
						$escape_type = DB_AUTO_INCREMENT_PK;
					} else {
						$escape_type = DB_PRIMARY_KEY_INTEGER;
					}
				} elseif ($col['Extra'] == 'auto_increment') {
					$escape_type = DB_AUTO_INCREMENT;
				} elseif (in_array($type, $ints)) {
					$escape_type = DB_INTEGER;
				} elseif (in_array($type, $floats)) {
					$escape_type = DB_FLOAT;
				} elseif ($type == 'datetime') {
					$escape_type = DB_DATETIME;
				} elseif (strtolower($col['Field']) == 'image') {
					$escape_type = DB_IMAGE;
				} else {
					$escape_type = DB_ESCAPE;
				}

				$table_model[$col['Field']] = $escape_type;
			}

			$this->cache->set('model.' . $table, $table_model);
		}

		return $table_model;
	}

	private function get_primary_key($table)
	{
		$table_model = $this->get_table_model($table);

		$primary_key = null;
		foreach ($table_model as $key => $type) {
			if ($type == DB_PRIMARY_KEY_INTEGER || $type == DB_AUTO_INCREMENT_PK) {
				if ($primary_key) {
					return null;
				}
				$primary_key = $key;
			}
		}

		return $primary_key;
	}

	private function action_filter($action, $table, &$data)
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
						trigger_error("Model::action_filter(): The following method does not exist: $class::$method().");
					}
				} else {
					if (function_exists($hook['callback'])) {
						$hook['callback']($hook['param']);
					} else {
						trigger_error("Model::action_filter(): The following function does not exist: $hook[callback]().");
					}
				}
			}
		}
	}
}
