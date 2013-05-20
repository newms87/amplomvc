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
	
   private $template;
   
	private $template_data;
	
   function __construct(&$registry){
   	$this->registry = $registry;
   }
   
	public function __get($key){
		return $this->registry->get($key);
	}
   
	public function load_template_data(){
		//TODO: Need to actually load template data here... This may be the data_statuses, data_yes_no, etc...
	}
	
	public function get_template_data(){
		return $this->template_data;
	}
	
   public function template(){
      return $this->template;
   }
   
   public function get_file(){
      return $this->file;
   }
   
	public function set_file($file_name){
		$file = $this->theme->find_file($file_name . '.tpl');
		
		if($file){
			$this->file = $file;
		}
		else{
         if($this->name){
            $this->cache->delete('template' . $this->name);
         }
			
         trigger_error('Error: Could not load template ' . $file_name . '.tpl!');
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
   
	public function load($name, $data = array()){
      if($this->name == $name) return;
      
      $this->name = $name;
      
      $this->data = $data;
   
	   $this->set_file($this->name);
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
		$file = DIR_THEME . $filename;
    
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
	
	public function find_file($file){
		$file = preg_replace("/\\.tpl\$/", '', $file) . '.tpl';
		
		return $this->theme->find_file($file);
	}
}