<?php
class DB 
{
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database)
	{
		//We cannot redeclare the mysqli class so mysqli is an alias for our wrapper class msyqlidb
		if($driver == 'mysqli')
{
			$driver = 'mysqlidb';
		}
		
		//the database interface
		_require_once(DIR_DATABASE . 'database.php');
		
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			_require_once(DIR_DATABASE . $driver . '.php');
		} else {
			exit('Error: Could not load database file ' . $driver . '!');
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
	}
	
	public function get_error()
	{
		return $this->driver->get_error();
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
			$this->query_error();
			
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
			$this->query_error();
			
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
			$this->query_error();
			
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
			$this->query_error();
			
			return null;
		}
		
		return current($resource->row);
  	}
	
	private function query_error()
	{
		$stack = debug_stack();
		$_SESSION['debug']['call stack'] = $stack;
		trigger_error($this->driver->get_error() . get_caller(2));
		html_dump($stack, 'call stack');
	}
	
	public function execute_file($file)
	{
		$result = $this->driver->execute_file($file);
		
		if (!is_null($result)) {
			return $result;
		}
		
		$sql = file_get_contents($file);
		
		if (!$sql) {
			trigger_error("DB::execute_file(): Error opening file $file.");
			return false;
		}
		
		if (!$this->driver->multi_query($sql)) {
			trigger_error($this->get_error());
			
			return false;
		}
		
		return true;
	}
	
	public function dump($file, $tables = '')
	{
		_is_writable(dirname($file));
		
		if ($this->driver->dump($file, $tables)) {
			return true;
		}
		
		trigger_error("DB::dump(): Failed to dump database tables, $table_string, to file $file");
		
		return false;
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
