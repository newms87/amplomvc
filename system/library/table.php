<?php
class Table {
	private $columns = array();
	private $file;
	private $path;
	private $route;
	private $row_id;
	private $data;
	private $extra_data = array();
	private $table_data;
	
	private $error = array();
	private $controller;
	
	function __construct($table, $controller = null){
		$this->columns = $table['columns'];
		
		$this->route = isset($table['route']) ? $table['route'] : '';
		$this->row_id = isset($table['row_id']) ? $table['row_id'] : '';
		
		if(!empty($table['data'])){
			$this->extra_data = $table['data'];
		}
		
		$this->controller = $controller;
	}
	
	public function __get($key){
		return $this->controller->$key;
	}
	
	function initialize($controller){
		$this->controller = $controller;
		$this->path		= $controller->template->template();
		$this->data		= $controller->data;
		
		$this->extra_data = array();
	}
	
	public function get_errors(){
		return $this->error;
	}
	
	public function get_columns(){
		return $this->columns;
	}
	
	public function get_filter_columns(){
		$filter_list = array();
		
		foreach($this->columns as $name => $col){
			$filter_list[$name] = isset($col['filter_default']) ? $col['filter_default'] : '';
		}
	}
	
	public function get_column_value($column, $key){
		if(isset($this->columns[$column][$key])){
			return $this->columns[$column][$key];
		}
		
		return null;
	}
	
	/**
	* Sets the Filter for this column used to filter rows from the database
	* 
	* @param $column (string) - The column to add filter data to
	* @param $type (string) -  filter input style.  Can be: 'text', 'select', 'date_range', 'time_range', 'datetime_range'
	* @param $data (array) - Used with 'select' type only. This is the data associative array that can be in key value pairs OR array of arrays with the config set for ID and Name keys
	* @param $config - Used with 'select' type only. Key value pair of ID key and Name key
	* 
	* 		eg: $column = 'name_column'
	* 			$type = 'select'
	* 			$data = array( array( 'id_key' => 3, 'name_key' => 'Dan'), array( 'id_key' => 4, 'name_key' => 'Newman') )
	* 			$config = array('id_key' => 'name_key')
	*/
	public function set_column_filter($column, $type, $data = null , $config = null){
		if(!isset($this->columns[$column]['filter'])){
			$this->columns[$column]['filter'] = true;
		}
		
		$this->columns[$column]['filter_type'] = $type;
		$this->columns[$column]['filter_data'] = $data;
		$this->columns[$column]['filter_config'] = $config;
	}
	
	public function set_column_filter_value($column, $value){
		$this->columns[$column]['filter_value'] = $value;
	}
	
	/**
	* Sets the Filter for this column used to filter rows from the database
	* 
	* @param $column (string) - The column to add filter data to
	* @param $type (string) -  filter input style.  Can be: 'text', 'int', 'date', 'text_list', 'map', 'map_array', 'assoc_array', 'format', or 'image'
	* @param $data (array) - Used with 'text_list', 'map', or 'format' types only.
	* 								For 'text_list' $data is the key to use when the cell data is an associative array.
	* 								For 'map' $data is a key value pair array with key as the value for the column and the value is the mapped data value that should be output to the cell.
	* 								For 'map_array' $data is an associative array used with $config. This is a pseudo type, that gets converted to a map type using the $config info.
	* 								For 'assoc_array' $data is an array map, which maps the output of the cell data associative array. Set $config to specify the key of the cell data array that should be used.
	* 										If $data is an array of associative arrays, you can specify $config to contain the key => value pair to convert $data to a map.
	* 										(eg: $data = array( array('key_name' => 'value1', 'value_name' => 'value2', ...), array('key_name' => 'value3', 'value_name' => 'value4'), ...), provide $config as array('key_name' => 'value_name', 'cell_data_key' ) 
	* 								For 'format' $data is the formatting style that should be used.
	* @param $config - Used with 'select' type only. Key value pair of ID key and Name key
	* 
	* 		eg: $column = 'name_column'
	* 			$type = 'text_list'
	* 			$data = array( array( 'id_key' => 3, 'name_key' => 'Dan'), array( 'id_key' => 4, 'name_key' => 'Newman') )
	* 			$config = array('id_key' => 'name_key')
	*/
	public function set_column_cell_data($column, $type, $data = null, $config = null){
		//for map_array pseudo type we convert the data associative array into a map
		if($type === 'map_array'){
			$new_data = array();
			foreach($data as $d){
				$new_data[$d[key($config)]] = $d[current($config)]; 
			}
			
			$data = $new_data;
			$type = 'map';
			$config = null;
		}
		
		//we need to conver the data to a map if it is an associative array
		if($type === 'assoc_array' && is_array(current($data))){
			$new_data = array();
			foreach($data as $d){
				$new_data[$d[key($config)]] = $d[current($config)]; 
			}
			
			$data = $new_data;
			next($config);
			$config = current($config);
		}
		
		$this->columns[$column]['display_type'] = $type;
		$this->columns[$column]['display_data'] = $data;
		$this->columns[$column]['display_config'] = $config;
	}
	
	public function get_table_data(){
		return $this->table_data;
	}
	
	public function set_table_data($data){
		$this->table_data = $data;
	}
	
	public function set_column_value($column, $key, $value){
		if(isset($this->columns[$column])){
			$this->columns[$column][$key] = $value;
		}
	}
	
	public function set_template($file){
		if(!preg_match("/\.tpl$/", $file)){
			$file .= '.tpl';
		}
		
		if (file_exists(DIR_THEME . $this->path . $file)) {
			$this->file = DIR_THEME . $this->path . $file; 
		}
		elseif(file_exists(DIR_THEME . 'default/template/' . $file)) {
			$this->file = DIR_THEME . 'default/template/' . $file;
		}
		else{
			list(,$caller) = debug_backtrace(false);
			trigger_error("Error: Could not load form template " . DIR_THEME . $this->path . $file . "! Called from $caller[class]::$caller[function]().");
			exit();
		}
	}
	
	public function set_columns($columns){
		$this->columns = $columns;
	}
	
	public function set_route($route){
		$this->route = $route;
	}
	
	public function set_row_id($id){
		$this->row_id = $id;
	}
	
	public function add_extra_data($data){
		$this->extra_data += $data;
	}
	
	public function build(){
		$this->prepare();
		
		//make the columns accessible from the template file
		$columns = $this->columns;
		
		extract($this->language->data);
		
		//add any additional data for the form template
		$this->extra_data ? extract($this->extra_data) : '';
		
		$table_data = $this->table_data;
		
		$row_id = $this->row_id;
		
		$route = $this->route;
		
		//render the file
		ob_start();
		
		require($this->file);
		
		$output = ob_get_contents();
			
		ob_end_clean();
		
		return $output;
	}
	
	private function prepare(){
		if(!$this->file || !file_exists($this->file)){
			trigger_error("You must set the template for the form before building! " . get_caller(3));
			exit();
		}
		
		if(!isset($this->table_data)){
			trigger_error("The table data was not set! Please call Table::set_table_data(\$data) before building! " . get_caller(3));
			exit();
		}
		
		foreach($this->columns as $name => &$column){
			
			if(!isset($column['type'])){
				trigger_error("Invalid table column! The type was not set for $name! " . get_caller(3));
				exit();
			}
			
			$default_values = array(
				'name' => $name,
				'attrs' => array(),
				'filter' => false,
				'display_type' => 'text',
				'align' => 'left',
				'sortable' => false,
			);
			
			foreach($default_values as $key => $default){
				if(!isset($column[$key])){
					$column[$key] = $default;
				}
			}
			
			if(!isset($column['display_name'])){
				$column['display_name'] = $this->language->get('column_' . $name);
			}
			
			if(empty($column['filter'])){
				$column['filter_type'] = null;
			}
			elseif(!isset($column['filter_value'])){
				$column['filter_value'] = '';
			}
			
			//additional / overridden attributes
			foreach($column as $attr => $value){
				if(strpos($attr, '#') === 0){
					$column['attrs'][substr($attr,1)] = $value;
				}
			}
			
			$column['html_attrs'] = '';
			
			foreach($column['attrs'] as $attr => $value){
				$column['html_attrs'] .= $attr . '="' . $value . '" ';
			}
						
			switch($column['type']){
				case 'text':
					break;
				case 'multi':
					break;
				case 'image':
					break;
				default: 
					break;
			}
			
			//Prepare Filters
			switch ($column['filter_type']) {
			case 'text':
				break;
			
			case 'date_range':
			case 'time_range':
			case 'datetime_range':
				break;
				
			case 'select':
				if(empty($column['filter_data'])){
					list(,,$caller) = debug_backtrace(false);
					trigger_error("You must specify filter_data for a select filter type! Called from $caller[file] on line $caller[line].");
					exit();
				}
				break;
			default;
			}
			
			
			switch ($column['display_type']) {
			case 'text':
			case 'int':
			case 'date':
				break;
				
			case 'text_list':
				if(empty($column['display_data'])){
					trigger_error("You must provide the display_data for a 'text_list' display type for $name! " . get_caller(3));
					exit();
				}
				break;
				
			case 'map':
				if(!isset($column['display_data'])){
					trigger_error("You must provide the display_data for a 'map' display type for column $name! " . get_caller(3));
					exit();
				}
				break;
				
			case 'format':
				if(empty($column['display_data'])){
					trigger_error("You must provide the display_data for a 'format' display type for column $name! " . get_caller(3));
					exit();
				}
				break;
				
			case 'image':
				$image_width = isset($column['image_width']) ? $column['image_width'] : $this->config->get('config_image_admin_list_width');
				$image_height = isset($column['image_height']) ? $column['image_height'] : $this->config->get('config_image_admin_list_height');
				
				foreach($this->table_data as &$item){
					$column['display_data'] = $this->image->resize($item[$name], $image_width, $image_height);
				}
				break;
			
			default:
				break;
			}
		}
	}
}