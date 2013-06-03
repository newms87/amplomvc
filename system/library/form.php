<?php
class Form 
{
	private $registry;
	
	private $name;
	private $show_tag;
	private $action;
	private $method;
	private $encryption;
	private $name_format = '';
	
	private $fields = array();
	private $disabled_fields = array();
	
	private $template_file;
	private $data = array();
	
	private $error;
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function init($form)
	{
		$this->name = $form;
		$this->show_tag = true;
		$this->data = array();
		$this->action = '';
		$this->method = 'post';
		$this->encryption = false;
		$this->name_format = '';
		$this->template_file = 'default';
		
		$file = DIR_FORM . $form . '.php';
		
		if (!is_file($file)) {
			$this->fields = array();
		
			trigger_error("Could not find form $form!");
		}
		else {
			$_ = array();
			
			include($file);
			
			$this->fields = array();
			
			//TODO: Sort Order cannot be set before runtime.. is this a limitation?
			$sort_order = 0;
			
			foreach ($_ as $key => $field) {
				$this->fields[$key] = $field;
				$this->fields[$key]['sort_order'] = $sort_order++;
			}
		}
	}
	
	public function get_errors()
	{
		if ($this->name_format) {
			return $this->tool->name_format($this->name_format, $this->error);
		}
		
		return $this->error;
	}
	
	public function show_form_tag($show = true)
	{
		$this->show_tag = $show;
	}
	
	public function set_data($data)
	{
		$this->data = $data;
	}
	
	public function set_action($action)
	{
		$this->action = $action;
	}
	
	public function set_method($method)
	{
		$this->method = $method;
	}
	
	public function set_encryption($encryption)
	{
		$this->encryption = $encryption;
	}
	
	public function set_name_format($name_format)
	{
		$this->name_format = $name_format;
	}
	
	public function set_field($field, $data)
	{
		$this->fields[$field] = $data;
	}
	
	public function set_field_options($field, $options, $config = null)
	{
		if (isset($this->fields[$field])) {
			$this->fields[$field]['options'] = $options;
			$this->fields[$field]['build_config'] = $config;
		}
		else {
			trigger_error("Attempt to set options for unknown field $field! " . get_caller());
		}
	}
	
	public function get_fields()
	{
		return $this->fields;
	}
	
	public function get_field_value($field, $key)
	{
		if (isset($this->fields[$field][$key])) {
			return $this->fields[$field][$key];
		}
		
		return null;
	}
	
	public function set_field_value($field, $key, $value)
	{
		if (isset($this->fields[$field])) {
			$this->fields[$field][$key] = $value;
		}
	}
	
	public function add_field($field)
	{
		$this->fields[] = $field;
	}
	
	public function add_fields($fields)
	{
		foreach ($fields as $field) {
			$this->add_field($field);
		}
	}
	
	public function set_fields()
	{
		$args = func_get_args();
		
		if(empty($args)) return;
		
		if (is_array($args[0])) {
			$args = $args[0];
		}
		
		//reset the field list
		if (!empty($this->disabled_fields)) {
			$this->fields += $this->disabled_fields;
		}
		
		//filter out the fields that are not in the requested list
		foreach ($this->fields as $key => $field) {
			if (!in_array($key, $args)) {
				$this->disabled_fields[$key] = $field;
				unset($this->fields[$key]);
			}
		}
	}
	
	public function enable_fields()
	{
		$args = func_get_args();
		
		if(empty($args)) return;
		
		if (is_array($args[0])) {
			$args = $args[0];
		}
		
		foreach ($args as $field) {
			if (isset($this->disabled_fields[$field])) {
				$this->fields[$field] = $this->disabled_fields[$field];
				unset($this->disabled_fields[$field]);
			}
		}
	}
	
	public function disable_fields()
	{
		$args = func_get_args();
		
		if(empty($args)) return;
		
		if (is_array($args[0])) {
			$args = $args[0];
		}
		
		foreach ($args as $field) {
			if (isset($this->fields[$field])) {
				$this->disabled_fields[$field] = $this->fields[$field];
				unset($this->fields[$field]);
			}
		}
	}
	
	public function set_template($file)
	{
		$this->template_file = $this->template->find_file($file);
		
		if (!$this->template_file) {
			$this->error = "Could not load form template $file!" . get_caller();
			trigger_error($this->error);
		}
	}
	
	public function build()
	{
		//Prep the form data
		if (!$this->prepare()) {
			trigger_error($this->error . ' ' . get_caller());
			return false;
		}
		
		//Make the data accessible to the form generator
		$form_id	= 'form_' . uniqid();
		$form_name	= 'form_' . $this->name;
		$show_tag = $this->show_tag;
		$action = $this->action;
		$method = $this->method;
		$fields = $this->fields;
		
		//render the file
		ob_start();
		
		require($this->template_file);
		
		return ob_get_clean();
	}
	
	private function prepare()
	{
		if (!$this->template_file || !is_file($this->template_file)) {
			$this->error = "You must set the template for the form before building!";
			return false;
		}
		
		foreach ($this->fields as $name => &$field) {
			if (!isset($field['type'])) {
				$this->error = "Invalid form field! The type was not set for $name!";
				return false;
			}
			
			if ($this->name_format) {
				$field['name'] = preg_replace("/%name%/", $name, $this->name_format);
			}
			elseif (!isset($field['name'])) {
				$field['name'] = $name;
			}
			
			if (!isset($field['required'])) {
				$field['required'] = false;
			}
			
			if (!isset($field['display_name'])) {
				$display_name = $this->language->get('entry_' . $name);
				
				if (($display_name == 'entry_' . $name) && isset($field['label'])) {
					$field['display_name'] = $field['label'];
				}
				else {
					$field['display_name'] = $display_name;
				}
			}
			
			if (!isset($field['attrs'])) {
				$field['attrs'] = array();
			}
			
			//additional / overridden attributes
			foreach ($field as $attr => $value) {
				if (strpos($attr, '#') === 0) {
					$field['attrs'][substr($attr,1)] = $value;
				}
			}
			
			$field['html_attrs'] = '';
			
			foreach ($field['attrs'] as $attr => $value) {
				$field['html_attrs'] .= $attr . '="' . $value . '" ';
			}
			
			
			if (!isset($field['value'])) {
				if (isset($this->data[$name])) {
					$field['value'] = $this->data[$name];
				}
				elseif (isset($field['default_value'])) {
					$field['value'] = $field['default_value'];
				}
				else {
					$field['value'] = '';
				}
			}
			
			switch($field['type']){
				case 'text':
					break;
				
				case 'radio':
				case 'select':
					if (empty($field['options'])) {
						$field['options'] = array();
						
						if (!isset($field['build_config'])) {
							$field['build_config'] = array('' => '');
						}
						
						continue;
					}
					else {
						$first_option = current($field['options']);
						
						if (is_array($first_option)) {
							$key = key($field['build_config']);
							$value = current($field['build_config']);
							
							if (!isset($first_option[$key]) || !isset($first_option[$value])) {
								$this->error = "You must specify the build config options for $name!";
								return false;
							}
						}
					}
					break;
				
				case 'checkbox':
					break;
					
				default:
					break;
			}
		}
		
		uasort($this->fields, function ($a,$b)
 { return $a['sort_order'] > $b['sort_order']; });
		
		return true;
	}

	public function validate($data)
	{
		$this->language->system('form');
		
		//For Each Field Validate the data, set $this->error if invalid
		foreach ($this->fields as $field_name => $field) {
			$field_display_name = $this->language->get('entry_' . $field_name, isset($field['label']) ? $field['label'] : $field_name);
			
			//Check if this field is set and if it is required
			if (!empty($field['required']) && (!isset($data[$field_name]) || is_null($data[$field_name]) || $data[$field_name] === '')) {
				$this->error[$field_name] = $this->language->get('error_required_' . $field_name, $this->language->format('error_required', $field_display_name));
				continue;
			}
			
			//Not required and no value given
			if (empty($field['required']) && empty($data[$field_name])) {
				continue;
			}
			
			//If not required and not set or does not require auto validation, Skip!
			if (!isset($data[$field_name]) || empty($field['validation'])) {
				continue;
			}
			
			//If a custom validation function is provided, call that instead
			if (is_callable($field['validation'])) {
				$field['validation']($data[$field_name]);
				continue;
			}
			
			//Call the validation function from the library
			$args = array('#value' => $data[$field_name]);
			
			if (is_array($field['validation'])) {
				$method = array_shift($field['validation']);
				
				$args += $field['validation'];
			}
			else {
				$method = $field['validation'];
			}
			
			if (!call_user_func_array(array($this->validation, $method), $args)) {
				$this->error[$field_name] = $this->language->get('error_' . $field_name, $this->language->format('error_invalid_field', $field_display_name));
			}
		}
		
		return $this->error ? false : true;
	}
}