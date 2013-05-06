<?php
final class mysqlidb implements Database{
	private $mysqli;
	private $err_msg;
	
	public function __construct($hostname, $username, $password, $database) {
		$this->mysqli = new mysqli($hostname, $username, $password, $database);
		
		if ($this->mysqli->connect_error) {
		    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		elseif (mysqli_connect_error()) {
		    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
		
		$this->query("SET NAMES 'utf8'");
		$this->query("SET CHARACTER SET utf8");
		$this->query("SET CHARACTER_SET_CONNECTION=utf8");
		$this->query("SET SQL_MODE = ''");
  	}
   
   public function get_error(){
      return $this->err_msg;
   }
		
  	public function query($sql) {
		$result = $this->mysqli->query($sql);

		if ($result) {
			if (is_object($result)) {
				$data = array();
		
				while ($row = $result->fetch_assoc()){
					$data[] = $row;
				}
				
				$result->free();
				
				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = count($data);

				return $query;
    		} else {
				return true;
			}
		} else {
		   $this->err_msg = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";
			
			return false;
    	}
  	}
	
  	public function multi_query($sql) {
		$this->mysqli->multi_query($sql);
		
		while ($this->mysqli->more_results() && $this->mysqli->next_result()){
		}
		
		if($this->mysqli->errno) {
		   $this->err_msg = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";
			
			return false;
    	}
		
		return true;
  	}
		
	public function execute_file($file){
		$mysql = defined("DB_MYSQL_FILE") ? DB_MYSQL_FILE : 'mysql';
		
		$file = escapeshellarg($file);
		
		$cmd = "\"$mysql\" --max_allowed_packet=2G --user=\"" . DB_USERNAME . "\" --password=\"" . DB_PASSWORD . "\" --host=\"" . DB_HOSTNAME . "\" " . DB_DATABASE . " < $file";
		
		$error_file = DIR_LOGS . 'db_file_error.txt';
		
		shell_exec($cmd . ' 2> ' . $error_file);
		
		if(filesize($error_file) > 1){
			$has_error = false;
			
			$handle = fopen($error_file, 'r');
			
			while (($buffer = fgets($handle, 4096)) !== false) {
				if(strpos($buffer, 'ERROR') !== false){
					$this->err_msg = "MySQLi::execute_file(): " . $buffer;
					$has_error = true;
				}
   		}
			
			fclose($handle);
			
			file_put_contents($error_file, '');
			
			if($has_error) return false;
			
			if(!defined("DB_MYSQL_FILE") ||  !is_file(DB_MYSQL_FILE)){
				trigger_error("You must define DB_MYSQL_FILE to contain the file and path to mysql (mysql.exe on windows) for execute_file()!");
				return null;
			}
		}
		
		return true;
	}
	
	public function dump($file, $tables = null){
		$mysqldump = defined("DB_MYSQLDUMP_FILE") ? DB_MYSQLDUMP_FILE : 'mysqldump';
		
		if(!empty($tables)){
			$tables = implode(' ', $tables);
		}
		else{
			$tables = '';
		}
		
		$file = escapeshellarg($file);
		
		$cmd = "\"$mysqldump\" --user=\"" . DB_USERNAME . "\" --password=\"" . DB_PASSWORD . "\" --host=\"" . DB_HOSTNAME . "\" " . DB_DATABASE . " $tables > $file";
		
		if(shell_exec($cmd . ' | echo 1') === null){
			if(!defined("DB_MYSQLDUMP_FILE") ||  !is_file(DB_MYSQLDUMP_FILE)){
				trigger_error("You must define DB_MYSQLDUMP_FILE to contain the file and path to mysqldump (mysqldump.exe on windows) for dump!");
				return false;
			}
		}
		
		return true;
	}
	
	public function escape($value) {
		return $this->mysqli->real_escape_string($value);
	}
	
	public function escape_html($value){
		return $this->mysqli->real_escape_string(htmlspecialchars_decode($value));
	}
	
  	public function countAffected() {
    	return $this->mysqli->affected_rows;
  	}

  	public function getLastId() {
    	return $this->mysqli->insert_id;
  	}
	
	public function __destruct() {
		$this->mysqli->close();
	}
}
