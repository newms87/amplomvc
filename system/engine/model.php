<?php

abstract class Model
{
	static $model = array();

	protected $db, $prefix, $error = array();

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

	protected function load($path, $class)
	{
		global $registry;
		return $registry->load($path, $class);
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
			$error = isset($this->error[$type]) ? $this->error[$type] : null;

			if ($error) {
				unset($this->error[$type]);
			}

			return $error;
		}

		$error = $this->error;

		$this->error = array();

		return $error;
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
		$result = $this->db->query($sql);

		if (!$result) {
			$this->error = $this->db->getError();
		}

		return $result;
	}

	protected function queryVar($sql)
	{
		return $this->db->queryVar($sql);
	}

	protected function queryRow($sql)
	{
		return $this->db->queryRow($sql);
	}

	protected function queryRows($sql, $index = null, $total = false)
	{
		if ($total) {
			return $this->queryTotal($sql, $index);
		}

		return $this->db->queryRows($sql, $index);
	}

	protected function queryColumn($sql, $index_key = null)
	{
		return $this->db->queryColumn($sql, $index_key);
	}

	/**
	 * IMPORTANT: This method is only guaranteed to work on queries that do not contain sub queries!
	 * Also, not compatible with GROUP BY and HAVING clauses. Using aggregate functions may cause undesirable results.
	 *
	 * Use this to optimally query sorted / filtered rows using the LIMIT clause from
	 * the database along with the total number of rows (minus the LIMIT clause).
	 *
	 * @param string $sql - The query string without any sub queries (no guarantee it will work on sub queries).
	 * @param string $index - the index field to use for the rows returned
	 * @param bool $use_calc_found_rows - force the query to either use or not use the SQL_CALC_FOUND_ROWS statement.
	 *
	 * @return array - an array with array( 0 => rows, 1 => total ). USAGE HINT: list($rows, $total) = $this->queryTotal(...);
	 */

	protected function queryTotal($sql, $index = null, $use_calc_found_rows = null)
	{
		if (!$this->query->parse($sql)) {
			return false;
		}

		$select = $this->query->getClause('select', false);
		$the_rest = substr($sql, $this->query->getOffset('from'));

		if (is_null($use_calc_found_rows)) {
			$use_calc_found_rows = $this->useCalcFoundRows($sql);
		}

		if ($use_calc_found_rows) {
			$query = "SELECT SQL_CALC_FOUND_ROWS $select $the_rest";
		} else {
			$query = $sql;
		}

		$rows = $this->queryRows($query, $index);

		if ($use_calc_found_rows) {
			$total = $this->queryVar("SELECT FOUND_ROWS()");
		} else {
			$the_rest = $this->query->getClauses('from', 'where', 'group by', 'having');
			$total = $this->queryVar("SELECT COUNT(*) $the_rest");
		}

		return array(
			$rows,
			$total
		);
	}

	public function queryField($table, $field, $where)
	{
		$table = $this->escape($table);

		$where = $this->getWhere($table, $where, null, null, true);

		return $this->queryVar("SELECT " . $this->escape($field) . " FROM `" . $this->prefix . "$table` WHERE $where LIMIT 1");
	}

	public function queryFields($table, $fields, $where)
	{
		$table = $this->escape($table);

		$where = $this->getWhere($table, $where, null, null, true);

		return $this->queryRow("SELECT `" . implode(',', $this->escape($fields)) . " FROM `" . $this->prefix . "$table` WHERE $where LIMIT 1");
	}

	protected function insert($table, $data)
	{
		global $model_history;

		$this->actionFilter('insert', $table, $data);

		$values = $this->getInsertString($table, $data, false);

		$success = $this->query("INSERT INTO `" . $this->prefix . "$table` SET $values");

		if (!$success) {
			trigger_error("There was a problem inserting entry for $table and was not modified.");

			if ($this->hasError('query')) {
				$this->error['query'] = $this->getError('query');
				trigger_error($this->error['query']);
			}

			return false;
		}

		$row_id = $this->getLastId();

		if (!$row_id) {
			$primary_key = $this->getPrimaryKey($table);

			if ($primary_key && isset($data[$primary_key])) {
				$row_id = $data[$primary_key];
			}
		}

		if ($model_history && in_array($table, $model_history)) {
			$this->history($table, $row_id, 'insert', $data, true);
		}

		return $row_id;
	}

	protected function update($table, $data, $where = null)
	{
		global $model_history;

		$this->actionFilter('update', $table, $data, $where);

		$primary_key = $this->getPrimaryKey($table);

		$values = $this->getInsertString($table, $data);

		$update_id = true;

		if (!$where) {
			if (empty($data[$primary_key])) {
				return $this->insert($table, $data);
			}

			$where = (int)$data[$primary_key];

			$update_id = $where;
		} elseif (is_array($where)) {
			if (isset($where[$primary_key])) {
				$update_id = (int)$where[$primary_key];
			}
		} else {
			$update_id = (int)$where;
		}

		$where = $this->getWhere($table, $where, '', '', true);

		$success = $this->query("UPDATE `" . $this->prefix . "$table` SET $values WHERE $where");

		if (!$success) {
			trigger_error("There was a problem updating entry for $table and was not modified.");

			if ($this->hasError('query')) {
				trigger_error($this->getError('query'));
			}

			return false;
		}

		if ($model_history && $update_id !== true && in_array($table, $model_history)) {
			$this->history($table, $update_id, 'update', $data, true);
		}

		return $update_id;
	}

	protected function delete($table, $where = null)
	{
		global $model_history;

		$this->actionFilter('delete', $table, $data);

		$where = $this->getWhere($table, $where, null, null, true);

		$success = $this->query("DELETE FROM `" . $this->prefix . "$table` WHERE $where");

		if (!$success) {
			trigger_error("There was a problem deleting entry for $table and was not modified.");

			if ($this->hasError('query')) {
				trigger_error($this->getError('query'));
			}

			return false;
		}

		if ($model_history && in_array($table, $model_history)) {
			$delete_id = false;

			if (is_array($where)) {
				$primary_key = $this->getPrimaryKey($table);

				if (isset($where[$primary_key])) {
					$delete_id = (int)$where[$primary_key];
				}
			} else {
				$delete_id = (int)$where;
			}

			if ($delete_id) {
				$this->history($table, $delete_id, 'delete', $data, true);
			}
		}

		return true;
	}

	public function history($table, $row_id, $action, $data, $message = null)
	{
		if ($table !== 'history') {
			$columns = $this->getTableColumns($table);
			$data = array_intersect_key($data, $columns);

			$json_data = json_encode($data);

			if (strlen($json_data) > 2000) {
				$dir = DIR_SYSTEM . 'history/' . date('Y-m') . '/' . uniqid();

				if (_is_writable($dir)) {
					$history_file = $dir . uniqid();
					file_put_contents($history_file, $json_data);
					$json_data = $history_file;
				}
			}

			$history = array(
				'user_id' => $this->user->getId(),
				'table'   => $table,
				'row_id'  => $row_id,
				'action'  => $action,
				'data'    => $json_data,
				'date'    => $this->date->now(),
			);

			if ($message) {
				$history['message'] = $message === true ? $this->route->getPath() : $message;
			}

			$this->insert('history', $history);
		}
	}

	protected function getWhere($table, $where, $prefix = '', $glue = '', $primary_key = false)
	{
		if (!$where) {
			return '1';
		}

		if (is_integer($where) || is_string($where)) {
			$primary_key = $this->getPrimaryKey($table);
			if (!$primary_key) {
				trigger_error("WHERE statement " . _l("%s does not have an integer primary key!", $table));
				return null;
			}

			return "`$primary_key` = '" . $this->escape($where) . "'";
		}

		$where = $this->getEscapedValues($table, $where, $primary_key);

		if (!$glue) {
			$glue = 'AND';
		}

		$values = '';

		foreach ($where as $key => $value) {
			$values .= ($values ? ' ' . $glue . ' ' : '') . ($prefix ? "`$prefix`." : '') . "`$key` = '$value'";
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
			if (_is_object($value)) {
				trigger_error(_l("%s(): The field %s was given a value that was not a valid type! Value: %s.", __METHOD__, $key, gettype($value)));
				exit;
			}

			switch ($columns[$key]['type']) {
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

	protected function extractSelect($table_name, $columns)
	{
		if (!$columns || !is_array($columns)) {
			return '*';
		}

		if (is_string($columns)) {
			return $columns;
		}

		if (strpos($table_name, ' ')) {
			list($table_name, $t) = explode(' ', $table_name, 2);
		} else {
			$t = false;
		}

		$table = $this->db->hasTable($table_name);

		if (!$table) {
			trigger_error(_l("%s: Table %s does not exist!", __METHOD__, $table_name));
			return false;
		}

		if (!$t) {
			$t = $table;
		}

		$columns = array_intersect_key($columns, $this->getTableColumns($table));

		$select = '';
		foreach ($columns as $col => $data) {
			$select .= ($select ? ',' : '') . "`$t`.`$col`";
		}

		return $select;
	}

	/**
	 * Builds the WHERE string for the mysql statement based on the $table columns,
	 * and the values set in $filter. You can override the way the values are filtered
	 * using the $columns to specify the data type for columns.
	 *
	 * @param string $table - The table to reference the columns to build the WHERE string
	 * @param array $filter - The values to filter (based on data types in the table columns)
	 * @param array $columns - The overridden table columns, to change data types
	 *
	 * @return string - The mysql WHERE clause for the table $table
	 */

	protected function extractWhere($table, $filter, $columns = array())
	{
		$where = '1';

		if (!$filter) {
			return $where;
		}

		if (!is_array($filter)) {
			trigger_error(_l("%s(): \$filter must be an array. \$filter = '%s'", __METHOD__, print_r($filter, true)));
			return $where;
		}

		$method = array(
			self::NO_ESCAPE           => 'equals',
			self::TEXT                => 'like',
			self::AUTO_INCREMENT      => 'int',
			self::AUTO_INCREMENT_PK   => 'int',
			self::PRIMARY_KEY_INTEGER => 'int',
			self::FLOAT               => 'float',
			self::INTEGER             => 'int',
			self::DATETIME            => 'date',
			self::IMAGE               => 'equals',
		);

		if (strpos($table, ' ')) {
			list($table, $t) = explode(' ', $table, 2);
		} else {
			$t = $this->prefix . $table;
		}

		$columns += $this->getTableColumns($table);

		foreach ($filter as $key => $value) {
			if (strpos($key, '!') === 0) {
				$key = substr($key, 1);
				$not = true;
			} else {
				$not = false;
			}

			if (!isset($columns[$key])) {
				continue;
			}

			if (is_array($columns[$key])) {
				$type = isset($method[$columns[$key]['type']]) ? $method[$columns[$key]['type']] : 'text';
			} else {
				$type = $columns[$key];
			}

			switch ($type) {
				case 'like':
					if (!$value) {
						$where .= " AND `$t`.`$key` " . ($not ? '!=' : '=') . " ''";
					} elseif (is_array($value)) {
						$likes = array();

						foreach ($value as $v) {
							$likes[] = "`$t`.`$key` " . ($not ? 'NOT LIKE' : 'LIKE') . " '%" . $this->escape($v) . "%'";
						}

						$where .= " AND (" . implode(" OR ", $likes) . ")";
					} else {
						$where .= " AND `$t`.`$key` " . ($not ? 'NOT LIKE' : 'LIKE') . " '%" . $this->escape($value) . "%'";
					}
					break;

				case 'number':
				case 'float':
				case 'int':
					if (is_array($value)) {
						$low  = (isset($value['low']) && $value['low'] !== '') ? ($type === 'int' ? (int)$value['low'] : (float)$value['low']) : false;
						$high = (isset($value['high']) && $value['high'] !== '') ? ($type === 'int' ? (int)$value['high'] : (float)$value['high']) : false;

						if ($low !== false && $high !== false) {
							if ($high < $low) {
								$temp = $low;
								$low  = $high;
								$high = $temp;
							}

							$where .= " AND `$t`.`$key` " . ($not ? 'NOT' : '') . " BETWEEN $low AND $high";
						} elseif ($low !== false) {
							$where .= " AND `$t`.`$key` " . ($not ? '<' : '>=') . " " . $low;
						} elseif ($high !== false) {
							$where .= " AND `$t`.`$key` " . ($not ? '>' : '<=') . " " . $high;
						} elseif (!empty($value)) {
							array_walk($value, function (&$a) use ($type) {
								$a = $type === 'int' ? (int)$a : (float)$a;
							});

							$where .= " AND `$t`.`$key` " . ($not ? 'NOT' : '') . " IN (" . implode(',', $value) . ")";
						}
					} elseif ($value) {
						$value = $type === 'int' ? (int)$value : (float)$value;
						$where .= " AND `$t`.`$key` " . ($not ? "!=" : "=") . " " . $value;
					} else {
						$where .= " AND (`$t`.`$key` " . ($not ? 'NOT' : '') . " IN (0,'') " . ($not ? 'AND' : 'OR') . " `$t`.`$key` " . ($not ? 'IS NOT NULL' : 'IS NULL') . ")";
					}
					break;

				case 'date':
				case 'datetime':
				case 'time':
					if (is_array($value)) {
						$start = !empty($value['start']) ? format('date', $value['start']) : false;
						$end   = !empty($value['end']) ? format('date', $value['end']) : false;

						if (!$start && !$end) {
							array_walk($value, function (&$a) use ($type) {
								$a = format('date', $a);
							});

							$where .= " AND `$t`.`$key` " . ($not ? 'NOT' : '') . " IN ('" . implode("','", $value) . "')";
						} else {
							if ($start && $end) {
								if ($this->date->isAfter($start, $end)) {
									$temp  = $end;
									$end   = $start;
									$start = $temp;
								}

								$where .= " AND `$t`.`$key` BETWEEN '$start' AND '$end'";
							} elseif ($start) {
								$where .= " AND `$t`.`$key` >= '$start'";
							} else {
								$where .= " AND `$t`.`$key` <= '$end'";
							}
						}
					} else {
						$where .= " AND `$t`.`$key` " . ($not ? "!=" : "=") . " '" . ($value ? format('date', $value) . "'" : '');
					}
					break;

				case 'text':
				default:
					if (is_array($value)) {
						$where .= " AND `$t`.`$key` " . ($not ? "NOT IN" : "IN") . " ('" . implode("','", $this->escape($value)) . "')";
					} else {
						$where .= " AND `$t`.`$key` " . ($not ? "!=" : "=") . " '" . $this->escape($value) . "'";
					}
					break;
			}
		}

		return $where;
	}

	protected function extractOrderLimit($data, $table = null)
	{
		return array(
			$this->extractOrder($data, $table),
			$this->extractLimit($data),
		);
	}

	protected function extractOrder($data, $table = null)
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

		//Order
		$order = '';

		foreach ($data['sort'] as $sort => $ord) {
			$sort = $this->escape($sort);
			$t = '';

			if ($table) {
				if (is_array($table)) {
					foreach ($table as $tbl => $name) {
						if ($this->hasColumn($tbl, $sort)) {
							$t = $name;
							break;
						}
					}
				} else {
					$t = $table;
				}
			}

			if (strpos($sort, '.') === false) {
				$sort = ($t ? "`$t`." : '') . "`$sort`";
			}

			$ord = strtoupper($ord) === 'DESC' ? 'DESC' : 'ASC';

			$order .= ($order ? ',' : '') . "$sort $ord";
		}

		return $order ? "ORDER BY $order" : '';
	}

	protected function extractLimit($data)
	{
		//Limit
		$limit = '';

		if (isset($data['limit'])) {
			if ((int)$data['limit'] > 0) {

				$start = (isset($data['start']) && (int)$data['start'] > 0) ? (int)$data['start'] : 0;

				$limit = " LIMIT $start," . (int)$data['limit'];
			}
		}

		return $limit;
	}

	protected function hasIndex($table, $fields)
	{
		if (empty($fields)) {
			return '';
		}

		if (!is_array($fields)) {
			$fields = array($fields => 1);
		}

		$fields = array_keys($fields);

		$columns = $this->getTableColumns($table);

		foreach ($fields as $field) {
			if (!empty($columns[$field]['Index'])) {
				return $field;
			}
		}

		return false;
	}

	/**
	 * Be careful using this method! It may cause performance problems if not used properly.
	 * Note that $table can either be a query string or a table name. If it is a table name you must provide $sort and $filter.
	 * If $table is a query, this method uses EXPLAIN and can sometimes take as long as the original query to determine the optimal query.
	 *
	 * @param string $table - either the table name or a sql query string. If it is a query string, $sort and $filter MUST be empty.
	 * @param array $sort - the fields the query will sort on (ORDER BY clause).
	 * @param array $filter - the fields the query will filter on (WHERE clause).
	 * @return bool - if true, it is recommended to use SQL_CALC_FOUND_ROWS in the SELECT clause.
	 */
	protected function useCalcFoundRows($table, $sort = array(), $filter = array())
	{
		//Use EXPLAIN to determine optimal performance
		if (!$sort && !$filter) {
			$results = $this->queryRows("EXPLAIN $table");

			$fast_types = array(
				'system',
				'const',
				'eq_ref',
				'index',
				'range',
			);

			foreach ($results as $r) {
				if (empty($r['key']) || !in_array($r['type'], $fast_types)) {
					return false;
				}
			}

			return true;
		}

		//Guess optimal performance based on indexes used
		if (empty($sort['sort']) && !$filter) {
			return true;
		}

		$table_model = $this->getTableModel($table);

		if ($table_model['table_type'] === 'VIEW') {
			return true;
		}

		$indexed = $this->hasIndex($table, $sort['sort']);

		if ($indexed) {
			if (isset($filter[$indexed])) {
				return false;
			}
		}

		return true;
	}

	public function getTableColumns($table, $merge = array(), $filter = array(), $sort = true)
	{
		$table_model = $this->getTableModel($table);

		$columns = $table_model['columns'];

		//Merge
		if ($merge) {
			foreach ($merge as $field => $data) {
				if (isset($columns[$field])) {
					$columns[$field] = $data + $columns[$field];
				} //$filter === false - only return merged columns when specifically requested (do not want these if building query for example)
				elseif ($filter === false || isset($filter[$field])) {
					$columns[$field] = $data;
				}
			}
		}

		//Filter
		if ($filter) {
			foreach ($filter as $key => $f) {
				if ($f === false) {
					unset($columns[$key]);
					unset($filter[$key]);
				}
			}

			if ($filter) {
				$columns = array_intersect_key($columns, $filter);
			}
		}

		//Sort
		if ($sort) {
			//To avoid issues with PHP 5.3.3
			$temp = $columns;

			uksort($columns, function ($ka, $kb) use ($filter, $temp) {
				$a = $temp[$ka];
				$b = $temp[$kb];

				//sort as first if Primary Key
				if ($a['type'] === 'pk') {
					return -1;
				} elseif ($b['type'] === 'pk') {
					return 1;
				}

				$sort_a = isset($filter[$ka]) ? $filter[$ka] : 0;
				$sort_b = isset($filter[$kb]) ? $filter[$kb] : 0;

				//Sort by requested sort order
				if ($sort_a !== $sort_b) {
					return $sort_a > $sort_b;
				}

				$name_a = isset($a['display_name']) ? $a['display_name'] : $ka;
				$name_b = isset($b['display_name']) ? $b['display_name'] : $kb;

				//Sort by name by last resort
				return $name_a > $name_b;
			});
		}

		return $columns;
	}

	public function hasColumn($table, $column)
	{
		$model = $this->getTableModel($table);
		return isset($model['columns'][$column]);
	}

	public function getTableModel($table)
	{
		$table  = $this->db->hasTable($table);
		$schema = $this->db->getName();

		if (empty(self::$model[$schema][$table])) {
			$model = cache('model.' . $schema . '.' . $table);

			if (!$model) {
				$name = $this->escape($table);

				$model = $this->queryRow("SELECT table_schema, table_name, table_type, engine, version FROM information_schema.tables WHERE table_schema = '$schema' AND table_name = '$name'");

				$columns = $this->db->getTableColumns($table);

				$indexes = $this->queryRows("SHOW INDEX FROM `$name`");

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

					$field = explode('_', $column['Field']);
					array_walk($field, function (&$a) {
						$a = ucfirst($a);
					});

					$column['display_name'] = implode(' ', $field);

					$length = null;
					if (preg_match("/([a-z_]+)\\s*\\((\\d+)\\)?/", $column['Type'], $length)) {
						$column['Type']   = $length[1];
						$column['Length'] = (int)$length[2];
					} else {
						$column['Length'] = 0;
					}

					foreach ($indexes as $index) {
						if ($index['Column_name'] === $column['Field']) {
							$column['Index'][] = $index;
						}
					}
				}
				unset($column);

				$model['columns'] = $columns;
				$model['indexes'] = $indexes;

				cache('model.' . $schema . '.' . $table, $model);
			}

			self::$model[$schema][$table] = $model;
		}

		return self::$model[$schema][$table];
	}

	protected function getPrimaryKey($table)
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
