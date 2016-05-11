<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */
abstract class Model
{
	static $model = array();

	protected
		$t,
		$db,
		$error = array(),
		$related_tables = array();

	const
		TEXT = 'text',
		TEXTAREA = 'textarea',
		NO_ESCAPE = 'no-escape',
		IMAGE = 'image',
		INTEGER = 'int',
		FLOAT = 'float',
		DATETIME = 'datetime',
		PRIMARY_KEY_INTEGER = 'pk-int',
		AUTO_INCREMENT = 'ai',
		AUTO_INCREMENT_PK = 'pk';

	static $type_map = array(
		self::NO_ESCAPE           => 'equals',
		self::TEXTAREA            => 'like',
		self::TEXT                => 'like',
		self::AUTO_INCREMENT      => 'int',
		self::AUTO_INCREMENT_PK   => 'int',
		self::PRIMARY_KEY_INTEGER => 'int',
		self::FLOAT               => 'float',
		self::INTEGER             => 'int',
		self::DATETIME            => 'date',
		self::IMAGE               => 'equals',
	);

	public function __construct()
	{
		global $registry;

		$reg_key = strtolower(get_class($this));

		if (!$registry->has($reg_key)) {
			$registry->set($reg_key, $this);
		}

		//use default database (Note: setting the $db property - for example, in the constructor of a Model class - will allow the instance to use a different DB)
		if (!$this->db) {
			$this->setDb($registry->get('db'));
		}
	}

	public function __get($key)
	{
		global $registry;

		return $registry->get($key);
	}

	public function setDb($db)
	{
		$this->db = $db;
		$this->t  = &$this->db->t;
	}

	public function getDb()
	{
		return $this->db;
	}

	protected function load($path, $class = null)
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
			return isset($this->error[$type]) ? $this->error[$type] : null;
		}

		return $this->error;
	}

	public function fetchError($type = null)
	{
		$error = $this->getError($type);

		$this->clearErrors($type);

		return $error;
	}

	public function clearErrors($type)
	{
		if ($type) {
			unset($this->error[$type]);
		} else {
			$this->error = array();
		}
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
			$this->error = $this->db->fetchError();
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

	protected function queryRows($sql, $index = null, $total = false, $sql_calc_found_rows = null)
	{
		if ($total) {
			return $this->queryTotal($sql, $index, $sql_calc_found_rows);
		}

		return $this->db->queryRows($sql, $index);
	}

	protected function queryColumn($sql, $column_key = null, $index_key = null)
	{
		return $this->db->queryColumn($sql, $column_key, $index_key);
	}

	/**
	 * IMPORTANT: This method is only guaranteed to work on queries that do not contain sub queries!
	 * Also, not compatible with GROUP BY and HAVING clauses. Using aggregate functions may cause undesirable results.
	 *
	 * Use this to optimally query sorted / filtered rows using the LIMIT clause from
	 * the database along with the total number of rows (minus the LIMIT clause).
	 *
	 * @param string $sql                 - The query string without any sub queries (no guarantee it will work on sub
	 *                                    queries).
	 * @param string $index               - the index field to use for the rows returned
	 * @param bool   $use_calc_found_rows - force the query to either use or not use the SQL_CALC_FOUND_ROWS statement.
	 *
	 * @return array - an array with array( 0 => rows, 1 => total ). USAGE HINT: list($rows, $total) =
	 *               $this->queryTotal(...);
	 */

	protected function queryTotal($sql, $index = null, $use_calc_found_rows = null)
	{
		if ($use_calc_found_rows === null) {
			$use_calc_found_rows = $this->useCalcFoundRows($sql);
		}

		if ($use_calc_found_rows) {
			$sql = preg_replace("/^SELECT/i", "SELECT SQL_CALC_FOUND_ROWS", $sql);
		}

		$rows = $this->queryRows($sql, $index);

		if ($use_calc_found_rows) {
			$total = $this->queryVar("SELECT FOUND_ROWS()");
		} else {
			$total = $this->queryVar(preg_replace("/^SELECT/i", "SELECT COUNT(*), ", $sql));
		}

		return array(
			$rows,
			$total,
		);
	}

	public function queryField($table, $field, $where)
	{
		$table = $this->escape($table);

		$where = $this->getWhere($table, $where, null, null, true);

		return $this->queryVar("SELECT " . $this->escape($field) . " FROM `" . $this->t[$table] . "` WHERE $where LIMIT 1");
	}

	public function queryFields($table, $fields, $where)
	{
		$table = $this->escape($table);

		$where = $this->getWhere($table, $where, null, null, true);

		return $this->queryRow("SELECT `" . implode(',', $this->escape($fields)) . " FROM `" . $this->t[$table] . "` WHERE $where LIMIT 1");
	}

	protected function insert($table, $data, $update = false)
	{
		$t = $this->t[$table];

		$this->actionFilter($t, 'insert', $data);

		$values = $this->getInsertString($t, $data, false);

		if (!$values) {
			$this->error['values'] = _l("There were no valid fields set to insert.");

			return false;
		}

		if ($update) {
			$success = $this->query("INSERT INTO `$t` SET $values ON DUPLICATE KEY UPDATE $values");
		} else {
			$success = $this->query("INSERT INTO `$t` SET $values");
		}

		if (!$success) {
			trigger_error(_l("There was a problem inserting entry for %s and was not modified.", $table));

			if ($this->hasError('query')) {
				$this->error['query'] = $this->fetchError('query');
				trigger_error($this->error['query']);
			}

			return false;
		}

		$row_id = $this->getLastId();

		if (!$row_id) {
			$primary_key = $this->getPrimaryKey($t);

			if ($primary_key && isset($data[$primary_key])) {
				$row_id = $data[$primary_key];
			} else {
				$row_id = true;
			}
		}

		if ($this->db->isHistoryTable($table)) {
			$this->history($t, $row_id, 'insert', $data, true);

		}

		return $row_id;
	}

	protected function update($table, $data, $where = null)
	{
		$t = $this->t[$table];

		$this->actionFilter($t, 'update', $data, $where);

		$primary_key = $this->getPrimaryKey($t);

		$values = $this->getInsertString($t, $data);

		if (!$values) {
			$this->error['values'] = _l("There were no valid fields set to update.");

			return false;
		}

		$update_id = true;

		if (!$where) {
			if (empty($data[$primary_key])) {
				return $this->insert($t, $data);
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

		$where = $this->getWhere($t, $where, '', '', true);

		$success = $this->query("UPDATE `$t` SET $values WHERE $where");

		if (!$success) {
			trigger_error(_l("There was a problem updating entry for %s and was not modified.", $table));

			if ($this->hasError('query')) {
				trigger_error($this->fetchError('query'));
			}

			return false;
		}

		if ($update_id !== true && $this->db->isHistoryTable($table)) {
			$this->history($t, $update_id, 'update', $data, true);
		}

		return $update_id;
	}

	protected function delete($table, $where = null)
	{
		$t = $this->t[$table];

		$this->actionFilter($t, 'delete', $data);

		$where = $this->getWhere($t, $where, null, null, true);

		$success = $this->query("DELETE FROM `$t` WHERE $where");

		if (!$success) {
			trigger_error(_l("There was a problem deleting entry for %s and was not modified.", $table));

			if ($this->hasError('query')) {
				trigger_error($this->fetchError('query'));
			}

			return false;
		}

		if ($this->db->isHistoryTable($table)) {
			$delete_id = false;

			if (is_array($where)) {
				$primary_key = $this->getPrimaryKey($t);

				if (isset($where[$primary_key])) {
					$delete_id = (int)$where[$primary_key];
				}
			} else {
				$delete_id = (int)$where;
			}

			if ($delete_id) {
				$this->history($t, $delete_id, 'delete', $data, true);
			}
		}

		return true;
	}

	public function history($table, $record_id, $action, $data, $message = null, $status = null)
	{
		$table = $this->t[$table];

		if ($table !== $this->t['history']) {
			$columns = $this->getTableColumns($table);
			$data    = array_intersect_key($data, $columns);

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
				'user_id'   => user_info('user_id'),
				'table'     => $table,
				'record_id' => $record_id,
				'action'    => $action,
				'status'    => $status,
				'data'      => $json_data,
				'date'      => $this->date->now(),
			);

			if ($message) {
				$history['message'] = $message === true ? $this->router->getPath() : $message;
			}

			$this->insert('history', $history);
		}
	}

	protected function getWhere($table, $where, $prefix = '', $glue = '', $use_primary_key = false)
	{
		if (!$where) {
			return '1';
		}

		if (is_integer($where) || is_numeric($where)) {
			$primary_key = $this->getPrimaryKey($table);

			if (!$primary_key) {
				trigger_error("WHERE statement " . _l("%s does not have an integer primary key!", $table));

				return null;
			}

			return "`$primary_key` = '" . $this->escape($where) . "'";
		} elseif (is_string($where)) {
			return $where;
		}

		$where = $this->getEscapedValues($table, $where, $use_primary_key);

		if (!$glue) {
			$glue = 'AND';
		}

		$values = '';

		foreach ($where as $key => $value) {
			$values .= ($values ? ' ' . $glue . ' ' : '') . ($prefix ? "`$prefix`." : '') . "`$key` = '$value'";
		}

		return $values ? $values : '1';
	}

	protected function getInsertString($table, $data, $use_primary_key = false)
	{
		$data = $this->getEscapedValues($table, $data, $use_primary_key);

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

	protected function extractColumns($table, $options)
	{
		static $column_cache = array();

		$key = $table . serialize($options);

		if (!isset($column_cache[$key])) {
			$table_list = !empty($options['join']) ? $options['join'] : array();
			$columns    = array();

			//Check for $options columns
			if (!empty($options['columns'])) {

				//TODO: This is for compatibility testing. Remove when safe
				if (!is_array($options['columns'])) {
					trigger_error("options[columns] was not an array! Please fix");
					exit;
				}

				$columns = $options['columns'];

				//Convert all columns to array
				foreach ($columns as $c => &$col) {
					if (!is_array($col)) {
						if (is_string($col)) {
							if (strpos($c, '#') === 0) {
								$col = array(
									'type'  => 'text',
									'field' => $col,
								);
							} else {
								$col = array('type' => $col);
							}
						} else {
							$col = array();
						}
					}

					$col['show'] = true;
				}
				unset($col);
			}

			//Parse $table alias and add to table list
			if ($table) {
				$table_list[$table] = array(
					'alias' => !empty($options['alias']) ? $options['alias'] : $this->t[$table],
				);
			}

			//Map columns to a table alias
			foreach ($table_list as $name => $data) {
				$alias = !empty($data['alias']) ? $data['alias'] : $this->t[$name];

				$cols = (array)$this->getTableColumns($name);

				foreach ($cols as $c => $col) {
					if (isset($columns[$c])) {
						$columns[$c] += $col;
					} else {
						$columns[$c]         = $col;
						$columns[$c]['show'] = empty($options['columns']);
					}

					$columns[$c]['table_alias'] = $alias;
				}
			}

			$column_cache[$key] = $columns;
		}

		return $column_cache[$key];
	}

	protected function extractSelect($table, $options)
	{
		if (!isset($this->t[$table])) {
			trigger_error(_l("Table %s does not exist!", $table));

			return false;
		}

		if (!$options || $options === '*') {
			return "`{$this->t[$table]}`.*";
		}

		if (is_string($options)) {
			return $options;
		}

		$columns = $this->extractColumns($table, $options);

		//If no columns were resolved, hopefully the dev specified exactly which columns, or there was likely a mistake
		if (!$columns) {
			return (!empty($options['columns']) && is_string($options['columns'])) ? $options['columns'] : '*';
		}

		$select = '';

		foreach ($columns as $c => $col) {
			if (!empty($col['show'])) {
				if (strpos($c, '#') === 0) {
					$str = $col['field'];
				} elseif (!empty($col['field'])) {
					$str = "{$col['field']} as `$c`";
				} elseif (!empty($col['table_alias'])) {
					$str = "`{$col['table_alias']}`.`$c`";
				} else {
					//Column is not in any tables and field is not specified, so this column should not be included
					continue;
				}

				$select .= ($select ? ',' : '') . $str;
			}
		}

		return $select;
	}

	protected function extractFrom($table, $options)
	{
		$from = $this->t[$table] . (!empty($options['alias']) ? " `{$options['alias']}`" : '');

		$columns = $this->extractColumns($table, $options);

		//Extract JOIN Clauses
		if (!empty($options['join'])) {
			foreach ($options['join'] as $join_table => $join) {
				if (strpos($join_table, '#') === 0) {
					$from .= ' ' . $join;
					continue;
				}

				if (empty($join['on'])) {
					trigger_error(_l("Error extracting FROM clause: 'on' is required"));

					return false;
				}

				$join_type  = !empty($join['type']) ? $join['type'] : "LEFT JOIN";
				$join_table = isset($join['table']) ? $join['table'] : $join_table;
				$j          = isset($join['alias']) ? $join['alias'] : '';

				$from .= " $join_type `{$this->t[$join_table]}` $j " . (strpos($join['on'], '=') ? 'ON' : 'USING') . " (" . $join['on'] . ")";
			}
		}

		return $from;
	}

	protected function extractWhere($table, $filter, $options = array())
	{
		$where    = '';
		$having   = !empty($options['having']) ? $options['having'] : '';
		$group_by = !empty($options['group_by']) ? $options['group_by'] : '';

		if (is_string($filter)) {
			$where = $filter;
		} elseif ($filter) {
			$columns = $this->extractColumns($table, $options);

			//Build WHERE statement from $filter
			foreach ($filter as $key => $value) {
				$not = false;

				if (strpos($key, '#') === 0) {
					$where .= ($where ? ' AND ' : '') . $value;
					continue;
				} elseif (strpos($key, '!') === 0) {
					$key = substr($key, 1);
					$not = true;
				}

				if (!isset($columns[$key])) {
					continue;
				}

				$column = $columns[$key];

				$is_table_col = !empty($column['table_alias']);

				$tc = $is_table_col ? "`{$column['table_alias']}`.`$key`" : "`$key`";

				if (!empty($column['compare_type'])) {
					$type = $column['compare_type'];
				} elseif (isset(self::$type_map[$column['type']])) {
					$type = self::$type_map[$column['type']];
				} else {
					$type = 'text';
				}

				$expression = '';

				switch ($type) {
					case 'like':
						if (!$value) {
							$expression .= "$tc " . ($not ? '!=' : '=') . " ''";
						} elseif (is_array($value)) {
							$likes = array();

							foreach ($value as $v) {
								$likes[] = "$tc " . ($not ? 'NOT LIKE' : 'LIKE') . " '%" . $this->escape($v) . "%'";
							}

							$expression .= "(" . implode(($not ? ' AND ' : ' OR '), $likes) . ")";
						} else {
							$expression .= "$tc " . ($not ? 'NOT LIKE' : 'LIKE') . " '%" . $this->escape($value) . "%'";
						}
						break;

					case 'number':
					case 'float':
					case 'int':
						if (is_array($value)) {
							$low  = (isset($value['gte']) && $value['gte'] !== '') ? ($type === 'int' ? (int)$value['gte'] : (float)$value['gte']) : false;
							$high = (isset($value['lte']) && $value['lte'] !== '') ? ($type === 'int' ? (int)$value['lte'] : (float)$value['lte']) : false;

							if ($low !== false && $high !== false) {
								if ($high < $low) {
									$temp = $low;
									$low  = $high;
									$high = $temp;
								}

								$expression .= "$tc " . ($not ? 'NOT' : '') . " BETWEEN $low AND $high";
							} elseif ($low !== false) {
								$expression .= "$tc " . ($not ? '<' : '>=') . " " . $low;
							} elseif ($high !== false) {
								$expression .= "$tc " . ($not ? '>' : '<=') . " " . $high;
							} elseif (!empty($value)) {
								array_walk($value, function (&$a) use ($type) {
									$a = $type === 'int' ? (int)$a : (float)$a;
								});

								$expression .= "$tc " . ($not ? 'NOT' : '') . " IN (" . implode(',', $value) . ")";
							}
						} elseif ($value) {
							$value = $type === 'int' ? (int)$value : (float)$value;
							$expression .= "$tc " . ($not ? "!=" : "=") . " " . $value;
						} else {
							$expression .= "($tc " . ($not ? 'NOT' : '') . " IN (0,'') " . ($not ? 'AND' : 'OR') . " $tc " . ($not ? 'IS NOT NULL' : 'IS NULL') . ")";
						}
						break;

					case 'date':
					case 'datetime':
					case 'timestamp':
					case 'time':
						if (is_array($value)) {
							$start = !empty($value['gte']) ? format('date', $value['gte']) : false;
							$end   = !empty($value['lte']) ? format('date', $value['lte']) : false;

							if (!$start && !$end) {
								if (isset($value['gte']) || isset($value['lte'])) {
									break;
								}

								array_walk($value, function (&$a) use ($type) {
									$a = format('date', $a);
								});

								$expression .= "$tc " . ($not ? 'NOT' : '') . " IN ('" . implode("','", $value) . "')";
							} else {
								if ($start && $end) {
									if (date_compare($start, '>', $end)) {
										$temp  = $end;
										$end   = $start;
										$start = $temp;
									}

									$expression .= "$tc BETWEEN '$start' AND '$end'";
								} elseif ($start) {
									$expression .= "$tc >= '$start'";
								} else {
									$expression .= "$tc <= '$end'";
								}
							}
						} elseif ($value) {
							$expression .= "$tc " . ($not ? "!=" : "=") . " '" . format('date', $value) . "'";
						} else {
							$expression .= "($tc IS NULL OR $tc = '')";
						}
						break;

					case 'text':
					default:
						if (is_array($value)) {
							$expression .= "$tc " . ($not ? "NOT IN" : "IN") . " ('" . implode("','", $this->escape($value)) . "')";
						} else {
							$expression .= "$tc " . ($not ? "!=" : "=") . " '" . $this->escape($value) . "'";
						}
						break;
				}

				if ($is_table_col) {
					$where .= ($where ? ' AND ' : '') . $expression;
				} else {
					$having .= ($having ? ' AND ' : '') . $expression;
				}
			}
		}

		if (!$where) {
			$where = '1';
		}

		if ($group_by) {
			$where .= " GROUP BY $group_by";
		}

		if ($having) {
			$where .= " HAVING $having";
		}

		return $where;
	}

	protected function extractOrder($table, $sort, $options = array())
	{
		if (empty($sort)) {
			return '';
		}

		if (!is_array($sort)) {
			$sort = array($sort => 'ASC');
		}

		//Order
		$order   = '';
		$columns = $this->extractColumns($table, $options);

		foreach ($sort as $col => $ord) {
			if (strpos($col, '#') === 0) {
				$order .= ($order ? ',' : '') . $ord;
				continue;
			}

			if (strpos($col, '.') === false) {
				$alias = !empty($columns[$col]['table_alias']) ? $columns[$col]['table_alias'] : '';
				$col   = ($alias ? "`$alias`." : '') . "`$col`";
			}

			$ord = strtoupper($ord) === 'DESC' ? 'DESC' : 'ASC';

			$order .= ($order ? ',' : '') . $this->escape($col) . ' ' . $ord;
		}

		return $order;
	}

	protected function extractLimit($options)
	{
		if (!empty($options['limit']) && $options['limit'] > 0) {
			if (!empty($options['page'])) {
				$options['start'] = (max(1, (int)$options['page']) - 1) * (int)$options['limit'];
			}

			return max(0, isset($options['start']) ? $options['start'] : 0) . ',' . (int)$options['limit'];
		}
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
	 * Note that $table can either be a query string or a table name. If it is a table name you must provide $sort and
	 * $filter. If $table is a query, this method uses EXPLAIN and can sometimes take as long as the original query to
	 * determine the optimal query.
	 *
	 * @param string $table  - either the table name or a sql query string. If it is a query string, $sort and $filter
	 *                       MUST be empty.
	 * @param array  $sort   - the fields the query will sort on (ORDER BY clause).
	 * @param array  $filter - the fields the query will filter on (WHERE clause).
	 * @return bool - if true, it is recommended to use SQL_CALC_FOUND_ROWS in the SELECT clause.
	 */
	protected function useCalcFoundRows($table, $sort = array(), $filter = array())
	{
		//Use EXPLAIN to determine optimal performance
		if (!$sort && !$filter) {
			if (preg_match("/( GROUP BY | HAVING )/i", $table)) {
				return true;
			}

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
		if (!$sort && !$filter) {
			return true;
		}

		$table_model = $this->getTableModel($table);

		if ($table_model['table_type'] === 'VIEW') {
			return true;
		}

		$indexed = $this->hasIndex($table, $sort);

		if ($indexed) {
			if (isset($filter[$indexed])) {
				return false;
			}
		}

		return true;
	}

	public function getTableColumns($table, $filter = array(), $merge = array(), $sort = true)
	{
		$table_model = $table ? $this->getTableModel($table) : false;

		if ($table_model) {
			$columns = $table_model['columns'];

			//Merge
			if ($merge) {
				foreach ($merge as $field => $data) {
					if (isset($columns[$field])) {
						$columns[$field] = $data + $columns[$field];
					} //$filter === false - only return merged columns when specifically requested (do not want these if building query for example)
					elseif (!$filter || isset($filter[$field])) {
						$columns[$field] = $data;
					}
				}
			}
		} else {
			$columns = $merge;
		}

		if (!$columns) {
			return array();
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

				if (isset($a['sort_order']) || isset($b['sort_order'])) {
					if (!isset($a['sort_order'])) {
						return 1;
					} elseif (!isset($b['sort_order'])) {
						return -1;
					} else {
						return $a['sort_order'] > $b['sort_order'];
					}
				}

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

				$name_a = isset($a['label']) ? $a['label'] : $ka;
				$name_b = isset($b['label']) ? $b['label'] : $kb;

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
		if (!isset($this->t[$table])) {
			if ($this->db->tableExists($table)) {
				$t = $table;
			} else {
				return false;
			}
		} else {
			$t = $this->t[$table];
		}

		$schema = $this->db->getSchema();

		if (empty(self::$model[$schema][$t])) {
			$model = cache('model.' . $schema . '.' . $t);

			if (!$model || empty($model['columns'])) {
				$model = $this->queryRow("SELECT table_schema, table_name, table_type, engine, version FROM information_schema.tables WHERE table_schema = '$schema' AND table_name = '$t'");

				$columns = $this->db->getTableColumns($t);

				$indexes = $this->queryRows("SHOW INDEX FROM `$t`");

				foreach ($columns as &$column) {
					$type = strtolower(trim(preg_replace("/(\\(|\\s).*$/", '', $column['Type'])));

					if ($type === 'text') {
						$type = 'textarea';
					} else {
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
							'timestamp' => self::DATETIME,
							'binary'    => self::NO_ESCAPE,
							'varbinary' => self::NO_ESCAPE,
						);

						$type = isset($cast[$type]) ? $cast[$type] : self::TEXT;
					}

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

					$column['type']   = $type;
					$column['sort']   = true;
					$column['filter'] = true;

					$field = explode('_', $column['Field']);
					array_walk($field, function (&$a) {
						$a = ucfirst($a);
					});

					$column['label'] = implode(' ', $field);

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

				cache('model.' . $schema . '.' . $t, $model);
			}

			self::$model[$schema][$t] = $model;
		}

		return self::$model[$schema][$t];
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

	private function actionFilter($table, $action, &$data)
	{

		//TODO TEMPORARILY DISABLED!
		return;


		$hooks = option('db_hooks');

		if ($hooks && !empty($hooks[$table][$action])) {
			foreach ($hooks[$table][$action] as $hook) {
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
							$method,
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
