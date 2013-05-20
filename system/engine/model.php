<?php
abstract class Model {
	protected $registry;
	
	public function __construct(&$registry) {
		$this->registry = &$registry;
	}
	
	public function __get($key) {
      return $this->registry->get($key);
   }
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
   
   public function _($key){
      return $this->language->get($key);
   }
   
	public function countAffected(){
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
   protected function callController($route){
      $params = func_get_args();
      array_shift($params);
   
      $action = new Action($route);
      $file = $action->getFile();
      $class = $action->getClass();
      $class_path = $action->getClassPath();
      $method = $action->getMethod();
      
      if (file_exists($file)) {
         _require_once($file);

         $controller = new $class($class_path, $this->registry);

         call_user_func_array(array($controller, $method), $params);
         
         return $controller->output;
      } else {
         trigger_error('Error: Could not load controller ' . $route. '! The file ' . $file . ' does not exist.');
         exit();
      }
   }
   
   protected function query($sql){
      $resource = $this->db->query($sql);
      if(!$resource){
         if($this->config->get('config_error_display')){
            $this->message->add("warning", "The Database Query Failed! " . $this->db->get_error());
         }
      }
      
      return $resource;
   }
   
   /**
    * Use to select rows from a talbe in the database
    * 
    * @param $table - The table to select from
    * @param $select - The fields to select from the table
    * @param $where - Can be an associative array, string or integer. 
    *                 If it is an integer it will be treated as the primary key.
    *                 If it is a string, it will be left untouched and passed as the WHERE value
    *                 If it is an associative array it will be treated as `key` = 'value' pairs combined with "AND"
    * 
    * @param $options - an associative array, options are 
    *             'group_by' - An array of field names which will be combined with implode(',', $group_by),
    *                          or it can be a string to allow for any GROUP BY (and HAVING) clause
    *             
    *             'order_by' - An array of field names which will be combined with implode(',', $order_by),
    *                          or it can be a string to allow for any ORDER BY clause
    *             
    *             'limit' - An int (for limit only) or string in the format "start, limit"
    * 
    * @return array - the rows that were retrieved from the database 
    */
   protected function get($table, $select, $where=null, $options=null){
      if(isset($where)){
         if(is_integer($where) || ((is_string($where) && preg_match("/[^\d]/", $where) == 0) && $where !== '')){
            $primary_key = $this->get_primary_key($table);
            if(!$primary_key){
               trigger_error("SELECT $table does not have an integer primary key!");
               return null;
            }
            
            $where = "`$primary_key` = '$where'";
         }
         elseif(is_array($where)){
            $where_escaped = $this->get_escaped_values($table, $where);
            
            $where = '';
            foreach($where_escaped as $key=>$value){
               $where .= ($where?' AND ':'') . "`$key`='$value'";
            }
         }
      }
   
      if($where){
         $where = "WHERE $where";
      }
      
      $group_by = '';
      if(isset($options['group_by'])){
         $group_by = "GROUP BY ";
         
         if(is_array($options['group_by'])){
            $group_by .= implode(',',$options['group_by']);
         }
         else{
            $group_by .= $options['group_by'];
         }
      }
      
      $order_by = '';
      if(isset($options['order_by'])){
         $order_by = "ORDER BY ";
         
         if(is_array($options['order_by'])){
            $order_by .= implode(',',$options['order_by']);
         }
         else{
            $order_by .= $options['order_by'];
         }
      }
      
      $limit = '';
      if(isset($options['limit'])){
         $limit = "LIMIT " . $options['limit'];
      }
      
      $resource = $this->db->query("SELECT $select FROM " . DB_PREFIX . "$table $where $group_by $order_by $limit");
      
      if(!$resource){
         $err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->get_error():'';
         $this->message->add("warning", "There was a problem getting values for $table!" . $err_msg);
      }
      
      return $resource;
   }
   
   protected function insert($table, $data){
   	$this->action_filter('insert', $table, $data);
		
      //This will grab the info for the function that called insert()
      list(,$caller) = debug_backtrace(false);
      
      $this->plugin_handler->execute_db_requests($table, 'insert', 'before', $caller['class'], $caller['function'], $data);
		
      $escaped_values = $this->get_escaped_values($table, $data, false);
		
      $values = '';
      foreach($escaped_values as $key=>$value){
         $values .= ($values?',':'') . "`$key`='$value'";
      }
      
      $success = $this->db->query("INSERT INTO " . DB_PREFIX . "$table SET $values");
      
      $data['last_insert_id'] = $last_id = $this->db->getLastId();
      
      $this->plugin_handler->execute_db_requests($table, 'insert', 'after', $caller['class'], $caller['function'], $data);
      
      if(!$success){
         $err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->get_error():'';
         $this->message->add("warning", "There was a problem inserting entry for $table! $table was not modified." . $err_msg);
         return false;
      }
      
      return $last_id;
   }
   
   protected function update($table, $data, $where){
   	$this->action_filter('update', $table, $data);
		
      //This will grab the info for the function that called update()
      list(,$caller) = debug_backtrace(false);
      
      $this->plugin_handler->execute_db_requests($table, 'update', 'before', $caller['class'], $caller['function'], $data, $where);
      
      $table_model = $this->get_table_model($table);
      
      $escaped_values = $this->get_escaped_values($table, $data);
      
      $values = '';
      foreach($escaped_values as $key=>$value){
         $values .= ($values?',':'') . "`$key`='$value'";
      }
      
      if(is_integer($where) || (is_string($where) && preg_match("/[^\d]/", $where) == 0)){
         $primary_key = $this->get_primary_key($table);
         if(!$primary_key){
            trigger_error($this->tool->error_info() . "UPDATE $table does not have an integer primary key!");
            return null;
         }
         
         $where = "`$primary_key` = '$where'";
      }
      elseif(is_array($where)){
         $where_escaped = $this->get_escaped_values($table, $where);

         if(!empty($where_escaped)){
            $where = '';
            foreach($where_escaped as $key=>$value){
               $where .= ($where?' AND ':'') . "`$key`='$value'";
            }
         }
      }
      
      if($where){
         $where = "WHERE $where";
      }
      
      $success = $this->db->query("UPDATE " . DB_PREFIX . "$table SET $values $where");
      
      $this->plugin_handler->execute_db_requests($table, 'update', 'after', $caller['class'],$caller['function'], $data, $where);
      
      if(!$success){
         $err_msg = $this->tool->error_info() . "There was a problem updating entry for $table! $table was not modified.";
         trigger_error($err_msg);
         $this->message->add("warning", $err_msg);
         return false;
      }
      
      return true;
   }
   
   protected function delete($table, $where=null){
   	$this->action_filter('delete', $table, $data);
		
      //This will grab the info for the function that called delete()
      list(,$caller) = debug_backtrace(false);
      
      $nothing = array();
      $this->plugin_handler->execute_db_requests($table, 'insert', 'before', $caller['class'],$caller['function'], $nothing, $where);
      
      if(is_integer($where) || (is_string($where) && preg_match("/[^\d]/", $where) == 0)){
         $primary_key = $this->get_primary_key($table);
         if(!$primary_key){
            trigger_error("DELETE $table does not have an integer primary key!");
            return null;
         }
         
         $where = "`$primary_key` = '$where'";
      }
      elseif(is_array($where)){
         $where_escaped = $this->get_escaped_values($table, $where);
      
         $where = '';
         foreach($where_escaped as $key=>$value){
            $where .= ($where?' AND ':'') . "`$key`='$value'";
         }
      }
      
      $table = $this->db->escape($table);
      
      if($where){
         $where = "WHERE $where";
      }
      else{
         $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "db_rule` WHERE `table`='$table' AND `truncate`='1'");
         if(!$query->num_rows){
            $args = func_get_args();
            $msg = "TRUNCATE $table Not allowed for this table! \$where = " . print_r(isset($args[1])?$args[1]:'',true);
            trigger_error($msg);
            $this->message->add('warning', $msg);
            return;
         }
      }
      
      $success = $this->db->query("DELETE FROM " . DB_PREFIX . "$table $where");
      
      $this->plugin_handler->execute_db_requests($table, 'insert', 'after', $caller['class'],$caller['function'], $nothing, $where);
      
      if(!$success){
         $err_msg = $this->config->get('config_error_display')?"<br /><br />" . $this->db->get_error():'';
         $this->message->add("warning", "There was a problem deleting entry for $table! $table was not modified." . $err_msg);
         return false;
      }
      
      return true;
   }
   
   private function get_table_model($table){
      $table_model = $this->cache->get('model.'.$table);
      
      if(!$table_model){
         $table = $this->db->escape($table);
         $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "$table`");

         $rules_q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "db_rule` WHERE `table`='$table'");
         
         $rules = array();
         foreach($rules_q->rows as $rule){
            $rules[$rule['column']] = $rule['escape_type'];
         }
         
         $table_model = array();
         
         foreach($query->rows as $row){
            if(in_array($row['Field'], array_keys($rules))){
               $table_model[$row['Field']] = $rules[$row['Field']];
            }else{
               $type = strtolower(trim(preg_replace("/\(.*$/",'',$row['Type'])));
               
               //we only care about ints and floats because only these we will do something besides escape
               $ints = array('bigint','mediumint','smallint','tinyint','int');
               $floats = array('decimal','float','double');
               
               if($row['Key'] == 'PRI' && in_array($type, $ints)){
                  if($row['Extra'] == 'auto_increment'){
                     $escape_type = DB_AUTO_INCREMENT_PK;
                  }
                  else{
                     $escape_type = DB_PRIMARY_KEY_INTEGER;
                  }
               }
               elseif($row['Extra'] == 'auto_increment'){
                  $escape_type = DB_AUTO_INCREMENT;
               }
               elseif(in_array($type,$ints)){
                  $escape_type = DB_INTEGER;
               }
               elseif(in_array($type,$floats)){
                  $escape_type = DB_FLOAT;
               }
               elseif($type == 'datetime'){
                  $escape_type = DB_DATETIME;
               }
               elseif(strtolower($row['Field']) == 'image'){
                  $escape_type = DB_IMAGE;
               }
               else{
                  $escape_type = DB_ESCAPE;
               }
               
               $table_model[$row['Field']] = $escape_type;
            }
         }
         
         $this->cache->set('model.'.$table,$table_model);
      }
      
      return $table_model;
   }
   
   private function get_primary_key($table){
      $table_model = $this->get_table_model($table);
      
      $primary_key = null;
      foreach($table_model as $key=>$type){
         if($type == DB_PRIMARY_KEY_INTEGER || $type == DB_AUTO_INCREMENT_PK){
            if($primary_key){
               return null;
            }
            $primary_key = $key;
         }
      }
      
      return $primary_key;
   }
   
   private function get_escaped_values($table, $data, $auto_inc=true){
      $table_model = $this->get_table_model($table);
      
      $data = array_intersect_key($data, $table_model);
  
      foreach($data as $key => &$value){
      		
      	if(is_resource($value) || is_array($value) || is_object($value)){
				trigger_error("Model::escape_value(): The field $key was given a value that was not a valid type! Value: " . gettype($value) . ". " . get_caller(1));
				exit;
			}
			
         switch((int)$table_model[$key]){
            case DB_AUTO_INCREMENT_PK:
            case DB_AUTO_INCREMENT:
               if($auto_inc){
                  $value = $this->db->escape($value);
               }
               else{
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
               if(!$value){
                  $value = DATETIME_ZERO;
               }
               $value = $this->tool->format_datetime($value);
               break;
            
            default:
               $value = $this->db->escape($value);
               break; 
         }
      }
      
      return $data;
   }
   
   public function execute($table, $select = '*', $tables = array(), $where = array(), $data = array()){
      if($tables && !preg_match("/[A-Z0-9_]+[\s]+[A-Z0-9_]+/i", $table)){
         trigger_error("Table $table is invalid for Model::execute()! Should be in format `table_name t` where t is the table alias");
         exit();
      }
      $table_string = $this->get_table_string($tables);
      
      $where_string = $this->build_where_string($where);
      
      $order_limit_string = $this->extract_order_limit_string($data);
      
      $sql = "SELECT $select FROM " . DB_PREFIX . "$table $table_string $where_string $order_limit_string";
      
      return $this->query($sql);
   }
   
   public function get_table_string($tables){
      if(!$tables) return '';
      
      $table_string = '';
      
      foreach($tables as $join => $table){
         foreach($table as $table_name => $on){
            $table_string .= " $join " . DB_PREFIX . "$table_name ON ( $on )";
         }
      }
      
      return $table_string;
   }
   
   public function build_where_string($where){
      if(!$where) return '';
      
      if(is_string($where)){
         return "WHERE $where";
      }
      
      $and_string = isset($where['AND']) ? implode(' AND ', $where['AND']) : '';
      
      $or_string = isset($where['OR']) ? implode(' OR ', $where['OR']) : '';
       
      return "WHERE $and_string $or_string";
   }
   
   public function extract_order_limit_string($data){
      $order_limit = '';

      if(isset($data['sort'])){
         $order = (isset($data['order']) && strtoupper($data['order']) == 'DESC') ? 'DESC' : 'ASC';
         
         if(!strpos($data['sort'], '.')){
            $data['sort'] = "`" . $this->db->escape($data['sort']) . "`";
         }
         else{
            $data['sort'] = $this->db->escape($data['sort']);
         }
         
         $order_limit .= "ORDER BY " . $data['sort'] . " $order";
      }
      
      if(isset($data['limit'])){
         $limit = (int)$data['limit'] > 0 ? (int)$data['limit'] : 0;
         
         $start = (isset($data['start']) && (int)$data['start'] > 0) ? (int)$data['start'] : 0;
         
         $order_limit .= " LIMIT $start, $limit";
      }
      
      return $order_limit;
   }

	private function action_filter($action, $table, &$data){
		$hooks = $this->config->get('db_hook_' . $action . '_' . $table);
		
		if($hooks){
			foreach($hooks as $hook){
				if(is_array($hook['callback'])){
					$classname = key($hook['callback']);
					$function = current($hook['callback']);
					
					$class = $this->$classname;
					
					if(method_exists($class, $function)){
						$class->$function($data, $hook['param']);
					}
					else{
						trigger_error("Model::action_filter(): The following method does not exist: $class::$function().");
					}
				}
				else{
					if(function_exists($hook['callback'])){
						$hook['callback']($hook['param']);
					}
					else{
						trigger_error("Model::action_filter(): The following function does not exist: $hook[callback]().");
					}
				}
			}
		}
	}
}
