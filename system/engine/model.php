<?php
abstract class Model 
{
	protected $registry;
	
	public function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function __set($key, $value)
	{
		$this->registry->set($key, $value);
	}
	
	public function _($key)
	{
		if (func_num_args() > 1) {
			$args = func_get_args();
			
			return call_user_func_array(array($this->language, 'format'), $args);
		}
		
		return $this->language->get($key);
	}
	
	public function countAffected()
	{
		return $this->db->countAffected();
	}
	
	/**
	* Calls a controller and returns the output
	*
	* @param $route - The route (and function name) of the controller (eg. product/product OR product/product/review)
	* @param $arg1, $arg2, $arg3... etc. Additional arguments to pass to the controller
	*
	* @return mixed - usually HTML, output from the rendered controller
	*/
	protected function callController($route, $param)
	{
		//TODO: We can probably find a better way to implement this...
		
		$params = func_get_args();
		array_shift($params);
	
		$action = new Action($this->registry, $route, $param);
		
		if ($action->execute()) {
			return $action->get_result();
		} else {
			trigger_error('Error: Could not load controller ' . $route. '! The file ' . $file . ' does not exist.');
			exit();
		}
	}
	
	protected function query($sql)
	{
		$resource = $this->db->query($sql);
		if (!$resource) {
			if ($this->config->get('config_error_display')) {
				$this->message->add("warning", "The Database Query Failed! " . $this->db->getError());
			}
		}
		
		return $resource;
	}
	
	protected function queryRows($sql)
	{
		return $this->db->queryRows($sql);
	}
	
	protected function queryRow($sql)
	{
		return $this->db->queryRow($sql);
	}
	
	protected function queryColumn($sql)
	{
		return $this->db->queryColumn($sql);
	}
	
	protected function queryVar($sql)
	{
		return $this->db->queryVar($sql);
	}
	
	/**
	* Use to select rows from a talbe in the database
	*
	* @param $table - The table to select from
	* @param $select - The fields to select from the table
	* @param $where - Can be an associative array, string or integer.
	*					If it is an integer it will be treated as the primary key.
	*					If it is a string, it will be left untouched and passed as the WHERE value
	*					If it is an associative array it will be treated as `key` = 'value' pairs combined with "AND"
	*
	* @param $options - an associative array, options are
	*				'group_by' - An array of field names which will be combined with implode(',', $group_by),
	*								or it can be a string to allow for any GROUP BY (and HAVING) clause
	*
	*				'order_by' - An array of field names which will be combined with implode(',', $order_by),
	*								or it can be a string to allow for any ORDER BY clause
	*
	*				'limit' - An int (for limit only) or string in the format "start, limit"
	*
	* @return array - the rows that were retrieved from the database
	*/
	protected function get($table, $select, $where=null, $options=null)
	{
		if (isset($where)) {
			if (is_integer($where) || ((is_string($where) && preg_match("/[^\d]/", $where) == 0) && $where !== '')) {
				$primary_key = $this->get_primary_key($table);
				if (!$primary_key) {
					trigger_error("SELECT $table does not have an integer primary key!");
					return null;
				}
				
				$where = "`$primary_key` = '$where'";
			}
			elseif (is_array($where)) {
				$where = $this->get_escaped_values($table, $where, ' AND ');
			}
		}
	
		if ($where) {
			$where = "WHERE $where";
		}
		
		$group_by = '';
		if (isset($options['group_by'])) {
			$group_by = "GROUP BY ";
			
			if (is_array($options['group_by'])) {
				$group_by .= implode(',',$options['group_by']);
			}
			else {
				$group_by .= $options['group_by'];
			}
		}
		
		$order_by = '';
		if (isset($options['order_by'])) {
			$order_by = "ORDER BY ";
			
			if (is_array($options['order_by'])) {
				$order_by .= implode(',',$options['order_by']);
			}
			else {
				$order_by .= $options['order_by'];
			}
		}
		
		$limit = '';
		if (isset($options['limit'])) {
			$limit = "LIMIT " . $options['limit'];
		}
		
		$resource = $this->db->query("SELECT $select FROM " . DB_PREFIX . "$table $where $group_by $order_by $limit");
		
		if (!$resource) {
			$err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->getError():'';
			$this->message->add("warning", "There was a problem getting values for $table!" . $err_msg);
		}
		
		return $resource;
	}
	
	protected function insert($table, $data)
	{
		$this->action_filter('insert', $table, $data);
		
		$values = $this->get_escaped_values($table, $data, ',', false);
		
		$success = $this->db->query("INSERT INTO " . DB_PREFIX . "$table SET $values");
		
		if (!$success) {
			$err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->getError():'';
			$this->message->add("warning", "There was a problem inserting entry for $table! $table was not modified." . $err_msg);
			return false;
		}
		
		return $this->db->getLastId();
	}
	
	protected function update($table, $data, $where)
	{
		$this->action_filter('update', $table, $data);
		
		$table_model = $this->get_table_model($table);
		
		$values = $this->get_escaped_values($table, $data, ',');
		
		if (is_integer($where) || (is_string($where) && !preg_match("/[^\d]/", $where))) {
			$primary_key = $this->get_primary_key($table);
			if (!$primary_key) {
				trigger_error("UPDATE $table does not have an integer primary key!" . get_caller(0, 4));
				return null;
			}
			
			$where = "`$primary_key` = '$where'";
		}
		elseif (is_array($where)) {
			$where = $this->get_escaped_values($table, $where, ' AND ');
		}
		
		if ($where) {
			$where = "WHERE $where";
		}
		
		$success = $this->db->query("UPDATE " . DB_PREFIX . "$table SET $values $where");
		
		if (!$success) {
			$err_msg = "There was a problem updating entry for $table! $table was not modified." . get_caller(0, 4);
			trigger_error($err_msg);
			$this->message->add("warning", $err_msg);
			return false;
		}
		
		return true;
	}
	
	protected function delete($table, $where=null)
	{
		$this->action_filter('delete', $table, $data);
		
		if (is_integer($where) || (is_string($where) && preg_match("/[^\d]/", $where) == 0)) {
			$primary_key = $this->get_primary_key($table);
			if (!$primary_key) {
				trigger_error("DELETE $table does not have an integer primary key!");
				return null;
			}
			
			$where = "`$primary_key` = '$where'";
		}
		elseif (is_array($where)) {
			$where = $this->get_escaped_values($table, $where, ' AND ');
		}
		
		$table = $this->db->escape($table);
		
		if ($where) {
			$where = "WHERE $where";
		}
		else {
			$truncate_allowed = $this->db->queryVar("SELECT COUNT(*) FROM `" . DB_PREFIX . "db_rule` WHERE `table`='$table' AND `truncate`='1'");
			
			if (!$truncate_allowed) {
				$msg = "Attempt to TRUNCATE $table not allowed for this table! Please specify this in the Database Rules if you want this functionality. " . get_caller(0, 1);
				trigger_error($msg);
				$this->message->add('warning', $msg);
				return;
			}
		}
		
		$success = $this->db->query("DELETE FROM " . DB_PREFIX . "$table $where");
		
		if (!$success) {
			$err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->getError():'';
			$this->message->add("warning", "There was a problem deleting entry for $table! $table was not modified." . $err_msg);
			return false;
		}
		
		return true;
	}
	
	private function get_table_model($table)
	{
		$table_model = $this->cache->get('model.'.$table);
		
		if (!$table_model) {
			$table = $this->db->escape($table);
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "$table`");

			$rules_q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "db_rule` WHERE `table`='$table'");
			
			$rules = array();
			foreach ($rules_q->rows as $rule) {
				$rules[$rule['column']] = $rule['escape_type'];
			}
			
			$table_model = array();
			
			foreach ($query->rows as $row) {
				if (in_array($row['Field'], array_keys($rules))) {
					$table_model[$row['Field']] = $rules[$row['Field']];
				} else {
					$type = strtolower(trim(preg_replace("/\(.*$/",'',$row['Type'])));
					
					//we only care about ints and floats because only these we will do something besides escape
					$ints = array('bigint','mediumint','smallint','tinyint','int');
					$floats = array('decimal','float','double');
					
					if ($row['Key'] == 'PRI' && in_array($type, $ints)) {
						if ($row['Extra'] == 'auto_increment') {
							$escape_type = DB_AUTO_INCREMENT_PK;
						}
						else {
							$escape_type = DB_PRIMARY_KEY_INTEGER;
						}
					}
					elseif ($row['Extra'] == 'auto_increment') {
						$escape_type = DB_AUTO_INCREMENT;
					}
					elseif (in_array($type,$ints)) {
						$escape_type = DB_INTEGER;
					}
					elseif (in_array($type,$floats)) {
						$escape_type = DB_FLOAT;
					}
					elseif ($type == 'datetime') {
						$escape_type = DB_DATETIME;
					}
					elseif (strtolower($row['Field']) == 'image') {
						$escape_type = DB_IMAGE;
					}
					else {
						$escape_type = DB_ESCAPE;
					}
					
					$table_model[$row['Field']] = $escape_type;
				}
			}
			
			$this->cache->set('model.'.$table,$table_model);
		}
		
		return $table_model;
	}
	
	private function get_primary_key($table)
	{
		$table_model = $this->get_table_model($table);
		
		$primary_key = null;
		foreach ($table_model as $key=>$type) {
			if ($type == DB_PRIMARY_KEY_INTEGER || $type == DB_AUTO_INCREMENT_PK) {
				if ($primary_key) {
					return null;
				}
				$primary_key = $key;
			}
		}
		
		return $primary_key;
	}
	
	public function get_escaped_values($table, $data, $glue = false, $auto_inc = true)
	{
		$table_model = $this->get_table_model($table);
		
		$data = array_intersect_key($data, $table_model);
  		
		foreach ($data as $key => &$value) {
				
			if (is_resource($value) || is_array($value) || is_object($value)) {
				trigger_error("Model::escape_value(): The field $key was given a value that was not a valid type! Value: " . gettype($value) . ". " . get_caller(1));
				exit;
			}
			
			switch((int)$table_model[$key]){
				case DB_AUTO_INCREMENT_PK:
				case DB_AUTO_INCREMENT:
					if ($auto_inc) {
						$value = $this->db->escape($value);
					}
					else {
						unset($data[$key]);
					}
					break;
				case DB_ESCAPE:
					$value = $this->db->escape($value);
					break;
				case DB_NO_ESCAPE:
					break;
				case DB_IMAGE:
					$value = $this->db->escape(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
					break;
				case DB_INTEGER:
					$value = (int)$value;
					break;
				case DB_FLOAT:
					$value = (float)$value;
					break;
				case DB_DATETIME:
					if (!$value) {
						$value = DATETIME_ZERO;
					}
					$value = $this->date->format($value);
					break;
				
				default:
					$value = $this->db->escape($value);
					break;
			}
		}unset($value);
		
		if ($glue) {
			$values = '';
			
			foreach ($data as $key=>$value) {
				$values .= ($values ? $glue : '') . "`$key` = '$value'";
			}
			
			return $values;
		}
		
		return $data;
	}
	
	public function extract_order($data)
	{
		$sort = '';

		if (!empty($data['sort'])) {
			$order = (isset($data['order']) && strtoupper($data['order']) === 'DESC') ? 'DESC' : 'ASC';
			
			if (!strpos($data['sort'], '.')) {
				$data['sort'] = "`" . $this->db->escape($data['sort']) . "`";
			} else {
				$data['sort'] = $this->db->escape($data['sort']);
			}
			
			$sort = "ORDER BY " . $data['sort'] . " $order";
		}
		
		return $sort;
	}
	
	public function extract_limit($data)
	{
		$limit = '';
		
		if (isset($data['limit'])) {
			if ((int)$data['limit'] > 0) {
				
				$start = (isset($data['start']) && (int)$data['start'] > 0) ? (int)$data['start'] : 0;
			
				$limit = " LIMIT $start," . (int)$data['limit'];
			}
		}
		
		return $limit;
	}

	private function action_filter($action, $table, &$data)
	{
		$hooks = $this->config->get('db_hook_' . $action . '_' . $table);
		
		if ($hooks) {
			foreach ($hooks as $hook) {
				if (is_array($hook['callback'])) {
					$classname = key($hook['callback']);
					$method = current($hook['callback']);
					
					$class = $this->$classname;
					
					if (method_exists($class, $method)) {
						if (!is_array($hook['param'])) {
							$hook['param'] = array($hook['param']);
						}
						
						$params = array('__data__' => &$data) + $hook['param'];
						
						call_user_func_array(array($class, $method), $params);
					}
					else {
						trigger_error("Model::action_filter(): The following method does not exist: $class::$method().");
					}
				}
				else {
					if (function_exists($hook['callback'])) {
						$hook['callback']($hook['param']);
					}
					else {
						trigger_error("Model::action_filter(): The following function does not exist: $hook[callback]().");
					}
				}
			}
		}
	}
}
