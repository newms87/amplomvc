<?php
class Template {
	private $registry;
	
	public $data = array();
   
   private $tables  = array();
   private $forms   = array();
   private $blocks  = array();
   private $options = array();
   
   private $name;
   private $file;
	
   private $db;
   private $cache;
   private $config;
   private $language;
   private $url;
   
   private $template;
   private $theme;
   
	private $controller;
	
   function __construct(&$registry, $theme = '', $controller = null){
   	$this->registry = $registry;
		
      if($controller){
         $this->controller = $controller;
         $this->db         = $controller->db;
         $this->cache      = $controller->cache;
         $this->config     = $controller->config;
         $this->language   = $controller->language;
         $this->url        = $controller->url;
      }
      
      if($theme){
         $this->template = trim($theme, '/') . '/template/';
         $this->theme = $theme;
      }
      else{
         $this->template = '';
         $this->theme = 'default';
      }
   }
   
	public function __get($key){
		return $this->registry->get($key);
	}
	
   private function _($key){
      return $this->language->data[$key];
   }
   
   public function template(){
      return $this->template;
   }
   
   public function get_file(){
      return $this->file;
   }
   
   public function set_file($file_name){
      if (file_exists(DIR_TEMPLATE . $this->template . $file_name . '.tpl')) {
         $this->file = DIR_TEMPLATE . $this->template . $file_name . '.tpl'; 
      }
      elseif(file_exists(DIR_TEMPLATE . 'default/template/' . $file_name . '.tpl')) {
         $this->file = DIR_TEMPLATE . 'default/template/' . $file_name . '.tpl';
      }
      else{
         if($this->name && $this->controller){
            $this->cache->delete('template'.$this->name);
         }
         trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $this->template . $file_name . '.tpl!');
         exit();
      }
   }
   
	public function set_data($data){
		$this->data = $data;
	}
	
   public function has_table($table){
      return isset($this->tables[$table]);
   }
   
   public function get_table($table){
      if(isset($this->tables[$table])){
         return $this->tables[$table];
      }
      else{
         list(,$caller) = debug_backtrace(false);
         trigger_error("The table $table does not exist in the template $this->name! Called from $caller[class]::$caller[function]().");
         exit();
      }
   }
   
   public function has_form($form){
      return isset($this->forms[$form]);
   }
   
   public function get_form($form){
      if(isset($this->forms[$form])){
         return $this->forms[$form];
      }
      else{
         list(,$caller) = debug_backtrace(false);
         trigger_error("The form $form does not exist in the template $this->name! Called from $caller[class]::$caller[function]().");
         exit();
      }
   }
   
   public function get_block_template($block){
      if(isset($this->blocks[$block])){
         return $this->blocks[$block];
      }
      else{
         return $block . '.tpl';
      }
   }
   
   public function option($option, $default = false){
      if(isset($this->options[$option])){
         return $this->options[$option];
      }
      else{
         return $default;
      }
   }
   
   public function load_template_option($name){
      
      $template = $this->cache->get('template.' . $name);
      
      $to_file = DIR_TEMPLATE_OPTION . $this->theme . '/' . $name . '.to';
      
      if($template){
         if($template['last_modified']){
            //check if the file has been modified since caching
            if(filemtime($to_file) > $template['last_modified']){
               $template = false;
            }
            //check if the current language has been modified since caching
            elseif($this->language->get_latest_modified_file() > $this->cache->get_cache_time('template.' . $name)){
               $template = false;
            }
         }
         //of if a new file has been created for this template
         elseif(file_exists($to_file)){
            $template = false;
         }
      }
      
      if(!$template){
         $_table  = array();
         $_form   = array();
         $_block  = array();
         $_option = array();
         $template_file = $name;
         
         if (file_exists($to_file)) {
            extract($this->data);
            
            require($to_file);
            
            $mtime = filemtime($to_file);
         }
         else{
            $mtime = false;
         }
         
         $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "template WHERE `name` = '" . $this->db->escape($name) . "' LIMIT 1");
         
         if($query->num_rows){
            $tables   = unserialize($query->row['tables']);
            $forms   = unserialize($query->row['forms']);
            $blocks  = unserialize($query->row['blocks']);
            $options = unserialize($query->row['options']);
            
            if(is_array($forms)){
               $_table = $tables + $_table;
            }
            
            if(is_array($forms)){
               $_form   = $forms + $_form;
            }
            
            if(is_array($blocks)){
               $_block  = $blocks + $_block;
            }
            
            if(is_array($options)){
               $_option = $options + $_option;
            }
            
            if($query->row['template_file']){
               $template_file = $query->row['template_file'];
            }
         }
         
         $table_objects = array();
         
         foreach($_table as $table_name => $table){
            foreach($table['columns'] as $column_name => $column){
               if(empty($column['active'])){
                  unset($table['columns'][$column_name]);
               }
            }
            
            $table_objects[$table_name] = new Table($table);
         }
         
         $form_objects = array();
         
         foreach($_form as $form_name => $form){
            foreach($form['fields'] as $field_name => $field){
               if(empty($field['active'])){
                  unset($form['fields'][$field_name]);
               }
            }
            
            $form_objects[$form_name] = new Form($form);
         }
         
         $template = array(
            'tables'        => $table_objects,
            'forms'         => $form_objects,
            'blocks'        => $_block,
            'options'       => $_option,
            'file'          => $template_file,
            'last_modified' => $mtime,
          );
         
         $this->cache->set('template.'.$name, $template);
      }
      
      foreach($template['tables'] as $t){
         $t->initialize($this->controller);
      }
      
      foreach($template['forms'] as $f){
         $f->initialize($this->controller);
      }
      
      $this->tables  = $template['tables'];
      $this->forms   = $template['forms'];
      $this->blocks  = $template['blocks'];
      $this->options = $template['options'];
      
      return $template['file'];
   }
   
   public function load($name, $data = array()){
      if($this->name == $name) return;
      
      $this->name = $name;
      
      $this->data = $data;
      
		if($this->controller){
      	$file_name = $this->load_template_option($name);
		}
		else{
			$file_name = $name;
		}
      
      $this->set_file($file_name);
   }
   
	public function render(){
		if(file_exists($this->file)){
			extract($this->data);
			
			ob_start();
			
			include($this->file);
			
			return ob_get_clean();
		}
		else {
			trigger_error('Error: Could not load template file ' . $this->file . '! ' . get_caller(1));
			exit();
		}
	}
	
	public function fetch($filename) {
		$file = DIR_TEMPLATE . $filename;
    
		if (file_exists($file)) {
			extract($this->data);
			
      	ob_start();
      
	  		include($file);
      
	  		$content = ob_get_contents();

   		ob_end_clean();

      	return $content;
    	} else {
			trigger_error('Error: Could not load template file ' . $file . '!');
			exit();				
    	}	
	}
}