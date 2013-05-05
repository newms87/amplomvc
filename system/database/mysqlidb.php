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
		   $this->err_msg = "MySQLi Error ($this->mysqli->errno): $this->mysqli->error<br />$sql";
			return false;
    	}
  	}
	
	public function execute_file($file){
		$sql = file_get_contents($file);
		
		if(!$sql){
			trigger_error("MySQLi::execute_file(): Error opening file $file.");
			return false;
		}
		
		return $this->query($sql);
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
