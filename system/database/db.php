<?php

class DB
{
	public $tables;

	static $profile = array();
	static $drivers = array();

	private $driver;
	private $prefix;

	//Time Simulation
	private $synctime = false;

	//In case a plugin wants to wrap this Class
	protected $error = array();

	public function __construct($driver = null, $hostname = null, $username = null, $password = null, $database = null, $prefix = null)
	{
		global $ac_time_offset;

		if ($ac_time_offset) {
			$this->synctime = true;
		}

		//We cannot redeclare the mysqli class so mysqli is an alias for our wrapper class msyqlidb
		if (!$driver || $driver === 'mysqli') {
			$driver = 'mysqlidb';
		}

		$key = $driver . $hostname . $username . $database;

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

			$db = new $driver($hostname, $username, $password, $database);

			//Set our errors to the driver errors if there were any
			$this->error = $db->getError();

			$db->query("SET time_zone = '" . MYSQL_TIMEZONE . "'");

			self::$drivers[$key] = $db;
		}

		$this->driver = self::$drivers[$key];
		$this->prefix = $prefix === null ? DB_PREFIX : $prefix;
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

	public function getQueryError()
	{
		return $this->driver->getError();
	}

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	public function getName()
	{
		return $this->driver->getName();
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
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		if (DB_PROFILE) {
			$start = microtime(true);

			if (DB_PROFILE_NO_CACHE) {
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

		if (!$resource) {
			trigger_error($this->driver->getError());

			$this->error['query'] = $this->driver->getError();

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
				$rows[$row[$index]] = $row;
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
		$file_length = strlen($string);
		$quote_char_list = array(
			"'",
			"`",
			'"'
		);
		$in_quote = false;
		$sql = '';
		$pos = 0;

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

				if ($this->getError()) {
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

		if ($this->getError()) {
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
			$prefix = $this->prefix;
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
						$default = "";
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
		if (!$this->tables) {
			$this->tables = array_change_key_case($this->queryColumn("SHOW TABLES", true));
		}

		$table_name = strtolower($table);

		if (isset($this->tables[$table_name])) {
			if (defined("LOWERCASE_DB_TABLES")) {
				return $table_name;
			}
			return $table;
		} elseif (isset($this->tables[strtolower($this->prefix) . $table_name])) {
			if (defined("LOWERCASE_DB_TABLES")) {
				return $this->prefix . $table_name;
			}
			return $this->prefix . $table;
		}

		return false;
	}

	public function getTables()
	{
		$rows = $this->queryRows("SHOW TABLES");

		$tables = array();

		foreach ($rows as $row) {
			$tables[current($row)] = current($row);
		}

		return $tables;
	}

	public function createTable($table, $sql)
	{
		clear_cache('model');
		$this->tables = null;
		return $this->query("CREATE TABLE IF NOT EXISTS `" . $this->prefix . "$table` ($sql)");
	}

	public function dropTable($table)
	{
		clear_cache('model');
		$this->tables = null;
		return $this->query("DROP TABLE IF EXISTS `" . $this->prefix . "$table`");
	}

	public function countTables()
	{
		$result = $this->query("SHOW TABLES");

		return $result ? $result->num_rows : null;
	}

	public function getKeyColumn($table)
	{
		$result = $this->queryRow("SHOW KEYS FROM " . $this->prefix . "$table WHERE Key_name = 'PRIMARY'");

		return $result ? $result['Column_name'] : false;
	}

	public function hasColumn($table, $column)
	{
		if ($this->hasTable($table)) {
			$columns = $this->getTableColumns($table);

			foreach ($columns as $row) {
				if (strtolower($row['Field']) === strtolower($column)) {
					return true;
				}
			}
		}

		return false;
	}

	public function getTableColumns($table)
	{
		static $columns;

		$table = $this->hasTable($table);

		if (!$table) {
			return array();
		}

		if (!isset($columns[$table])) {
			$columns[$table] = $this->queryRows("SHOW COLUMNS FROM `$table`", 'Field');
		}

		return $columns[$table];
	}

	public function addColumn($table, $column, $options = '')
	{
		if ($this->hasTable($table) && !$this->hasColumn($table, $column)) {
			clear_cache('model');
			return $this->query("ALTER TABLE `" . $this->prefix . "$table` ADD COLUMN `$column` $options");
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

			clear_cache('model');
			return $this->query("ALTER TABLE `" . $this->prefix . "$table` CHANGE COLUMN `$column` `$new_column` $options");
		}
	}

	public function dropColumn($table, $column)
	{
		if ($this->hasColumn($table, $column)) {
			clear_cache('model');
			return $this->query("ALTER TABLE `" . $this->prefix . "$table` DROP COLUMN `$column`");
		}

		return false;
	}

	public function getIndex($table, $key)
	{
		$table = $this->hasTable($table);

		if ($table) {
			return $this->queryRows("SHOW INDEX FROM `" . $table . "` WHERE Key_name = '" . $this->escape($key) . "'");
		}
	}

	public function createIndex($table, $key, $fields, $type = 'BTREE')
	{
		$table = $this->hasTable($table);

		if ($table) {
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

			return $this->query("CREATE INDEX `" . $this->escape($key) . "` ON `$table`(" . implode(',', $key_fields) . ") USING " . $type);
		}
	}

	public function dropIndex($table, $key)
	{
		$table = $this->hasTable($table);

		if ($table) {
			return $this->query("DROP INDEX `" . $key . "` ON `$table`");
		}
	}

	public function setAutoIncrement($table, $value)
	{
		if (!$this->driver->setAutoIncrement($table, $value)) {
			trigger_error($this->driver->getError());

			return false;
		}

		return true;
	}

	public function alterPrefix($prefix, $old_prefix = '')
	{
		clear_cache('model');

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
