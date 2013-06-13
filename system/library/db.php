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
			_require(DIR_DATABASE . 'database.php');
			
			if (file_exists(DIR_DATABASE . $driver . '.php')) {
				_require(DIR_DATABASE . $driver . '.php');
			} else {
				die('Error: Could not load database file ' . $driver . '!');
			}
		}
		else {
			require_once(DIR_DATABASE . 'database.php');
			
			if (file_exists(DIR_DATABASE . $driver . '.php')) {
				require_once(DIR_DATABASE . $driver . '.php');
			} else {
				$this->error = 'Error: Could not load database file ' . $driver . '!';
			}
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
	}
	
	public function get_error()
	{
		$driver_error = $this->driver->get_error();
		
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
			$this->query_error($sql);
			
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
	public function query_rows($sql)
	{
  		$resource = $this->driver->query($sql);
		
		if (!$resource) {
			$this->query_error($sql);
			
			return false;
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
	public function query_row($sql)
	{
  		$resource = $this->driver->query($sql);
		
		if (!$resource) {
			$this->query_error($sql);
			
			return false;
		}
		
		return $resource->row;
  	}
	
	/**
	* Returns the first field in the first row of the query
	*
	* @param $sql - the MySQL query string
	* @return mixed - The DB table field value as an integer, float or string, or null on failure
	*
	*/
  	public function query_var($sql)
  	{
  		$resource = $this->driver->query($sql);
		
		if (!$resource) {
			$this->query_error($sql);
			
			return null;
		}
		
		return current($resource->row);
  	}
	
	private function query_error($sql = '')
	{
		if (function_exists('debug_stack') && function_exists('html_dump')) {
			$stack = debug_stack();
			$_SESSION['debug']['call stack'] = $stack;
			html_dump($stack, 'call stack');
			echo '<br /><br />' . $sql;
		}
		
		if (function_exists('get_caller')) {
			trigger_error($this->driver->get_error() . get_caller(1) . get_caller(2));
		}
	}
	
	public function execute_file($file)
	{
		$content = file_get_contents($file);
		
		$lines = explode(";\r\n", $content);
		
		foreach ($lines as $line) {
			if (!empty($line) && is_string($line) && trim($line)) {
				$this->query($line);
			}
		}
		
		if ($this->get_error()) {
			return false;
		}
		
		return true;
	}
	
	public function dump($file, $tables = '')
	{
		_is_writable(dirname($file));
		
		$eol = "\r\n";
		
		if (!$tables) {
			$tables = $this->query_rows("SHOW TABLES");
		}
		
		$sql = '';
		
		foreach ($tables as $table) {
			if (is_array($table)) {
				$table = current($table);
			}
			
			if (!preg_match("/^".DB_PREFIX."/", $table)) {
				$table = DB_PREFIX . $table;
			}
			
			$sql .= "DROP TABLE IF EXISTS `$table`;" . $eol;
			$sql .= "CREATE TABLE `$table` (" . $eol;
			
			$columns = $this->query_rows("SHOW COLUMNS FROM `$table`");
			
			$primary_key = array();
			
			foreach ($columns as $column) {
				if (strtoupper($column['Key']) === 'PRI') {
					$primary_key[] = $column['Field'];
				}
				
				$field = $column['Field'];
				$type = $column['Type'];
				$null = strtoupper($column['Null']) === 'YES' ? '' : 'NOT NULL';
				
				if (is_null($column['Default'])) {
					if (!$null) {//meaning NULL is allowed
						$default = "DEFAULT NULL";
					} else {
						$default = "";
					}
				}else {
					$default = "DEFAULT '" . $this->escape(trim($column['Default'],"'\"")) . "'";
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
			
			$rows = $this->query_rows("SELECT * FROM `$table`");
			
			if (!empty($rows)) {
				$sql .= "INSERT INTO `$table` VALUES ";
				foreach ($rows as $row) {
					$sql .= "(";
					foreach ($row as $key => $value) {
						$sql .= "'" . $this->escape($value) . "',";
					}
					
					$sql = rtrim($sql,',') . "),";
				}
				
				$sql = rtrim($sql,',') . ";" . $eol . $eol;
			}
		}
		
		if (!file_put_contents($file, $sql)) {
			trigger_error("DB::dump(): Failed to dump database tables, $table_string, to file $file");
		
			return false;
		}
		
		if ($this->get_error()) {
			return false;
		}
		
		return true;
	}
	
	public function get_tables()
	{
		$result = $this->driver->query("SHOW TABLES");
		
		$tables = array();
		
		foreach ($result->rows as $row) {
			$tables[current($row)] = current($row);
		}
		
		return $tables;
	}
	
	public function count_tables()
	{
		$result = $this->driver->query("SHOW TABLES");
		
		return $result->num_rows;
	}
	
	public function get_key_column($table)
	{
		$result = $this->driver->query("SHOW KEYS FROM " . DB_PREFIX . "$table WHERE Key_name = 'PRIMARY'");
		
		if ($result->num_rows) {
			return $result->row['Column_name'];
		}
		
		return false;
	}
	
	public function has_column($table, $column)
	{
		$query = $this->driver->query("SHOW COLUMNS FROM " . DB_PREFIX . "$table");
		foreach ($query->rows as $row) {
			if(strtolower($row['Field']) == strtolower($column))
				return true;
		}
		return false;
	}
	
	public function table_add_column($table, $column, $type, $null=true, $after=null)
	{
		if (!$this->has_column($table, $column)) {
			$null = $null?"NULL":"NOT NULL";
			$after = $after?"AFTER `$after`":'';
			
			$this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` ADD COLUMN `$column` $type $null $after");
		}
	}
	
	public function table_drop_column($table, $column)
	{
		if ($this->has_column($table, $column)) {
			$this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
		}
	}
	
	public function set_autoincrement($table, $value)
	{
		if (!$this->driver->set_autoincrement($table, $value)) {
			trigger_error($this->driver->get_error());
		}
	}
	
	public function get_insert_string($data)
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
			trigger_error("DB:escape(): Argument for value was not a a valid type! Value: " . gettype($value) . ". " . get_caller() . " >>>> " . get_caller(2));
			exit;
		}
		
		return $this->driver->escape($value);
	}
	
	public function escape_html($value)
	{
		return $this->driver->escape_html($value);
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
