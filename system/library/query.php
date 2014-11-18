<?php

class Query
{
	private $clauses, $clauses_pos, $query;

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
		if (!preg_match("/^SELECT /i", $sql)) {
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
