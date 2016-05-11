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
class Query extends Library
{
	private $clauses, $clauses_pos, $query;

	protected
		$table = null,
		$alias = null,
		$pk = null,
		$related_tables = array(),
		$columns = array();

	public
		$select,
		$from,
		$where,
		$group_by,
		$having,
		$order_by,
		$limit;

	public function __construct($init = array())
	{
		$defaults = array(
			'db'          => null,
			'table'       => null,
			'primary_key' => null,
		);

		foreach ($defaults as $key => $default) {
			$this->$key = isset($init[$key]) ? $init[$key] : $default;
		}

		parent::__construct();

		if (isset($init['related_tables'])) {
			$this->setRelatedTables($init['related_tables']);
		}

		if (isset($init['columns'])) {
			$this->setColumns($init['columns']);
		}
	}

	public function setTable($table, $alias = null, $pk = null)
	{
		if (!isset($this->t[$table])) {
			trigger_error(_l("Unable to set %s as the primary table because it does not exist in the database %s.", $table, $this->db->getName()));

			return false;
		}

		$this->table = $this->t[$table];
		$this->alias = $alias ?: $this->table;
		$this->pk    = $pk ?: $this->getPrimaryKey($this->table);
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getAlias()
	{
		return $this->alias;
	}

	public function getPk()
	{
		return $this->pk;
	}

	public function setRelatedTables($tables)
	{
		foreach ($tables as $table => $join) {
			if (isset($this->t[$table])) {
				if (is_string($join)) {
					$join = array(
						'type'    => '',
						'table'   => $join,
						'alias'   => '',
						'require' => true,
					);
				} else {
					if (empty($join['on'])) {
						trigger_error(_l("Must specify join 'on' for the related table %s", $table));
						continue;
					}

					$join += array(
						'type'    => "LEFT JOIN",
						'table'   => $table,
						'alias'   => false,
						'require' => false,
					);
				}

				$this->related_tables[$this->t[$table]] = $join;
			} else {
				trigger_error(_l("The table % s does not exist in the database % s . Unable to add related table . ", $table, $this->db->getName()));
			}
		}
	}

	public function getRelatedTables()
	{
		return $this->related_tables;
	}

	public function setColumns(array $columns, $merge = true)
	{
		//Convert all columns to column array format
		foreach ($columns as $c => &$col) {
			if (!is_array($col)) {
				if (is_string($col)) {
					if (strpos($c, '#') === 0) {
						$col = array(
							'type'  => 'text',
							'field' => $col,
							'alias' => false,
						);
					} else {
						$col = array('type' => $col);
					}
				} else {
					$col = array();
				}
			}

			$col['table'] = !empty($col['table']) ? $this->t[$col['table']] : '';

			$col += array(
				'show'        => true,
				'alias'       => $c,
				'table_alias' => $col['table'],
			);
		}
		unset($col);

		if ($merge) {
			$table_list = array();

			if ($this->table) {
				$table_list[$this->table] = array('alias' => $this->alias);
			}

			$table_list += $this->related_tables;

			//Map columns to a table / alias, and fill additional data
			foreach ($table_list as $name => $data) {
				$table = $this->t[$name];
				$alias = !empty($data['alias']) ? $data['alias'] : $table;

				$table_columns = (array)$this->getTableColumns($table);

				foreach ($table_columns as $c => $col) {
					if (isset($columns[$c])) {
						$columns[$c] += $col;
					} else {
						$columns[$c]         = $col;
						$columns[$c]['show'] = !$merge;
					}

					$columns[$c] += array(
						'table'       => $table,
						'table_alias' => $alias,
					);
				}
			}
		}

		$this->columns = $columns;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function buildSelect()
	{
		//If no columns were resolved, select all columns
		if (!$this->columns) {
			return $this->select = '*';
		}

		$this->select = '';

		foreach ($this->columns as $c => $col) {
			if (!empty($col['show'])) {
				if (!empty($col['field'])) {
					$str = $col['field'] . ($col['alias'] ? " as `$col[alias]`" : '');
				} elseif (!empty($col['table_alias'])) {
					$str = "`{$col['table_alias']}` . `$c`";
				} else {
					//Column is not in any tables and field is not specified, so this column should not be included
					continue;
				}

				$this->select .= ($select ? ',' : '') . $str;
			}
		}

		return $this->select;
	}

	public function buildFrom()
	{
		$this->from = "`$this->table`" . ($this->alias ? "`{$this->alias}`" : '');

		foreach ($this->columns as $column) {
			if ($column['show'] && isset($this->related_tables[$column['table']])) {
				$this->related_tables[$column['table']]['require'] = true;
			}
		}

		//Extract JOIN Clauses
		foreach ($this->related_tables as $table => $join) {
			if ($join['require']) {
				if ($join['type']) {
					$this->from .= " {$join['type']} `{$join['table']}` {$join['alias']} " . (strpos($join['on'], '=') ? 'ON' : 'USING') . " (" . $join['on'] . ")";
				} else {
					$this->from .= ' ' . $join['table'];
				}
			}
		}

		return $this->from;
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

				$tc = $is_table_col ? "`{$column['table_alias']}` . `$key`" : "`$key`";

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

								$expression .= "$tc " . ($not ? 'NOT' : '') . " IN(" . implode(',', $value) . ")";
							}
						} elseif ($value) {
							$value = $type === 'int' ? (int)$value : (float)$value;
							$expression .= "$tc " . ($not ? " != " : " = ") . " " . $value;
						} else {
							$expression .= "($tc " . ($not ? 'NOT' : '') . " IN(0, '') " . ($not ? 'AND' : 'OR') . " $tc " . ($not ? 'IS NOT NULL' : 'IS NULL') . ")";
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

								$expression .= "$tc " . ($not ? 'NOT' : '') . " IN('" . implode("', '", $value) . "')";
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
							$expression .= "$tc " . ($not ? " != " : " = ") . " '" . format('date', $value) . "'";
						} else {
							$expression .= "($tc IS null OR $tc = '')";
						}
						break;

					case 'text':
					default:
						if (is_array($value)) {
							$expression .= "$tc " . ($not ? "NOT IN" : "IN") . " ('" . implode("','", $this->escape($value)) . "')";
						} else {
							$expression .= "$tc " . ($not ? " != " : " = ") . " '" . $this->escape($value) . "'";
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
				$col   = ($alias ? "`$alias` . " : '') . "`$col`";
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


	public function build($sort = array(), $filter = array(), $select = array())
	{

	}


	public function getQuery()
	{
		return $this->query;
	}

	public function getOffset($key)
	{
		return isset($this->clauses_pos[$key]) ? $this->clauses_pos[$key] : null;
	}

	public function getClause($key, $prefix = true)
	{
		if ($this->clauses_pos) {
			if (!isset($this->clauses[$key])) {
				$this->clauses[$key] = false;

				if (!empty($this->clauses_pos[$key]) || $key === 'select') {
					$from = $this->clauses_pos[$key];

					if ($from !== false) {
						foreach ($this->clauses_pos as $to) {
							if ($to > $from) {
								$this->clauses[$key] = substr($this->query, $from, $to - $from);
								break;
							}
						}

						if ($to <= $from) {
							$this->clauses[$key] = substr($this->query, $from);
						}
					}
				}
			}

			return $prefix ? $this->clauses[$key] : substr($this->clauses[$key], strlen($key));
		}
	}

	public function getClauses($clauses)
	{
		if (!is_array($clauses)) {
			$clauses = func_get_args();
		}

		$sql = '';

		foreach ($clauses as $clause) {
			$sql .= $this->getClause($clause);
		}

		return $sql;
	}

	public function parse($sql)
	{
		if (!preg_match(" /^SELECT / i", $sql)) {
			return false;
		}

		$this->query   = $sql;
		$this->clauses = array();

		$this->clauses_pos = array(
			'select'   => 0,
			'from'     => false,
			'where'    => false,
			'group by' => false,
			'having'   => false,
			'order by' => false,
			'limit'    => false,
		);

		$targets = array_keys($this->clauses_pos);

		$target_firsts = array();

		foreach ($targets as $k => $t) {
			$target_firsts[$t[0]] = $k;
		}

		$target_index = 0;
		$target_last  = 0;
		$word_index   = 0;

		$escape = false;
		$quote  = '';
		$paren  = 0;

		for ($i = 7; $i < strlen($sql); $i++) {
			$c = $sql[$i];

			switch ($c) {
				case '\\':
					$escape = !$escape;
					break;

				case '`':
				case '"':
				case '\'':
					if ($quote === $c) {
						if (!$escape) {
							$quote = '';
						}
					} elseif (!$quote) {
						$quote = $c;
					}
					break;

				case '(':
				case ')':
					if (!$quote) {
						if ($paren && $c === ')') {
							$paren--;
						} elseif ($c === '(') {
							$paren++;
						}
					}
					break;


				//Parse out the $this->query clause targets (eg: from, where, etc...)
				default:
					if (!$quote && !$paren) {
						$l = strtolower($c);

						if ($target_index) {
							if ($targets[$target_index][$word_index] === $l) {
								$word_index++;

								if ($target_last === $word_index) {
									$this->clauses_pos[$targets[$target_index]] = $i - $target_last;
									unset($target_firsts[$targets[$target_index][0]]);
									$target_index = 0;
								}
							} else {
								$target_index = 0;
							}
						} elseif (isset($target_firsts[$l]) && preg_match("/[^a-z\\d_\\-\\.]/i", $sql[$i - 1])) {
							$target_index = $target_firsts[$l];
							$word_index   = 1;
							$target_last  = strlen($targets[$target_index]);
						}
					}

					break;
			}
		}

		return true;
	}
}
