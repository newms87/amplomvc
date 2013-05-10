<?php
class DB {
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database) {
		//We cannot redeclare the mysqli class so mysqli is an alias for our wrapper class msyqlidb
		if($driver == 'mysqli'){
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
   
   public function get_error(){
      return $this->driver->get_error();
   }
   
  	public function query($sql) {
  	   //Use debug_backtrace to get caller class / function name. Then call any plugins requesting to modify query string
  	   $resource = $this->driver->query($sql);
		
      if(!$resource){
      	$stack = debug_stack();
      	$_SESSION['debug']['call stack'] = $stack; 
         trigger_error($this->driver->get_error());
			html_dump($stack, 'call stack');
         return false;
      }
      
		return $resource;
  	}
	
	public function execute_file($file){
		$result = $this->driver->execute_file($file);
		
		if(!is_null($result)){
			return $result;
		}
		
		$sql = file_get_contents($file);
		
		if(!$sql){
			trigger_error("DB::execute_file(): Error opening file $file.");
			return false;
		}
		
		if(!$this->driver->multi_query($sql)){
			trigger_error($this->get_error());
			
			return false;
		}
		
		return true;
	}
	
	public function dump($file, $tables = ''){
		if(!is_file($file)){
			trigger_error("DB::dump(): Could not find file $file. " . caller());
			return false;
		}
		
		_is_writable(dirname($file));
		
		if($this->driver->dump($file, $tables)){
			return true;
		}
		
		trigger_error("DB::dump(): Failed to dump database tables, $table_string, to file $file");
		
		return false;
	}
	
	public function get_tables(){
		$result = $this->driver->query("SHOW TABLES");
		
		$tables = array();
		
		foreach($result->rows as $row){
			$tables[current($row)] = current($row);
		}
		
		return $tables;
	}
	
	public function count_tables(){
		$result = $this->driver->query("SHOW TABLES");
		
		return $result->num_rows;
	}
	
	public function get_key_column($table){
		$result = $this->driver->query("SHOW KEYS FROM " . DB_PREFIX . "$table WHERE Key_name = 'PRIMARY'");
		
		if($result->num_rows){
			return $result->row['Column_name'];
		}
		
		return false;
	}
	
   public function has_column($table, $column){
      $query = $this->driver->query("SHOW COLUMNS FROM " . DB_PREFIX . "$table");
      foreach($query->rows as $row){
         if(strtolower($row['Field']) == strtolower($column))
            return true;
      }
      return false;
   }
   
   public function table_add_column($table, $column, $type, $null=true, $after=null){
      if(!$this->has_column($table, $column)){
         $null = $null?"NULL":"NOT NULL";
         $after = $after?"AFTER `$after`":'';
         
         $this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` ADD COLUMN `$column` $type $null $after");
      }
   }
   
   public function table_drop_column($table, $column){
      if($this->has_column($table, $column)){
         $this->driver->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
      }
   }
   
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
	public function escape_html($value){
		return $this->driver->escape_html($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}
}
