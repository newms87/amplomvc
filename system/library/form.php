<?php
class Form {
   private $fields = array();
   private $file;
   private $path;
   private $builder;
   private $data;
   private $request;
   private $language;
   private $validation;
   private $error = array();
   
   private $use_form_tag = false;
   private $action;
   private $method;
   private $form_tag_attrs;
   
   private $form_data = null;
   private $default_attrs = null;
   
   function __construct($form){
      $this->fields = $form['fields'];
      
      if(isset($form['action'])){
         $method = isset($form['method']) ? $form['method'] : 'POST';
         $attrs  = isset($form['attrs']) ? $form['attrs'] : array();
         
         $this->use_form_tag($form['action'], $method, $attrs);
      }
      
      if(!empty($form['data'])){
         $this->form_data = $form['data'];
      }
   }
   
   function initialize($controller){
      $this->path       = $controller->template->template();
      $this->builder    = $controller->builder;
      $this->data       = $controller->data;
      $this->request    = $controller->request;
      $this->language   = $controller->language;
      $this->validation = $controller->validation;
   }
   
   public function get_errors(){
      return $this->error;
   }
   
   public function get_fields(){
      return $this->fields;
   }
   
   public function get_field_value($field, $key){
      if(isset($this->fields[$field][$key])){
         return $this->fields[$field][$key];
      }
      
      return null;
   }
   
   public function set_field_value($field, $key, $value){
      if(isset($this->fields[$field])){
         $this->fields[$field][$key] = $value;
      }
   }
   
   public function use_form_tag($action, $method = 'POST', $attrs = array()){
      if($action === false || $action === null){
         $this->use_form_tag = false;
      }
      else{
         $this->use_form_tag = true;
         $this->action = $action;
         $this->method = $method;
         
         $this->form_tag_attrs = '';
         foreach($attrs as $attr => $value){
            $this->form_tag_attrs .= $attr . '="' . $value . '"';
         }
      }
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
         list(,$caller) = debug_backtrace(false);
         trigger_error("Error: Could not load form template " . DIR_TEMPLATE . $this->path . $file . "! Called from $caller[class]::$caller[function]().");
         exit();
      }
   }
   
   public function set_fields($fields){
      $this->fields = $fields;
   }
   
   public function set_form_data($data){
      $this->form_data = $data;
   }
   
   public function set_default_attrs($attrs){
      $this->default_attrs = $attrs;
   }
   
   public function fill_data_from(){
      $args = func_get_args();
      
      foreach($this->fields as $key => &$field){
         foreach($args as $arg_num => $arg){
               
            if(isset($field['select'])){
               break;
            }
            
            if(is_string($arg)){
               switch($arg){
                  case 'POST':
                     if(isset($_POST[$key])){
                        $field['select'] = $_POST[$key];
                     }
                     break;
                  case 'SESSION':
                     if(isset($this->request->session[$key])){
                        $field['select'] = $this->request->session;
                     }
                     break;
                  case 'GET':
                     if(isset($_GET[$key])){
                        $field['select'] = $_GET[$key];
                     }
                     break;
                  case 'DEFAULT':
                     if(isset($field['default'])){
                        $field['select'] = $field['default'];
                     }
                     break;
                  default:
                     $field['select'] = $arg;
                     break;
               }
            }
            elseif(is_array($arg)){
               if(isset($arg[$key])){
                  $field['select'] = $arg[$key];
               }
            }
            elseif(!is_null($arg)){
               $field['select'] = $arg;
            }
         }
      }
   }
   
   public function build(){
      $this->prepare();
      
      //make the fields accessible from the template file
      $fields = $this->fields;
      
      //Make the Form element vars accessible
      $use_form_tag = $this->use_form_tag;
      $action = $this->action;
      $method = $this->method;
      $form_tag_attrs = $this->form_tag_attrs;
      
      //add any additional data for the form template
      $this->form_data ? extract($this->form_data) : '';
      
      //render the file
      ob_start();
      
      require($this->file);
      
      $output = ob_get_contents();
         
      ob_end_clean();
      
      return $output;
   }
   
   private function prepare(){
      if(!$this->file || !file_exists($this->file)){
         list(,,$caller) = debug_backtrace(false);
         trigger_error("You must set the template for the form before building! Called from $caller[class]::$caller[function]().");
         exit();
      }
      
      foreach($this->fields as $name => &$field){
         if(!isset($field['type'])){
            list(,,$caller) = debug_backtrace(false);
            trigger_error("Invalid form field! The type was not set for $name! Called from $caller[class]::$caller[function]().");
            exit();
         }
         
         if(!isset($field['name'])){
            $field['name'] = $name;
         }
         
         if(!isset($field['required'])){
            $field['required'] = false; 
         }
         
         if(!isset($field['display_name'])){
            $field['display_name'] = $this->language->get('entry_' . $name);
         }
         
         if(!isset($field['attrs'])){
            $field['attrs'] = array();
             
            if(empty($field['attrs']) && $this->default_attrs){
               $field['attrs'] = $this->default_attrs;
            }
         }
         
         //additional / overridden attributes
         foreach($field as $attr => $value){
            if(strpos($attr, '#') === 0){
               $field['attrs'][substr($attr,1)] = $value;
            }
         }
         
         $field['html_attrs'] = '';
         
         foreach($field['attrs'] as $attr => $value){
            $field['html_attrs'] .= $attr . '="' . $value . '" ';
         }
         
         
         if(!isset($field['select'])){
            if(isset($this->data[$name])){
               $field['select'] = $this->data[$name];
            }
            elseif(isset($field['default'])){
               $field['select'] = $field['default'];
            }
            else{
               $field['select'] = '';
            }
         }
         
         switch($field['type']){
            case 'text':
               break;
            
            case 'radio':
            case 'select':
               if(!isset($field['values']) || !is_array($field['values'])){
                  $field['values'] = array();
               }
               
               $first_field = current($field['values']);
               
               //attempt to determine field values primary key and display name
               if(!empty($field['values']) && is_array($first_field)){
                     
                  if(!isset($field['builder_name']) && isset($first_field['name'])){
                     $field['builder_name'] = 'name';
                  }

                  foreach(array_keys($first_field) as $key){
                     if(!isset($field['builder_id'])){
                        if(strpos($key, '_id')){
                           $field['builder_id'] = $key;
                        }
                     }
                     if(!isset($field['builder_name'])){
                        if(strpos($key, 'name') !== false){
                           $field['builder_name'] = $key;
                        }
                     }
                  }
                  
                  if(!isset($field['builder_name']) || !isset($field['builder_id'])){
                     list(,,$caller) = debug_backtrace(false);
                     trigger_error("Invalid Form Field! The builder ID and Name pair could not be determined for $name! Called from $caller[class]::$caller[function]().");
                     exit();
                  }
                  
                  if(isset($field['empty_option']) && $field['empty_option']){
                     $field['values'] = array('' => $field['empty_option']) + $field['values'];
                  }
               }
               else{
                  $field['builder_name'] = $field['builder_id'] = '';
               }
               
               unset($first_field);
               break;
            
            case 'checkbox':
               break;
               
            default: 
               break;
         }
      }
   }

   public function validate($data){
      foreach($this->fields as $field_name => $field){
         if(!isset($field['validate'])){
            if((!isset($field['validation']) || !$field['validation']) && !isset($field['required']) || !$field['required']){
               continue;
            }
         }
         elseif(!$field['validate']){
            continue;
         }
         
         if(isset($data[$field_name])){
            if(!isset($field['validation'])){
               $field['validation'] = 'not_empty';
            }
            
            if(is_callable($field['validation'])){
               $field['validation']();
               continue;
            }
            elseif(is_array($field['validation'])){
               $method = array_shift($field['validation']);
               
               $args = array('#value'=>$data[$field_name]) + $field['validation'];
               
               if(is_callable($method)){
                  $method($args);
               }
            }
            else{
               $method = $field['validation'];
               $args = array('#value' => $data[$field_name]);
            }
            
            if(!call_user_func_array(array($this->validation, $method), $args)){
               $this->error[$field_name] = $this->validation->fetch_error();
            }
         }
      }
      
      return $this->error ? false : true;
   }
}