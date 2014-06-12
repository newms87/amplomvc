<?php

class DB
{
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
		if ($driver === 'mysqli') {
			$driver = 'mysqlidb';
		}

		$key = $driver . $hostname . $username . $database;

		if (!isset(self::$drivers[$key])) {
			//the database interface
			if (function_exists("_ac_mod_file")) {
				require_once(_ac_mod_file(DIR_DATABASE . 'database.php'));

				if (file_exists(DIR_DATABASE . $driver . '.php')) {
					require_once(_ac_mod_file(DIR_DATABASE . $driver . '.php'));
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

			$db->query("SET time_zone = '" . MYSQL_TIMEZONE . "'");

			self::$drivers[$key] = $db;
		}

		$this->driver = self::$drivers[$key];
		$this->prefix = is_null($prefix) ? DB_PREFIX : $prefix;
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
	public function query($sql)
	{
		if ($this->synctime) {
			$sql = $this->synctime($sql);
		}

		if (defined("SHOW_DB_PROFILE") && SHOW_DB_PROFILE) {
			$start = microtime(true);

			if (SHOW_DB_PROFILE && DB_PROFILE_NO_CACHE) {
				$sql = preg_replace("/^SELECT /i", "SELECT SQL_NO_CACHE ", $sql);
			}

			$resource = $this->driver->query($sql);

			$time = round(microtime(true) - $start, 6);

			self::$profile[] = array(
				'query' => $sql,
				'time'  => $time ? $time : .000005,
			);
		} else {
			$resource = $this->driver->query($sql);
		}

		if (!$resource) {
			trigger_error($this->driver->getError());

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
	 * @return mixed - will return an indexed array or false on failure
	 *
	 */
	public function queryColumn($sql)
	{
		$resource = $this->query($sql);

		if (!is_object($resource)) {
			return array();
		}

		return array_column($resource->rows, key($resource->row));
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

	public function dump($file, $tables = array(), $prefix = null)
	{
		if (!_is_writable(dirname($file))) {
			$this->error = _l("The directory was not writable for %s", $file);
			return false;
		}

		$eol = "\r\n";

		if (!$tables) {
			$tables = $this->queryRows("SHOW TABLES");
		}

		if (is_null($prefix)) {
			$prefix = $this->prefix;
		}

		$sql = '';

		foreach ($tables as $table) {
			if (is_array($table)) {
				$table = current($table);
			}

			$table = preg_replace("/^" . $this->prefix . "/", '', $table);

			$tablename = $prefix . $table;

			$sql .= "DROP TABLE IF EXISTS `$tablename`;" . $eol;
			$sql .= "CREATE TABLE `$tablename` (" . $eol;

			$columns = $this->queryRows("SHOW COLUMNS FROM `" . $this->prefix . "$table`");

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

			$rows = $this->queryRows("SELECT * FROM `" . $this->prefix . "$table`");

			if (!empty($rows)) {
				$sql .= "INSERT INTO `$tablename` VALUES ";
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
			$this->error = _l("%s(): failed to dump database to file %s", __METHOD__, $file);
			trigger_error($this->error);
		}

		return $this->hasError();
	}

	public function hasTable($table)
	{
		return $this->queryVar("SHOW TABLES LIKE '" . $this->prefix . $this->escape($table) . "'") ? true : false;
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
		return $this->query("CREATE TABLE IF NOT EXISTS `" . $this->prefix . "$table` ($sql)");
	}

	public function dropTable($table)
	{
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
		static $columns;

		if (!isset($columns[$table])) {
			$columns[$table] = $this->queryRows("SHOW COLUMNS FROM `" . $this->prefix . "$table`", 'Field');
		}

		return $columns[$table];
	}

	public function addColumn($table, $column, $options = '')
	{
		if (!$this->hasColumn($table, $column)) {
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

			return $this->query("ALTER TABLE `" . $this->prefix . "$table` CHANGE COLUMN `$column` `$new_column` $options");
		}
	}

	public function dropColumn($table, $column)
	{
		if ($this->hasColumn($table, $column)) {
			return $this->query("ALTER TABLE `" . $this->prefix . "$table` DROP COLUMN `$column`");
		}

		return false;
	}

	public function setAutoIncrement($table, $value)
	{
		if (!$this->driver->setAutoIncrement($table, $value)) {
			trigger_error($this->driver->getError());

			return false;
		}

		return true;
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
