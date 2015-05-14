<?php

class DB
{
	public $tables, $columns;
	public $t;

	static $profile = array();
	static $drivers = array();

	protected
		$driver,
		$synctime = false,
		$error = array(),
		$query_history;

	public function __construct($driver = null, $hostname = null, $username = null, $password = null, $schema = null, $prefix = null)
	{
		global $ac_time_offset;

		if ($ac_time_offset) {
			$this->synctime = true;
		}

		//We cannot redeclare the mysqli class so mysqli is an alias for our wrapper class msyqlidb
		if (!$driver || $driver === 'mysqli') {
			$driver = 'mysqlidb';
		}

		//Will (greatly) optimize performance on local Windows installs if there is no 127.0.0.1 localhost entry in the hosts file.
		if ($hostname === 'localhost') {
			$hostname = '127.0.0.1';
		}

		$key = $driver . $hostname . $username . $schema;

		if (!isset(self::$drivers[$key])) {
			//the database interface
			if (function_exists("_mod")) {
				require_once(_mod(DIR_DATABASE . 'database_interface.php'));

				if (file_exists(DIR_DATABASE . $driver . '.php')) {
					require_once(_mod(DIR_DATABASE . $driver . '.php'));
				} else {
					die('Error: Could not load database file ' . $driver . '!');
				}
			} else {
				require_once(DIR_DATABASE . 'database.php');

				if (file_exists(DIR_DATABASE . $driver . '.php')) {
					require_once(DIR_DATABASE . $driver . '.php');
				} else {
					$this->error = 'Error: Could not load database file ' . $driver . '!';
				}
			}

			$db = new $driver($hostname, $username, $password, $schema);

			//Set our errors to the driver errors if there were any
			$this->error = $db->getError();

			$db->query("SET time_zone = '" . MYSQL_TIMEZONE . "'");

			self::$drivers[$key] = $db;
		}

		$this->driver = self::$drivers[$key];

		$this->t = new Model_T;

		$this->t->schema = $schema;
		$this->t->prefix = $prefix === null ? DB_PREFIX : $prefix;

		$this->updateTables();
	}

	public function updateTables()
	{
		$cache           = 'model.' . $this->t->schema . '.' . $this->t->prefix . '.' . 'tables';
		$this->t->tables = cache($cache);

		if (!$this->t->tables) {
			$this->t->tables = $this->getTables($this->t->prefix);

			if ($this->t->prefix !== DB_PREFIX) {
				$this->t->tables += $this->getTables(DB_PREFIX);
			}

			cache($cache, $this->t->tables);

			Model::$model = array();
			$this->columns = array();
		}
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
		if ($type) {
			$error = isset($this->error[$type]) ? $this->error[$type] : null;
			unset($this->error[$type]);
		} else {
			$error       = $this->error;
			$this->error = array();
		}

		return $error;
	}

	public function clearErrors()
	{
		$this->error = array();
	}

	public function getQueryError()
	{
		return $this->driver->getError();
	}

	public function queryHistory($offset = -1)
	{
		$index = $offset >= 0 ? $offset : count($this->query_history) + $offset;
		return isset($this->query_history[$index]) ? $this->query_history[$index] : null;
	}

	public function setPrefix($prefix)
	{
		$this->t->prefix = $prefix;

		$this->updateTables();
	}

	public function getPrefix()
	{
		return $this->t->prefix;
	}

	public function getSchema()
	{
		return $this->t->schema;
	}

	public function getProfile()
	{
		return self::$profile;
	}

	/**
	 * Returns an array with 3 elements, 'row', 'rows', and 'num_rows'.
	 * 'row' is the first row of the query
	 * 'rows' are all of the resulting rows of the MySQL query.
	 * 'num_rows' is the count of rows in the return result
	 *
	 * @param $sql - the MySQL query string
	 * @return mixed - An array as described above, or false on failure
	 *
	 */
	public function query($sql, $cast_type = true)
	{
		$this->error = array();

		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		if (AMPLO_PROFILE) {
			$start = microtime(true);

			if (AMPLO_PROFILE_NO_CACHE) {
				$sql = preg_replace("/^SELECT /i", "SELECT SQL_NO_CACHE ", $sql);
			}

			$resource = $this->driver->query($sql, $cast_type);

			$time = round(microtime(true) - $start, 6);

			self::$profile[] = array(
				'query' => $sql,
				'time'  => $time ? $time : .000005,
			);
		} else {
			$resource = $this->driver->query($sql, $cast_type);
		}

		$this->query_history[] = $sql;

		if (!$resource) {
			$error = $this->driver->getError();

			trigger_error($error);

			$this->error['query'] = $error;

			return false;
		}

		return $resource;
	}

	/**
	 * Returns an array of associative arrays with the Select field as the keys
	 * and the column data as the values for the MySQL query
	 *
	 * @param $sql - the MySQL query string
	 * @param $index - The table field to use as the associative array key index
	 * @return mixed - an array of associative arrays of field => value pairs, or false on failure
	 *
	 */
	public function queryRows($sql, $index = null)
	{
		$resource = $this->query($sql);

		if (!is_object($resource)) {
			return array();
		}

		if ($index) {
			$rows = array();

			foreach ($resource->rows as $row) {
				if (isset($row[$index])) {
					$rows[$row[$index]] = $row;
				} else {
					$rows[] = $row;
				}
			}

			return $rows;
		}

		return $resource->rows;
	}

	/**
	 * Returns an associative array with the Select field as the keys
	 * and the column data as the values for the MySQL query
	 *
	 * @param $sql - the MySQL query string
	 * @return mixed - An associative array of field => value pairs, or false on failure
	 *
	 */
	public function queryRow($sql)
	{
		$resource = $this->query($sql);

		if (!is_object($resource)) {
			return array();
		}

		return $resource->row;
	}

	/**
	 * Returns an array with each value as the first Select field of each row
	 *
	 * @param $sql - the MySQL query string
	 * @param $index_key - The column to use as the index/keys for the returned array. Can be an integer, string, or true.
	 *                     If it is true, the index key will be the first column in the returned result set.
	 *
	 * @return mixed - will return an indexed array or false on failure
	 *
	 */
	public function queryColumn($sql, $index_key = null)
	{
		$resource = $this->query($sql);

		if (!is_object($resource)) {
			return array();
		}

		if ($index_key === true) {
			$index_key = key($resource->row);
		}

		return array_column($resource->rows, key($resource->row), $index_key);
	}

	/**
	 * Returns the first field in the first row of the query
	 *
	 * @param $sql - the MySQL query string
	 * @return mixed - The DB table field value as an integer, float or string, or null on failure
	 *
	 */
	public function queryVar($sql)
	{
		$resource = $this->query($sql);

		if (!is_object($resource)) {
			return null;
		}

		return $resource->row ? current($resource->row) : null;
	}

	public function multiquery($string)
	{
		$file_length     = strlen($string);
		$quote_char_list = array(
			"'",
			"`",
			'"'
		);
		$in_quote        = false;
		$sql             = '';
		$pos             = 0;

		while ($pos < $file_length) {
			$char = $string[$pos];
			if ($char === '\\') {
				$pos++;
				$sql .= $char . $string[$pos];
			} elseif (in_array($char, $quote_char_list)) {
				if ($in_quote) {
					if ($in_quote === $char) {
						$in_quote = false;
					}
				} else {
					$in_quote = $char;
				}

				$sql .= $char;
			} elseif ($in_quote) {
				$sql .= $char;
			} elseif ($char !== ';') {

				$sql .= $string[$pos];
			} else {
				$this->query($sql);

				if ($this->error) {
					return false;
				}
				$sql = '';
			}
			$pos++;
		}

		return true;
	}

	public function executeFile($file)
	{
		$content = file_get_contents($file);

		$lines = explode(";\r\n", $content);

		foreach ($lines as $line) {
			if (!empty($line) && is_string($line) && trim($line)) {
				$this->query($line);
			}
		}

		if ($this->error) {
			return false;
		}

		return true;
	}

	public function dump($file, $tables = array(), $prefix = null, $remove_prefix = false)
	{
		if (!_is_writable(dirname($file))) {
			$this->error['directory'] = _l("The directory was not writable for %s", $file);
			return false;
		}

		if ($prefix === null) {
			$prefix = $this->t->prefix;
		}

		$eol = "\r\n";

		if (!$tables) {
			$tables = $this->queryRows("SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = '" . $this->getName() . "' ORDER BY table_type ASC, table_name ASC");
		} else {
			foreach ($tables as &$table) {
				if (is_string($table)) {
					$table = array(
						'table_name' => $table,
						'table_type' => 'BASE TABLE',
					);
				}
			}
			unset($table);
		}

		$sql = $remove_prefix ? '' : '#AMPLO_PREFIX=' . $prefix . $eol . $eol;

		foreach ($tables as $table) {
			$name = $table['table_name'];

			if (strtoupper($table['table_type']) === 'VIEW') {
				$definition = $this->queryVar("SELECT view_definition FROM information_schema.views WHERE table_name = '$name' AND table_schema = '" . $this->getName() . "'");
				$sql .= "CREATE VIEW `$name` AS $definition" . $eol;

				continue;
			}

			$columns = $this->queryRows("SHOW COLUMNS FROM `$name`");
			$rows    = $this->queryRows("SELECT * FROM `$name`");

			if ($remove_prefix) {
				$name = preg_replace("/^$prefix/", '', $name);
			}

			$sql .= "DROP TABLE IF EXISTS `$name`;" . $eol;
			$sql .= "CREATE TABLE `$name` (" . $eol;

			$primary_key = array();

			foreach ($columns as $column) {
				if (strtoupper($column['Key']) === 'PRI') {
					$primary_key[] = $column['Field'];
				}

				$field = $column['Field'];
				$type  = $column['Type'];
				$null  = strtoupper($column['Null']) === 'YES' ? '' : 'NOT NULL';

				if (is_null($column['Default'])) {
					if (!$null) { //meaning NULL is allowed
						$default = "DEFAULT NULL";
					} else {
						$default = '';
					}
				} else {
					$default = "DEFAULT '" . $this->escape(trim($column['Default'], "'\"")) . "'";
				}

				$extra = !empty($column['Extra']) ? strtoupper($column['Extra']) : '';

				$sql .= "\t`$field` $type $null $default $extra," . $eol;
			}

			if (!empty($primary_key)) {
				$sql .= "\tPRIMARY KEY (`" . implode('`,`', $primary_key) . "`)" . $eol;
			} else {
				$sql = preg_replace("/,$eol\$/", '', $sql);
			}

			$sql .= ");" . $eol . $eol;

			if (!empty($rows)) {
				$sql .= "INSERT INTO `$name` VALUES ";
				foreach ($rows as $row) {
					$sql .= "(";
					foreach ($row as $key => $value) {
						$sql .= "'" . $this->escape($value) . "',";
					}

					$sql = rtrim($sql, ',') . "),";
				}

				$sql = rtrim($sql, ',') . ";" . $eol . $eol;
			}
		}

		if (!file_put_contents($file, $sql)) {
			$this->error = _l("%s(): failed to write sql dump to file %s", __METHOD__, $file);
			trigger_error($this->error);
		}

		return empty($this->error);
	}

	public function hasTable($table)
	{
		return isset($this->t[$table]);
	}

	public function getTables($prefix = false)
	{
		$rows = $this->queryRows("SHOW TABLES");

		$tables = array();

		foreach ($rows as $row) {
			$name = current($row);

			if (!$prefix || strpos($name, $prefix) === 0) {
				$base          = $prefix ? preg_replace("/^" . $prefix . "/", '', $name) : $name;
				$tables[$base] = $name;
			}
		}

		return $tables;
	}

	public function createTable($table, $sql)
	{
		$result = $this->query("CREATE TABLE IF NOT EXISTS `" . $this->t->prefix . "$table` ($sql)");

		clear_cache('model');
		$this->updateTables();

		return $result;
	}

	public function copyTable($table, $copy, $with_data = false)
	{
		if ($table === $copy) {
			return true;
		}

		if (isset($this->t[$copy])) {
			$this->error['copy'] = _l("A table with the same name as copy, %s, already exists!", $copy);
			return false;
		}

		$t = $this->t[$table];

		$row = $this->queryRow("SHOW CREATE TABLE `$t`");

		if (!empty($row['Create Table'])) {
			$sql = preg_replace("/^CREATE\\s*TABLE\\s*`$t`/i", "CREATE TABLE IF NOT EXISTS `$copy`", $row['Create Table']);

			if (!$with_data) {
				$sql = preg_replace("/AUTO_INCREMENT=\\d+\\s*/", '', $sql);

				$result = $this->query($sql);

				clear_cache('model');
				$this->updateTables();

				return $result;
			}
		}

		return false;
	}

	public function dropTable($table)
	{
		if (!isset($this->t[$table])) {
			$this->error['table'] = _l("The table %s does not exist", $table);
			return false;
		}

		$result = $this->query("DROP TABLE IF EXISTS `{$this->t[$table]}`");

		clear_cache('model');
		$this->updateTables();

		return $result;
	}

	public function countTables()
	{
		return count($this->t->tables);
	}

	public function getKeyColumn($table)
	{
		$result = $this->queryRow("SHOW KEYS FROM {$this->t[$table]} WHERE Key_name = 'PRIMARY'");

		return $result ? $result['Column_name'] : false;
	}

	public function hasColumn($table, $column)
	{
		$columns = $this->getTableColumns($table);

		foreach ($columns as $row) {
			if (strtolower($row['Field']) === strtolower($column)) {
				return true;
			}
		}

		return false;
	}

	public function getTableColumns($table)
	{
		if (!isset($this->t[$table])) {
			return array();
		}

		$t = $this->t[$table];

		if (!isset($this->columns[$t])) {
			$this->columns[$t] = $this->queryRows("SHOW COLUMNS FROM `$t`", 'Field');
		}

		return $this->columns[$t];
	}

	public function addColumn($table, $column, $options = '')
	{
		if (isset($this->t[$table]) && !$this->hasColumn($table, $column)) {
			$result = $this->query("ALTER TABLE `{$this->t[$table]}` ADD COLUMN `$column` $options");

			clear_cache('model');
			$this->updateTables();

			return $result;
		}

		return false;
	}

	public function changeColumn($table, $column, $new_column = null, $options = '')
	{
		if ($this->hasColumn($table, $column)) {
			if (!$new_column) {
				$new_column = $column;
			} elseif ($column !== $new_column && $this->hasColumn($table, $new_column)) {
				return false;
			}

			$result = $this->query("ALTER TABLE `{$this->t[$table]}` CHANGE COLUMN `$column` `$new_column` $options");

			clear_cache('model');
			$this->updateTables();

			return $result;
		}

		return false;
	}

	public function dropColumn($table, $column)
	{
		if ($this->hasColumn($table, $column)) {
			$result = $this->query("ALTER TABLE `{$this->t[$table]}` DROP COLUMN `$column`");

			clear_cache('model');
			$this->updateTables();

			return $result;
		}

		return false;
	}

	public function getIndex($table, $key)
	{
		if (isset($this->t[$table])) {
			return $this->queryRows("SHOW INDEX FROM `{$this->t[$table]}` WHERE Key_name = '" . $this->escape($key) . "'");
		}
	}

	public function createIndex($table, $key, $fields, $type = 'BTREE')
	{
		if (isset($this->t[$table])) {
			if ($this->getIndex($table, $key)) {
				return true;
			}

			$key_fields = array();

			if (!is_array($fields)) {
				$key_fields[] = "`$fields`";
			} else {
				$orders = array(
					'ASC',
					'DESC'
				);

				foreach ($fields as $name => $ord) {
					if (!in_array(strtoupper($ord), $orders)
					) {
						$key_fields[] = "`$ord`";
					} else {
						$key_fields[] = "`$name` $ord";
					}
				}
			}

			return $this->query("CREATE INDEX `" . $this->escape($key) . "` ON `{$this->t[$table]}`(" . implode(',', $key_fields) . ") USING " . $type);
		}

		return false;
	}

	public function dropIndex($table, $key)
	{
		if ($this->getIndex($table, $key)) {
			$result = $this->query("DROP INDEX `" . $key . "` ON `{$this->t[$table]}`");

			clear_cache('model');
			$this->updateTables();

			return $result;
		}

		return false;
	}

	public function setAutoIncrement($table, $value)
	{
		if (!$this->driver->setAutoIncrement($this->t[$table], $value)) {
			trigger_error($this->driver->getError());

			return false;
		}

		return true;
	}

	public function alterPrefix($prefix, $old_prefix = '')
	{
		$tables = $this->getTables();

		foreach ($tables as $table) {
			if ($old_prefix) {
				$new_table = preg_replace("/^$old_prefix/", $prefix, $table);
			} else {
				if (preg_match("/^$prefix/", $table)) {
					continue;
				}

				$new_table = $prefix . $table;
			}

			$this->query("DROP TABLE IF EXISTS `$new_table`");
			$this->query("RENAME TABLE `$table` TO `$new_table`");
		}

		clear_cache('model');
		$this->updateTables();

		return empty($this->error);
	}

	public function escape($value)
	{
		if (is_resource($value) || is_object($value)) {
			trigger_error(_l("%s(): Argument for value was not a a valid type! Value: %s.", gettype($value)));
			exit;
		} elseif (is_array($value)) {
			$driver = $this->driver;
			array_walk_recursive($value, function (&$v) use ($driver) {
				$v = $driver->escape($v);
			});
			return $value;
		}

		return $this->driver->escape($value);
	}

	public function escapeHtml($value)
	{
		return $this->driver->escapeHtml($value);
	}

	public function countAffected()
	{
		return $this->driver->countAffected();
	}

	public function getLastId()
	{
		return $this->driver->getLastId();
	}

	private function synctime($sql)
	{
		$now = new DateTime("@" . _time(), new DateTimeZone(DEFAULT_TIMEZONE));
		return str_replace("NOW()", "'" . $now->format("Y-m-d H:i:s") . "'", $sql);
	}
}

class Model_T implements ArrayAccess
{
	public
		$tables = array(),
		$prefix,
		$schema;

	public function offsetGet($offset)
	{
		if (isset($this->tables[$offset])) {
			return $this->tables[$offset];
		}

		$t = strtolower($offset);

		if (isset($this->tables[$t])) {
			return $this->tables[$t];
		}

		$pt = $this->prefix . $t;

		foreach ($this->tables as $key => $table) {
			$lkey = strtolower($key);

			if ($lkey === $t || $lkey === $pt) {
				return $table;
			}

			$ltable = strtolower($table);

			if ($ltable === $t || $ltable === $pt) {
				return $table;
			}
		}

		return $offset;
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->tables[] = $value;
		} else {
			$this->tables[$offset] = $value;
		}
	}

	public function offsetExists($offset)
	{
		if (isset($this->tables[$offset])) {
			return true;
		}

		$t = strtolower($offset);

		if (isset($this->tables[$t])) {
			return true;
		}

		$pt = $this->prefix . $t;

		foreach ($this->tables as $key => $table) {
			$lkey = strtolower($key);

			if ($lkey === $t || $lkey === $pt) {
				return true;
			}

			$ltable = strtolower($table);

			if ($ltable === $t || $ltable === $pt) {
				return true;
			}
		}

		return false;
	}

	public function offsetUnset($offset)
	{
		unset($this->tables[$offset]);
	}
}
