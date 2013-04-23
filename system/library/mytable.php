<?php
class mytable {
   private $file;
	private $table;
	private $path;
	private $registry;
   
   function __construct($registry){
   	$this->registry = $registry;
   }
	
	public function __get($key){
		return $this->registry->get($key);
	}
   
	public function init(){
		$this->path = '';
	}
	
	public function set_path($path){
		$this->path = $path;
	}
	
	public function set_table($table){
		$this->table = $table;
	}
	
   public function set_template($file){
      if(!preg_match("/\.tpl$/", $file)){
         $file .= '.tpl';
      }
      
      if (file_exists(DIR_TEMPLATE . $this->path . $file)) {
         $this->file = DIR_TEMPLATE . $this->path . $file; 
      }
      elseif(file_exists(DIR_TEMPLATE . 'default/template/' . $file)) {
         $this->file = DIR_TEMPLATE . 'default/template/' . $file;
      }
      else{
         trigger_error("Error: Could not load form template " . DIR_TEMPLATE . $this->path . $file . "!" . get_caller(3));
         exit();
      }
   }
   
	public function map_attribute($attr, $values){
		if(empty($this->table['columns'])){
			trigger_error("Error: You must set the table structure with Table::set_table() before mapping data!" . get_caller(3));
			exit();
		}
		
		foreach($this->table['columns'] as $slug => &$column){
			$column[$attr] = isset($values[$slug]) ? $values[$slug] : null;
		}
	}
	
   public function build(){
     	$this->prepare();
      
      extract($this->table);
		
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
      
      if(!isset($this->table)){
         trigger_error("The table structure was not set! Please call Table::set_table(\$table) before building! " . get_caller(3));
         exit();
      }
      
      foreach($this->table['columns'] as $slug => &$column){
         
         if(!isset($column['type'])){
            trigger_error("Invalid table column! The type was not set for $slug! " . get_caller(3));
            exit();
         }
         
         $default_values = array(
            'display_name' => $slug,
            'attrs' => array(),
            'filter' => false,
            'type' => 'text',
            'align' => 'center',
            'sortable' => false,
         );
         
         foreach($default_values as $key => $default){
            if(!isset($column[$key])){
               $column[$key] = $default;
            }
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
         
			
			//This sets a blank option in a dropdown by default
			if($column['filter']){
				if(in_array($column['type'], array('select','multiselect')) && !isset($column['filter_blank'])){
					$column['filter_blank'] = true;
				}
			}
			
			switch($column['type']){
            case 'text':
               break;
            case 'multi':
               break;
            case 'image':
					if(!isset($column["sort_value"])){
						$column['sort_value'] = "__image_sort__" . $slug;
					}
               break;
				case 'select':
					if(empty($column['build_data'])){
						trigger_error("You must specify build_data for the column $slug of type select! " . get_caller(3));
						exit();
					}
					
					if(!isset($column['build_config'])){
						if(is_array(current($column['build_data']))){
							trigger_error("You must specify build_config for the column $slug of type select with this nature of build_data! " . get_caller(3));
							exit();
						}
					}
					
					//normalize the data for easier processing
					foreach($column['build_data'] as $key => &$bd_item){
						$bd_item = array(
							'key' => $key,
							'name' => $bd_item
						);
					}
					$column['build_config'] = array('key' => 'name');
					
					break;
            default: 
               break;
         }

			if(!isset($column["sort_value"])){
				$column["sort_value"] = $slug;
			}
			
      }
   }
}