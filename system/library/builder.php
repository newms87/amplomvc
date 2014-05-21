<?php
class Builder extends Library
{
	private $builder_id;
	private $builder_name;
	private $builder_type;

	/**
	 * Use this to give the builder hints on how to builder the desired html structure
	 *
	 * @param $id - the key in the array to use as the value
	 * @param $name - the key in the array to use as the display name
	 * @param (optional) $type - How the $id keys should be treated (eg: int, string, float, etc.). If no type is set, it will try to figure it out on its own
	 */
	public function setConfig($id, $name = null, $type = null)
	{
		if (is_array($id)) {
			$name = $id[1];
			$id   = $id[0];
		}

		$this->builder_id   = $id;
		$this->builder_name = $name;
		$this->builder_type = $type;
	}


	//TODO: This is a hack... Handle this better
	public function finalSale()
	{
		$final_sale_explanation = _l("A Product Marked as <span class='final_sale'></span> cannot be returned. Read our <a href=\"%s\" onclick=\"return colorbox($(this));\">Return Policy</a> for details.", site_url('page/content', 'page_id=' . option('config_shipping_return_page_id')));

		return "<div class='extra_info_block'><span class='final_sale'></span><span class='help_icon'><span class='help_icon_popup'>$final_sale_explanation</span></span></div>";
	}

	public function attrs($data)
	{
		$html = '';

		foreach ($data as $key => $value) {
			if (strpos($key, '#') === 0) {
				$html .= substr($key, 1) . "=\"$value\"";
			}
		}

		return $html;
	}





	//TODO: Should accept an array with parameters. build($type, $params)
	//TODO: Add build to helper/caller.php functions





	function build($type, $data, $name = '', $select = null, $attr_list = array(), $escape_quotes = false)
	{
		//This is for select option groups
		$opt_group_active = false;

		if (!is_array($data)) {
			$this->error_log->write("library/tpl.php::build(): data was not an array. " . gettype($data) . " was given." . get_caller(0, 1));
			return;
		}

		if (!isset($attr_list['class'])) {
			$attr_list['class'] = 'builder-' . $type;
		} else {
			$attr_list['class'] .= ' builder-' . $type;
		}

		//build the attributes
		if (is_array($attr_list)) {
			$attrs = '';
			foreach ($attr_list as $attr => $value) {
				$attrs .= $escape_quotes ? $attr . '=\"' . $value . '\"' : $attr . "=\"" . $value . "\"";
			}
		} else {
			$attrs = $attr_list;
		}

		if (!is_array($select)) {
			$select = array($select);
		}

		$cast_to = function ($value, $type) {
			switch ($type) {
				case 'int':
					return (int)$value;
				case 'float':
					return (float)$value;
				case 'string':
					return (string)$value;
				default:
					trigger_error("Invalid Builder Type: " . $type . ". Valid values are 'int', 'string', 'float', 'none'");
					return null;
			}
		};

		$options          = '';
		$selected_options = ''; //for clickable list

		foreach ($data as $value => $display) {

			if (is_array($display)) {
				if (!isset($this->builder_id) || !isset($this->builder_name) || ($this->builder_id ? !isset($display[$this->builder_id]) : false) || !isset($display[$this->builder_name])) {
					trigger_error(_l("You must set the ID and Name to keys in the \$data Array using \$this->builder->setConfig(\$id,\$name)."));
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
				} else {
					$v = is_integer($s) ? (int)$value : $value;

					if (((is_integer($v) && $s !== '' && !is_bool($s) && !is_null($s)) ? (int)$s : $s) === $v) {
						$selected = true;
						break;
					}
				}
			}

			switch ($type) {
				case 'select':
					$s = $selected ? "selected='true'" : '';
					if (strpos($value, '#optgroup') === 0) {
						if ($opt_group_active) {
							$options .= "</optgroup>";
						}
						$options .= "<optgroup label=\"$display\">";
						$opt_group_active = true;
					} else {
						$options .= "<option value=\"$value\" $s>$display</option>";
					}
					break;

				case 'ac-radio':
				case 'radio':
					$s = $selected ? 'checked="checked"' : '';
					$options .= "<label for=\"radio-$name-$value\" class=\"$type\"><input type=\"radio\" id=\"radio-$name-$value\" name=\"$name\" value=\"$value\" $s /><div class=\"text\">$display</div></label>";
					break;

				case 'checkbox':
					$s = $selected ? 'checked="checked"' : '';
					$options .= "<div class=\"checkbox-button\"><input type=\"checkbox\" id=\"checkbox-$name-$value\" class=\"ac-checkbox\" name=\"{$name}[]\" value=\"$value\" $s /><label for=\"checkbox-$name-$value\">$display</label></div>";
					break;

				case 'multiselect':
					$s = $selected ? 'checked="checked"' : '';
					$options .= "<li><input id=\"checkbox_$name-$value\" type=\"checkbox\" name=\"$name" . "[]\" value=\"$value\" $s /><label for=\"checkbox_$name-$value\">$display</label></li>";
					break;

				case 'clickable_list':
					if ($selected) {
						$selected_options .= "<div onclick=\"clickable_list_remove($(this))\"><span>$display</span><input type=\"hidden\" value=\"$value\" name=\"$value\" /><img src=\"view/theme/default/image/remove.png\" /></div>";
					} else {
						$options .= "<div onclick=\"clickable_list_add($(this), '$value')\"><span>$display</span><img src=\"view/theme/default/image/add.png\" /></div>";
					}
				default:
					break;
			}
		}

		switch ($type) {
			case 'select':
				if ($opt_group_active) {
					$options .= "</optgroup>";
				}
				return "<select name=\"$name\" $attrs>$options</select>";

			case 'radio':
			case 'ac-radio':
			case 'checkbox':
				return "<div $attrs>$options</div>";

			case 'multiselect':
				return "<ul class=\"scrollbox\" $attrs>$options</ul>" .
				"<div class=\"scrollbox_buttons\">" .
				"<a class=\"check_all\" onclick=\"$(this).parent().prev().find('input[type=checkbox]').attr('checked','checked')\">[ Check All ]</a>" .
				"<a class=\"uncheck_all\" onclick=\"$(this).parent().prev().find('input[type=checkbox]').removeAttr('checked')\">[ Uncheck All ]</a>" .
				"</div>";

			case 'clickable_list':
				$added_list = "<div class=\"scrollbox clickable_added\">$selected_options</div>";
				$list       = "<div class=\"scrollbox clickable\">$options</div>";
				return "<div class=\"clickable_list\">$added_list $list</div>";
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
