<?php
class DB {
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database) {
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
	
   private function has_column($table, $column){
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
