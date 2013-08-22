<?php
class DB
{
	private $driver;
	private $error;

	public function __construct($driver, $hostname, $username, $password, $database)
	{
		//We cannot redeclare the mysqli class so mysqli is an alias for our wrapper class msyqlidb
		if ($driver == 'mysqli') {
			$driver = 'mysqlidb';
		}

		//the database interface
		if (function_exists("_require_once")) {
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

		$this->driver = new $driver($hostname, $username, $password, $database);
	}

	public function getError()
	{
		$driver_error = $this->driver->getError();

		if ($this->error) {
			$driver_error = '<br>' . $this->error;
		}

		return $driver_error;
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
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return false;
		}

		return $resource;
	}

	/**
	 * Returns an array of associative arrays with the Select field as the keys
	 * and the column data as the values for the MySQL query
	 *
	 * @param $sql - the MySQL query string
	 * @return mixed - an array of associative arrays of field => value pairs, or false on failure
	 *
	 */
	public function queryRows($sql)
	{
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return false;
		}

		if (!is_object($resource)) {
			return array();
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
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return false;
		}

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
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return false;
		}

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
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return null;
		}

		if (!is_object($resource)) {
			return null;
		}

		return $resource->row ? current($resource->row) : null;
	}

	private function queryError($sql = '')
	{
		if (function_exists('debug_stack') && function_exists('html_dump')) {
			$stack                           = debug_stack();
			$_SESSION['debug']['call stack'] = $stack;
			html_dump($stack, 'call stack');
			echo '<br /><br />' . $sql;
		}

		if (function_exists('get_caller')) {
			trigger_error($this->driver->getError() . get_caller(1, 5));
		}
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

	public function dump($file, $tables = '', $prefix = null)
	{
		_is_writable(dirname($file));

		$eol = "\r\n";

		if (!$tables) {
			$tables = $this->queryRows("SHOW TABLES");
		}

		//Normalize Table Requests
		$table_list = array();
		foreach ($tables as $table => $with_data) {
			if (!is_string($table)) {
				$table = $with_data;
			}

			$table_list[$table] = (bool)$with_data;
		}

		$tables = $table_list;

		if (is_null($prefix)) {
			$prefix = DB_PREFIX;
		}

		$sql = '';

		foreach ($tables as $table => $with_data) {
			if (is_array($table)) {
				$table = current($table);
			}

			$table = preg_replace("/^" . DB_PREFIX . "/", '', $table);

			$tablename = $prefix . $table;

			$sql .= "DROP TABLE IF EXISTS `$tablename`;" . $eol;
			$sql .= "CREATE TABLE `$tablename` (" . $eol;

			$columns = $this->queryRows("SHOW COLUMNS FROM `" . DB_PREFIX . "$table`");

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

			//Table Data
			if ($with_data) {
				$rows = $this->queryRows("SELECT * FROM `" . DB_PREFIX . "$table`");

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
		}

		if (!file_put_contents($file, $sql)) {
			trigger_error("DB::dump(): Failed to dump database tables, $table_string, to file $file");

			return false;
		}

		if ($this->getError()) {
			return false;
		}

		return true;
	}

	public function hasTable($table)
	{
		return $this->queryVar("SHOW TABLES LIKE '" . DB_PREFIX . $this->escape($table) . "'") ? true : false;
	}

	public function getTables()
	{
		$result = $this->driver->query("SHOW TABLES");

		$tables = array();

		foreach ($result->rows as $row) {
			$tables[current($row)] = current($row);
		}

		return $tables;
	}

	public function createTable($table, $sql)
	{
		return $this->driver->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "$table` ($sql)");
	}

	public function dropTable($table)
	{
		return $this->driver->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "$table`");
	}

	public function countTables()
	{
		$result = $this->driver->query("SHOW TABLES");

		return $result->num_rows;
	}

	public function getKeyColumn($table)
	{
		$result = $this->driver->query("SHOW KEYS FROM " . DB_PREFIX . "$table WHERE Key_name = 'PRIMARY'");

		if ($result->num_rows) {
			return $result->row['Column_name'];
		}

		return false;
	}

	public function hasColumn($table, $column)
	{
		$query = $this->driver->query("SHOW COLUMNS FROM " . DB_PREFIX . "$table");
		foreach ($query->rows as $row) {
			if (strtolower($row['Field']) == strtolower($column)) {
				return true;
			}
		}
		return false;
	}

	public function addColumn($table, $column, $options = '')
	{
		if (!$this->hasColumn($table, $column)) {
			return $this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` ADD COLUMN `$column` $options");
		}

		return false;
	}

	public function changeColumn($table, $column, $new_column = null, $options = '')
	{
		if ($this->hasColumn($table, $column)) {
			if (!$new_column) {
				$new_column = $column;
			} elseif ($this->hasColumn($table, $new_column)) {
				return false;
			}

			return $this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` CHANGE COLUMN `$column` `$new_column` $options");
		}
	}

	public function dropColumn($table, $column)
	{
		if ($this->hasColumn($table, $column)) {
			return $this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
		}

		return false;
	}

	public function setAutoincrement($table, $value)
	{
		if (!$this->driver->setAutoincrement($table, $value)) {
			trigger_error($this->driver->getError());

			return false;
		}

		return true;
	}

	public function getInsertString($data)
	{
		$str = array();

		foreach ($data as $key => $value) {
			$str[] = "`$key`='$value'";
		}

		return implode(',', $str);
	}

	public function escape($value)
	{
		if (is_resource($value) || is_object($value) || is_array($value)) {
			trigger_error("DB:escape(): Argument for value was not a a valid type! Value: " . gettype($value) . ". " . get_caller(0, 3));
			exit;
		}

		return $this->driver->escape($value);
	}

	public function escapeHtml($value)
	{
		return $this->driver->escapeHtml($value);
	}

	public function escapeAll($values)
	{
		array_walk_recursive($values, function (&$value, $key, $db) { $value = $db->escape($value); }, $this);

		return $values;
	}

	public function countAffected()
	{
		return $this->driver->countAffected();
	}

	public function getLastId()
	{
		return $this->driver->getLastId();
	}
}
