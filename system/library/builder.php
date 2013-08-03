<?php
class Builder extends Library
{
	private $builder_id;
	private $builder_name;
	private $builder_type;
	
	private $builder_template;
	
	/**
	* Use this to give the builder hints on how to builder the desired html structure
	*
	* @param $id - the key in the array to use as the value
	* @param $name - the key in the array to use as the display name
	* @param (optional) $type - How the $id keys should be treated (eg: int, string, float, etc.). If no type is set, it will try to figure it out on its own
	*/
	public function set_config($id, $name = null, $type=null)
	{
		if (is_array($id)) {
			$name = $id[1];
			$id = $id[0];
		}
		
		$this->builder_id = $id;
		$this->builder_name = $name;
		$this->builder_type = $type;
	}
	
	public function set_builder_template($template)
	{
		$this->builder_template = $template;
	}
	
	public function display_messages($messages)
	{
		$html ='';
		foreach ($messages as $type=>$msgs) {
			$html .= "<div class ='message_box $type'>";
			$html .= "<div class='message_list'>";
			foreach($msgs as $msg) {
				if (!empty($msg)) {
					$html .= "<div>$msg</div>";
				}
			}
			$html .= "</div>";
			
			if ($this->config->get('config_allow_close_message_box')) {
				$html .= "<span class ='close' onclick=\"$(this).closest('.message_box').remove()\"></span>";
			}
			
			$html .= "</div>";
		}
		
		return $html;
	}
	
	public function display_errors($errors = false)
	{
		if(!$errors) return '';
		
		return $html = "<div class ='message_box warning'>" . $this->display_errors_r($errors) . "</div>";
	}
		
	public function display_errors_r($errors)
	{
		if(!$errors) return '';
		
		if (is_string($errors)) {
			return "<div>$errors</div>";
		}
		elseif (is_array($errors)) {
			$html = '';
			foreach ($errors as $e) {
				$html .= $this->display_errors_r($e);
			}
			return $html;
		}
	}
	
	public function batch_action($selector, $actions, $path)
	{
		foreach ($actions as $key => &$action) {
			$action['attrs'] = '';
			
			//All keys beginning with '#' are html tag attributes
			foreach ($action as $attr => $val) {
				if (strpos($attr, '#') === 0) {
					$action['attrs'] .= "$attr='$val' ";
				}
			}
		
			if (!isset($action['default'])) {
				$action['default'] = '';
			}
				
			if (!isset($action['key'])) {
				$action['key'] = $key;
			}
		}
		
		$data = array(
			'selector' => $selector,
			'actions' => $actions,
			'url' => $this->url->link($path, 'action=_action_&action_value=_action_value_&' . $this->url->getQueryExclude('action', 'action_value', 'selected')),
		);
		
		$data += $this->language->data;
		
		//Load Batch Action template
		$template = new Template($this->registry);
		
		$template->load('block/widget/batch_action');
		$template->setData($data);
		
		return $template->render();
	}
	
	//TODO: Probably get rid of this...
	public function finalSale()
	{
		$final_sale_explanation = $this->_('final_sale_explanation',$this->url->link('information/information/info','information_id=' . $this->config->get('config_shipping_return_info_id')));
		
		return "<div class='extra_info_block'><span class='final_sale'></span><span class='help_icon'><span class='help_icon_popup'>$final_sale_explanation</span></span></div>";
	}
	
	public function attrs($data)
	{
		$html = '';
		
		foreach ($data as $key => $value) {
			if (strpos($key,'#') === 0) {
				$html .= substr($key,1) . "=\"$value\"";
			}
		}
		
		return $html;
	}
	
	public function image_input($name, $image = '', $thumb = null, $no_image = null, $width = null, $height = null, $escape_quotes = false)
	{
		$text_clear = $this->language->get('text_clear');
		$text_browse = $this->language->get('text_browse');
		
		if (!$width) {
			if ($thumb) {
				if (is_array($thumb)) {
					$width = (int)$thumb[0];
					$height = (int)$thumb[1];
				}
				elseif (is_string($thumb) && file_exists($thumb)) {
					$size = getimagesize($thumb);
					$width = $size[0];
					$height = $size[1];
				}
				else {
					trigger_error("Error in Builder: image_input(): \$thumb must be an existing file or an array of array(\$width, \$height)");
					return;
				}
			}
			elseif ($this->config->isAdmin()) {
				$width = $this->config->get('config_image_admin_thumb_width');
				$height = $this->config->get('config_image_admin_thumb_height');
			}
			else {
				$width = $this->config->get('config_image_thumb_width');
				$height = $this->config->get('config_image_thumb_height');
			}
		}
	
		if (!$no_image) {
			$no_image = $this->image->resize('no_image.png', $width, $height);
		}
		
		if (!is_file($thumb) && $image) {
			$thumb = $this->image->resize($image, $width, $height);
		}
		
		if (!$thumb) {
			$thumb = $no_image;
		}
		
		$html = '';
		
		switch($this->builder_template){
			case 'browse_clear':
				$html .= "<div class =\"image browse_clear\">";
				$html .= 	"<img src=\"$thumb\" alt=\"\" class=\"iu_thumb\" /><br />";
				$html .= 	"<input type=\"hidden\" name=\"$name\" value=\"$image\" class=\"iu_image\" />";
				$html .= 	"<a onclick=\"upload_image($(this).closest('.image'));\">$text_browse</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
				$html .= 	"<a onclick=\"clear_image($(this).closest('.image'));\">$text_clear</a>";
				$html .= "</div>";
				break;
				
			case 'click_image_small':
				$html .= "<div class=\"image click_image_small\" onclick=\"upload_image($(this));\">";
				$html .= 	"<img src=\"$thumb\" alt=\"\" class=\"iu_thumb\" />";
				$html .= 	"<input type=\"hidden\" name=\"$name\" value=\"$image\" class=\"iu_image\" />";
				$html .= 	"<img src=\"$no_image\" alt=\"\" class=\"click_image_small_change\" />";
				$html .= "</div>";
				break;
			
			case 'click_image':
			default:
				$html .= "<div class=\"image click_image\" onclick=\"upload_image($(this));\">";
				$html .= 	"<img src=\"$thumb\" alt=\"\" class=\"iu_thumb\" /><br />";
				$html .= 	"<input type=\"hidden\" name=\"$name\" value=\"$image\" class=\"iu_image\" />";
				$html .= 	"<div class=\"click_image_text\"><img src=\"" . HTTP_THEME_IMAGE . "small_plus_icon.gif\" /><span>Click to Change<span></div>";
				$html .= "</div>";
				break;
		}
		
		$html .= $this->builder->js('image_manager');
		
		return $html;
	}

	function build($type, $data, $name, $select=null,$attr_list = array(), $escape_quotes = false){
		//This is for select option groups
		$opt_group_active = false;
		
		if (!is_array($data)) {
			$this->error_log->write("library/tpl.php::build(): data was not an array. " . gettype($data) . " was given." . get_caller(0,1));
			return;
		}
		
		//build the attributes
		if (is_array($attr_list)) {
			$attrs ='';
			foreach ($attr_list as $attr => $value) {
				$attrs .= $escape_quotes ? $attr.'=\"'.$value.'\"' : $attr . "=\"" . $value ."\"";
			}
		}
		else {
			$attrs = $attr_list;
		}
		
			if (!is_array($select)) {
				$select = array($select);
			}
			
			$cast_to = function ($value, $type)
			{
				switch($type){
					case 'int':
						return (int)$value;
					case 'float':
						return (float)$value;
					case 'string':
						return (string)$value;
					default:
						trigger_error("Invalid Builder Type: " . $type. ". Valid values are 'int', 'string', 'float', 'none'");
						return null;
				}
			};
			
		$options='';
		$selected_options = ''; //for clickable list
		
		foreach ($data as $value => $display) {
			
			if (is_array($display)) {
				if (!isset($this->builder_id) || !isset($this->builder_name) || ($this->builder_id ? !isset($display[$this->builder_id]) : false) || !isset($display[$this->builder_name])) {
					trigger_error("You must set the ID and Name to keys in the \$data Array using \$this->builder->set_config(\$id,\$name). " . get_caller());
					return;
				}
				
				if ($this->builder_id) {
					$value = $display[$this->builder_id];
				}
				
				$display = $display[$this->builder_name];
			}
			
				
				//Determine if the value is a selected value.
				//If the user specified the type of vars, use that type.,
				//otherwise try to guess the type.
				$selected = false;
				
			foreach ($select as $s) {
				if (is_array($s)) {
					$s = $s[$this->builder_id];
				}
				
				if ($this->builder_type) {
					$value = $cast_to($value, $this->builder_type);
					
					if ($cast_to($s, $this->builder_type) === $value) {
						$selected = true;
						break;
					}
				}
				else {
					$v = is_integer($s) ? (int)$value : $value;
						
					if (((is_integer($v) && $s !=='' && !is_bool($s) && !is_null($s))?(int)$s:$s) === $v) {
							$selected = true;
							break;
						}
				}
			}
			
			switch($type){
				case 'select':
					$s = $selected?"selected='true'":'';
					if (strpos($value,'#optgroup') === 0) {
					if ($opt_group_active) {
						$options .= "</optgroup>";
					}
					$options .= "<optgroup label='$display'>";
					$opt_group_active = true;
					}
					else {
					$options .= "<option value='$value' $s>$display</option>";
					}
					break;
					
				case 'radio':
					$s = $selected?'checked="checked"':'';
					$options .= "<span class ='radio_button'><input type='radio' id='radio-$name-$value' name='$name' value='$value' $s /><label for='radio-$name-$value'>$display</label></span>";
					break;
				
				case 'multiselect':
					$s = $selected?'checked="checked"':'';
					$options .= "<li><input id='checkbox_$name-$value' type='checkbox' name='$name"."[]' value='$value' $s /><label for='checkbox_$name-$value'>$display</label></li>";
					break;
						
				case 'clickable_list':
					if ($selected) {
					$selected_options .= "<div onclick='clickable_list_remove($(this))'><span>$display</span><input type='hidden' value='$value' name='$value' /><img src='view/theme/default/image/remove.png' /></div>";
					}
					else {
					$options .= "<div onclick='clickable_list_add($(this), '$value')'><span>$display</span><img src='view/theme/default/image/add.png' /></div>";
					}
				default:
					break;
			}
		}
		
		switch($type){
			case 'select':
				if ($opt_group_active) {
					$options .= "</optgroup>";
				}
				return "<select name='$name' $attrs>$options</select>";
				
			case 'radio':
				return "<span $attrs>$options</span>";
				
				case 'multiselect':
					return "<ul class ='scrollbox' $attrs>$options</ul>" .
							"<div class='scrollbox_buttons'>".
								"<a class='check_all' onclick=\"$(this).parent().prev().find('input[type=checkbox]').attr('checked','checked')\">[ Check All ]</a>".
								"<a class='uncheck_all' onclick=\"$(this).parent().prev().find('input[type=checkbox]').removeAttr('checked')\">[ Uncheck All ]</a>".
							"</div>";
					
			case 'clickable_list':
				$added_list = "<div class='scrollbox clickable_added'>$selected_options</div>";
				$list = "<div class='scrollbox clickable'>$options</div>";
				return "<div class='clickable_list'>$added_list $list</div>";
		}
	}
	
	public function js($js)
	{
		static $js_loaded_files = array();
		
		$args = func_get_args();
		array_shift($args);
			
		ob_start();
			
		include('builder_js.php');
	
		return ob_get_clean();
	}
}