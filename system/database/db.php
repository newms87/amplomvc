<?php
class DB
{
	private $driver;

	protected $error = array();

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
	public function queryRows($sql, $key_column = null)
	{
		$resource = $this->driver->query($sql);

		if (!$resource) {
			$this->queryError($sql);

			return false;
		}

		if (!is_object($resource)) {
			return array();
		}

		if ($key_column) {
			$rows = array();

			foreach ($resource->rows as $row) {
				$rows[$row[$key_column]] = $row;
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

	public function multiquery($string)
	{
		$file_length = strlen($string);
		$quote_char_list = array("'", "`", '"');
		$in_quote = false;
		$sql = '';
		$pos = 0;

		while($pos < $file_length) {
			$char = $string[$pos];
			if ($char === '\\') {
				$pos++;
				$sql .= $char . $string[$pos];
			}
			elseif (in_array($char, $quote_char_list)) {
				if ($in_quote) {
					if ($in_quote === $char) {
						$in_quote = false;
					}
				} else {
					$in_quote = $char;
				}

				$sql .= $char;
			}
			elseif ($in_quote) {
				$sql .= $char;
			}
			elseif ($char !== ';') {

				$sql .= $string[$pos];
			}
			else {
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
		_is_writable(dirname($file));

		$eol = "\r\n";

		if (!$tables) {
			$tables = $this->queryRows("SHOW TABLES");
		}

		if (is_null($prefix)) {
			$prefix = DB_PREFIX;
		}

		$sql = '';

		foreach ($tables as $table) {
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
		$rows = $this->queryRows("SHOW TABLES");

		$tables = array();

		foreach ($rows as $row) {
			$tables[current($row)] = current($row);
		}

		return $tables;
	}

	public function createTable($table, $sql)
	{
		return $this->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "$table` ($sql)");
	}

	public function dropTable($table)
	{
		return $this->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "$table`");
	}

	public function countTables()
	{
		$result = $this->query("SHOW TABLES");

		return $result ? $result->num_rows : null;
	}

	public function getKeyColumn($table)
	{
		$result = $this->queryRow("SHOW KEYS FROM " . DB_PREFIX . "$table WHERE Key_name = 'PRIMARY'");

		return $result ? $result['Column_name'] : false;
	}

	public function hasColumn($table, $column)
	{
		$rows = $this->queryRows("SHOW COLUMNS FROM " . DB_PREFIX . "$table");

		foreach ($rows as $row) {
			if (strtolower($row['Field']) === strtolower($column)) {
				return true;
			}
		}

		return false;
	}

	public function addColumn($table, $column, $options = '')
	{
		if (!$this->hasColumn($table, $column)) {
			return $this->query("ALTER TABLE `" . DB_PREFIX . "$table` ADD COLUMN `$column` $options");
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

			return $this->query("ALTER TABLE `" . DB_PREFIX . "$table` CHANGE COLUMN `$column` `$new_column` $options");
		}
	}

	public function dropColumn($table, $column)
	{
		if ($this->hasColumn($table, $column)) {
			return $this->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
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

	public function escape($value)
	{
		if (is_resource($value) || is_object($value)) {
			trigger_error("DB:escape(): Argument for value was not a a valid type! Value: " . gettype($value) . ". " . get_caller(0, 3));
			exit;
		}
		elseif (is_array($value)) {
			$driver = $this->driver;
			array_walk_recursive($value, function(&$v)use($driver) { $v = $driver->escape($v); });
			return $value;
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
