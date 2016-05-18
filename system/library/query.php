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
class Query extends Library
{
	private $clauses, $clauses_pos;

	protected
		$table = null,
		$alias = null,
		$pk = null,
		$related_tables = array(),
		$columns = array(),
		$sort = array(),
		$filter = array(),
		$query = null,
		$index = null;

	public
		$select,
		$from,
		$where,
		$group_by,
		$having,
		$order_by,
		$limit;

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

	public function __construct($init = array())
	{
		$init += array(
			'db'              => null,
			'table'           => null,
			'pk'              => null,
			'alias'           => null,
			'related_tables'  => array(),
			'columns'         => array(),
			'require_columns' => array(),
			'sort'            => array(),
			'filter'          => array(),
			'limit'           => 0,
			'page'            => 1,
			'start'           => 0,
			'index'           => null,
		);

		if ($init['db']) {
			$this->setDb($init['db']);
		}

		parent::__construct();

		$this->setTable($init['table'], $init['alias'], $init['pk']);
		$this->setRelatedTables($init['related_tables']);
		$this->setColumns($init['columns']);
		$this->requireColumns($init['require_columns']);
		$this->setSort($init['sort']);
		$this->setFilter($init['filter']);
		$this->setLimit($init['limit'], $init['page'], $init['start']);
		$this->setIndex($init['index']);
	}

	public function reset()
	{
		$this->select = $this->from = $this->where = $this->group_by = $this->having = $this->order_by = $this->limit = null;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getTableAlias($table = null)
	{
		if ($table) {
			$table = $this->t[$table];

			if ($table && $table !== $this->table) {
				return isset($this->related_tables[$table]) ? $this->related_tables[$table]['alias'] : '';
			}
		}

		return $this->alias;
	}

	public function getPk()
	{
		return $this->pk;
	}

	public function getRelatedTables()
	{
		return $this->related_tables;
	}

	public function getSort()
	{
		return $this->sort;
	}

	public function getFilter()
	{
		return $this->filter;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function getIndex()
	{
		return $this->index;
	}

	public function getQuery()
	{
		return $this->query;
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

		$this->reset();
	}

	public function setRelatedTables($tables)
	{
		foreach ($tables as $name => $join) {
			if (empty($join['table'])) {
				$join['table'] = $name;
			}

			if (isset($this->t[$join['table']])) {
				if (empty($join['on'])) {
					trigger_error(_l("Must specify join 'on' for the related table %s", $name));
					continue;
				}

				$join['table'] = $this->t[$join['table']];

				$join += array(
					'type'        => "LEFT JOIN",
					'alias'       => false,
					'is_required' => false,
				);

				$this->related_tables[$name] = $join;
			} else {
				trigger_error(_l("The table %s does not exist in the database %s . Unable to add related table . ", $join['table'], $this->db->getName()));
			}
		}

		$this->reset();
	}

	public function setColumns(array $columns, $merge = true)
	{
		$this->columns = array();

		//Convert all columns to column array format
		foreach ($columns as $c => &$col) {
			$this->addColumn((is_array($col) && !empty($col['alias'])) ? $col['alias'] : $c, $col);
		}
		unset($col);

		if ($merge) {
			$this->mergeColumns();

			//Set all the main table columns as selected
			if (!$columns) {
				$this->requireAllTableColumns($this->table);
			}
		}

		$this->reset();
	}

	public function addColumn($name, $column)
	{
		if (!is_array($column)) {
			if (is_string($column)) {
				if (strpos($name, '#') === 0) {
					$column = array(
						'type'  => 'text',
						'field' => $column,
						'alias' => false,
					);
				} else {
					$column = array('type' => $column);
				}
			} else {
				$column = array();
			}
		}

		$column['table'] = !empty($column['table']) ? $this->t[$column['table']] : null;

		$column += array(
			'is_selected' => true,
			'is_required' => true,
			'alias'       => $name,
			'table_alias' => $column['table'] ? $this->getTableAlias($column['table']) : null,
		);

		$this->columns[$name] = $column;
	}

	public function requireAllTableColumns($table)
	{
		foreach ($this->columns as $c => &$col) {
			if ($col['table'] === $table) {
				$this->requireColumn($c);
			}
		}
	}

	public function requireColumn($name)
	{
		if (isset($this->columns[$name])) {
			$this->columns[$name]['is_required'] = true;
			$this->columns[$name]['is_selected'] = true;
		}
	}

	public function requireColumns($columns)
	{
		foreach ($columns as $c => $col) {
			$this->requireColumn(is_numeric($c) ? $col : $c);
		}
	}

	public function mergeColumns()
	{
		$table_list = array();

		if ($this->table) {
			$table_list[$this->table] = array(
				'table' => $this->table,
				'alias' => $this->alias
			);
		}

		$table_list += $this->related_tables;

		//Map columns to a table / alias, and fill additional data
		foreach ($table_list as $data) {
			$table = $this->t[$data['table']];
			$alias = !empty($data['alias']) ? $data['alias'] : $table;

			$table_columns = (array)$this->getTableColumns($table);

			foreach ($table_columns as $c => $col) {
				if (isset($this->columns[$c])) {
					$this->columns[$c] += $col;
				} else {
					$this->columns[$c]                = $col;
					$this->columns[$c]['is_selected'] = false;
					$this->columns[$c]['is_required'] = false;
				}

				if (!isset($this->columns[$c]['table'])) {
					$this->columns[$c]['table'] = $table;
				}

				if (!isset($this->columns[$c]['table_alias'])) {
					$this->columns[$c]['table_alias'] = $alias;
				}
			}
		}
	}

	public function setSort($sort)
	{
		$this->sort = $sort;

		$this->order_by = null;
	}

	public function setFilter($filter)
	{
		$this->filter = $filter;

		$this->where = $this->having = null;
	}

	public function setLimit($limit, $page = 1, $start = 0)
	{
		$limit = (int)$limit;

		if ($limit > 0) {
			if ($page) {
				$start = (max(1, (int)$page) - 1) * (int)$limit;
			}

			$this->limit = "$start,$limit";
		} else {
			$this->limit = '';
		}
	}

	public function setIndex($index)
	{
		$this->index = $index;
	}

	public function requireTable($require_table)
	{
		$require_tables = (array)$require_table;

		foreach ($require_tables as $table) {
			if (isset($this->related_tables[$table])) {
				$this->related_tables[$table]['is_required'] = true;
			} else {
				foreach ($this->related_tables as $t => &$data) {
					if ($data['table'] === $table) {
						$data['is_required'] = true;
						break;
					}
				}
				unset($data);
			}
		}
	}

	public function applyFilter($filter = null)
	{
		if ($filter) {
			$this->setFilter($filter);
		}

		if (!$this->columns) {
			trigger_error(_l("Please set columns first before apply filter."));

			return false;
		}

		$where  = '';
		$having = '';

		//Build WHERE statement from $filter
		foreach ($this->filter as $key => $value) {
			$not = false;

			if (strpos($key, '#') === 0) {
				$where .= ($where ? ' AND ' : '') . $value;
				continue;
			} elseif (strpos($key, '!') === 0) {
				$key = substr($key, 1);
				$not = true;
			}

			if (!isset($this->columns[$key])) {
				continue;
			}

			$column = $this->columns[$key];

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

		$this->where  = $where;
		$this->having = $having;
	}

	public function applySort($sort = null)
	{
		if ($sort) {
			$this->setSort($sort);
		}

		//Order
		$this->order_by = '';

		foreach ($this->sort as $col => $ord) {
			if (strpos($col, '#') === 0) {
				$this->order_by .= ($this->order_by ? ',' : '') . $ord;
			} else {
				if (strpos($col, '.') === false) {
					$column = !empty($this->columns[$col]) ? $this->columns[$col] : false;

					if ($column) {
						if (!empty($column['sort_key'])) {
							$col = $column['sort_key'];
						} else {
							$col = (!empty($column['table_alias']) ? "`{$column['table_alias']}`." : '') . "`$col`";
						}
					}
				}

				$ord = strtoupper($ord) === 'DESC' ? 'DESC' : 'ASC';

				$this->order_by .= ($this->order_by ? ',' : '') . $this->escape($col) . ' ' . $ord;
			}
		}
	}

	/**
	 * This builds a select statement with all selected fields that do not belong to a table.
	 * This is most commonly used to allow filtering by calculated fields (eg: in the HAVING clause)
	 *
	 * @param $filter - First check if the column is being filtered by before including (eg: for HAVING clause)
	 * @return string
	 */

	public function selectCalculatedFields($filter = array())
	{
		$select = '';

		foreach ($this->columns as $c => $col) {
			if (!empty($col['is_selected']) && empty($col['table_alias'])) {
				if (!$filter || isset($filter[$c])) {
					if (!empty($col['field'])) {
						$str = $col['field'] . ($col['alias'] ? " as `$col[alias]`" : '');
					} else {
						//Column is not in any tables and field is not specified, so this column should not be included
						continue;
					}

					$select .= ($select ? ',' : '') . $str;
				}
			}
		}

		return $select;
	}

	public function select()
	{
		if ($this->select === null) {
			$this->select = '';

			foreach ($this->columns as $c => $col) {
				if (!empty($col['is_selected'])) {
					if (!empty($col['field'])) {
						$str = $col['field'] . ($col['alias'] ? " as `$col[alias]`" : '');
					} elseif (!empty($col['table_alias'])) {
						$str = "`{$col['table_alias']}`.`$c`";
					} else {
						//Column is not in any tables and field is not specified, so this column should not be included
						continue;
					}

					$this->select .= ($this->select ? ',' : '') . $str;
				}
			}

			//If no select was resolved, default to select all columns
			if (!$this->select) {
				$this->select = '*';
			}
		}

		return $this->select;
	}

	public function from()
	{
		if ($this->from === null) {
			$this->from = "`$this->table`" . ($this->alias ? " `{$this->alias}`" : '');

			foreach ($this->sort + $this->filter as $col => $c) {
				if (isset($this->columns[$col])) {
					$this->columns[$col]['is_required'] = true;
				}
			}

			foreach ($this->columns as $column) {
				if ($column['is_required']) {
					if (!empty($column['require_table'])) {
						$this->requireTable($column['require_table']);
					} elseif ($column['table'] !== $this->table) {
						$this->requireTable($column['table']);
					}
				}
			}

			//Extract JOIN Clauses
			foreach ($this->related_tables as $join) {
				if ($join['is_required']) {
					if ($join['type']) {
						$this->from .= ($join['type'] ? " {$join['type']}" : '') . " `{$join['table']}`" . ($join['alias'] ? " `{$join['alias']}`" : '') . (strpos($join['on'], '=') ? ' ON' : ' USING') . " (" . $join['on'] . ")";
					} else {
						$this->from .= ' ' . $join['table'];
					}
				}
			}
		}

		return $this->from;
	}

	public function where()
	{
		if ($this->where === null) {
			$this->applyFilter();
		}

		return $this->where;
	}

	public function groupBy()
	{
		return $this->group_by;
	}

	public function having()
	{
		if ($this->having === null) {
			$this->applyFilter();
		}

		return $this->having;
	}

	public function orderBy()
	{
		if ($this->order_by === null) {
			$this->applySort();
		}

		return $this->order_by;
	}

	public function limit()
	{
		return $this->limit;
	}

	public function buildClauses()
	{
		$this->select();
		$this->from();
		$this->where();
		$this->groupBy();
		$this->having();
		$this->orderBy();
		$this->limit();
	}

	public function buildQuery()
	{
		return $this->query = "SELECT $this->select FROM $this->from" .
			($this->where ? " WHERE $this->where" : '') .
			($this->group_by ? " GROUP BY $this->group_by" : '') .
			($this->having ? " HAVING $this->having" : '') .
			($this->order_by ? " ORDER BY $this->order_by" : '') .
			($this->limit ? " LIMIT $this->limit" : '');
	}

	public function build()
	{
		$this->buildClauses();

		return $this->buildQuery();
	}

	public function execute($calc_total = false, $sql_calc_found_rows = false)
	{
		if ($calc_total) {
			$this->buildClauses();

			if ($sql_calc_found_rows) {
				$this->select = "SQL_CALC_FOUND_ROWS " . $this->select;
				$rows         = $this->queryRows($this->buildQuery(), $this->index);
				$total        = $this->queryVar("SELECT FOUND_ROWS()");
			} else {
				$rows           = $this->queryRows($this->buildQuery(), $this->index);
				$calculated     = $this->selectCalculatedFields($this->filter);
				$this->select   = "COUNT(*)" . ($calculated ? ',' . $calculated : '');
				$this->order_by = '';
				$this->limit    = '';
				$total          = $this->queryVar($this->buildQuery());
			}

			return array(
				$rows,
				$total,
			);
		} else {
			return $this->queryRows($this->build(), $this->index);
		}
	}
}
