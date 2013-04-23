<?php
class Builder extends Controller{
   
	protected $registry;
	
   private $builder_id;
   private $builder_name;
   private $builder_type;
	
	private $builder_template;
  
   
   public function __construct(&$registry) {
      $this->registry = &$registry;
   }
   
   public function __get($key){
      return $this->registry->get($key);
   }
   
   public function build_links($links, $depth = 0){
   	switch($depth){
			case 0:
				$class = "top_menu";
				break;
			case 1:
				$class = "sub_menu";
				break;
			default:
				$class = "child_menu";
				break;
		}
		
      $html = "<ul class=\"link_list $class\">";
		
		$zindex = count($links);
		
      foreach($links as $link){
      	
         $attr_list = '';
			if(!empty($link['attrs'])){
	         foreach($link['attrs'] as $key=>$value){
	            $attr_list .= "$key=\"$value\"";
	         }
			}
         
         if(!empty($link['title']) && !isset($link['attrs']['title'])){
            $attr_list .= "title=\"$link[title]\"";
         }
         
         if(empty($link['display'])){
         	$link['display'] = $link['name'];
			}
			
			$children = '';
			$sub_class = '';
			if(!empty($link['children'])){
				$children = $this->build_links($link['children'], $depth+1);
				$sub_class = 'class="has_children"';
			}
			
			if(!empty($link['is_route'])){
				$query = isset($link['query']) ? $link['query'] : '';
				$link['href'] = $this->url->link($link['href'], $query);
			}
			
			$href = '';
			if(!empty($link['href'])){
				$href = "href=\"$link[href]\"";
			}
			
         $html .= "<li $sub_class style=\"z-index:$zindex\"><a $href $attr_list>$link[display]</a>$children</li>";
			
			$zindex--;
      }
      
		$html .= "</ul>";
		
      return $html;
   }
   
   public function display_breadcrumbs(){
      $html = "";
      foreach ($this->breadcrumb->get() as $crumb){
        $html .= $crumb['separator'] . "<a href='$crumb[href]'>$crumb[text]</a>";
      }
      
      return "<div class='breadcrumb'>$html</div>";
   }
   
   public function display_messages($messages){
      $html ='';
      foreach($messages as $type=>$msgs){
         $html .= "<div class='message_box $type'>";
         $html .= "<div class='message_list'>";
         foreach($msgs as $msg){
            if(!empty($msg)){
               $html .= "<div>$msg</div>";
            }
         }
         $html .= "</div>";
         
         if($this->config->get('config_allow_close_message_box')){
            $html .= "<span class='close' onclick=\"$(this).closest('.message_box').remove()\"></span>";
         }
         
         $html .= "</div>";
      }
      return $html;
   }
   
   public function display_errors($errors=false){
      if(!$errors) return '';
      
      return $html = "<div class='message_box warning'>" . $this->display_errors_r($errors) . "</div>";
   }
   
   public function display_errors_r($errors){
      if(!$errors) return '';
      
      if(is_string($errors)){
         return "<div>$errors</div>";
      }
      elseif(is_array($errors)){
         $html = '';
         foreach($errors as $e){
            $html .= $this->display_errors_r($e); 
         }
         return $html;
      }
   }

   public function image_input($name, $image = '', $thumb = null, $no_image = null, $width = null, $height = null, $escape_quotes = false){
      $text_clear = $this->language->get('text_clear');
      $text_browse = $this->language->get('text_browse');
      
		if(!$width){
	      if($thumb){
	         if(is_array($thumb)){
	            $width = (int)$thumb[0];
	            $height = (int)$thumb[1];
	         }
	         elseif(is_string($thumb) && file_exists($thumb)){
	            $size = getimagesize($thumb);
	            $width = $size[0];
	            $height = $size[1];
	         }
	         else{
	            trigger_error("Error in Builder: image_input(): \$thumb must be an existing file or an array of array(\$width, \$height)");
	            return;
	         }
	      }
	      elseif(defined("IS_ADMIN")){
	         $width = $this->config->get('config_image_admin_thumb_width');
	         $height = $this->config->get('config_image_admin_thumb_height');
	      }
	      else{
	         $width = $this->config->get('config_image_thumb_width');
	         $height = $this->config->get('config_image_thumb_height');
	      }
		}
      
      if(!$no_image){
         $no_image = $this->image->resize('no_image.jpg', $width, $height);
      }
      
      if($image){
         $thumb = $this->image->resize($image, $width, $height);
      }
      
      if(!$thumb){
         $thumb = $no_image;
      }
      
      $id = uniqid();
      
		$html = '';
		
		if($this->builder_template == 'click_image'){
			$html .= "<div class='image' onclick=" . ($escape_quotes ? "\\" : '') . "\"el_uploadSingle($(this).children('input').attr('id'),$(this).children('img').attr('id'));" . ($escape_quotes ? "\\" : '') . "\">";
			$html .= 	"<img src='$thumb' alt='' id='thumb-$id' /><br />";
			$html .= 	"<input type='hidden' name='$name' value='$image' id='image-$id' />";
			$html .= 	"<div class='click_image_text'><img src='/admin/view/image/small_plus_icon.gif' /><span>Click to Change<span></div>";
			$html .= "</div>";
		}else{
			$html .= "<div class='image'>";
			$html .= 	"<img src='$thumb' alt='' id='thumb-$id' /><br />";
			$html .= 	"<input type='hidden' name='$name' value='$image' id='image-$id' />";
			$html .= 	"<a onclick=" . ($escape_quotes ? "\\" : '') . "\"el_uploadSingle($(this).parent().find('input').attr('id'),$(this).parent().find('img').attr('id'));" . ($escape_quotes ? "\\" : '') . "\">$text_browse</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
         $html .= 	"<a onclick=" . ($escape_quotes ? "\\" : '') . "\"$(this).parent().find('img').attr('src', '$no_image'); $(this).parent().find('input').attr('value', '');" . ($escape_quotes ? "\\" : '') . "\">$text_clear</a>";
			$html .= "</div>";
		}
		
		return $html;
   }

   
   /**
    * Use this to give the builder hints on how to builder the desired html structure
    * 
    * @param $id - the key in the array to use as the value
    * @param $name - the key in the array to use as the display name
    * @param (optional) $type - How the $id keys should be treated (eg: int, string, float, etc.). If no type is set, it will try to figure it out on its own
    */
   public function set_config($id,$name, $type=null){
      $this->builder_id = $id;
      $this->builder_name = $name;
      $this->builder_type = $type;
   }
	
	public function set_builder_template($template){
		$this->builder_template = $template;
	}
   
   function build_batch_actions($title, $actions, $action_values, $go){
      $html = "<span class='batch_action_title'>$title</span>";
      $html .= $this->build('select',$actions, 'action','',array('id'=>'update_action'));
      foreach($action_values as $for=>$av){
         $form = '';
         $default = isset($av['#default'])?$av['#default']:'';
         $attrs = '';
         if(isset($av['#attrs']))
            foreach($av['#attrs'] as $attr=>$val)
               $attrs .= "$attr='$val' ";
         
         switch($av['#type']){
            case 'text':
               $form = "<input type='text' name='action_value' $attrs value='$default' />";
               break;
            case 'ckedit':
               $form = "<textarea class='ckedit batch_ckedit' id='ba-$for' name='action_value'>$default</textarea>";
               break;
            case 'select':
               $form = "<select name='action_value' $attrs>";
               foreach($av['#values'] as $key=>$val)
                  $form .= "<option value='$key'>$val</option>";
               $form .= "</select>";
            default:
               break;
         }
         $html .= "<div class='action_value' id='for-$for'>$form</div>";
      }
      
      $js = <<<JSC
<script type='text/javascript'>//<!--
$('#update_action').change(function(){
   $('.action_value').removeClass('active');
   $('#for-' + $(this).val()).addClass('active');
});

function do_batch_action(){
   av=$('.action_value.active [name="action_value"]');
   if(av.hasClass('ckedit'))
      av = escape(CKEDITOR.instances[av.attr('id')].getData());
   else	
      av = av.val() || '';
      
   $('#form').attr('action', '$go' + '&action=' + $('#update_action').val() + '&action_value=' + av).submit();
}
//--></script>
JSC;
      return $html . '<a class="button" onclick="do_batch_action()" >Go</a>' . $js;
   }
   
   
   function build($type, $data, $name, $select=null,  $attr_list = array(), $escape_quotes = false){
      //This is for select option groups
      $opt_group_active = false;
      
      
      if(!is_array($data)){
         $this->error_log->write("library/tpl.php::build(): data was not an array. " . gettype($data) . " was given.");
         return;
      }
      
      //build the attributes
      if(is_array($attr_list)){
         $attrs ='';
         foreach($attr_list as $attr => $value){
            $attrs .= $escape_quotes ? $attr.'=\"'.$value.'\"' : $attr . "=\"" . $value ."\"";
         }
      }
      else{
         $attrs = $attr_list;
      }
      
		if(!is_array($select)){
			$select = array($select);
		}
		
		$cast_to = function($value, $type){
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
      
      foreach($data as $value => $display){
         
         if(is_array($display)){
            if(!isset($this->builder_id) || !isset($this->builder_name) || !isset($display[$this->builder_id]) || !isset($display[$this->builder_name])){
               trigger_error("You must set the ID and Name to keys in the \$data Array using \$this->builder->set_config(\$id,\$name)");
               return; 
            }
            
            $value = $display[$this->builder_id];
            $display = $display[$this->builder_name];
         }
         
			
			//Determine if the value is a selected value.
			//If the user specified the type of vars, use that type.,
			//otherwise try to guess the type.
			$selected = false;
			
			foreach($select as $s){
				if(is_array($s)){
					$s = $s[$this->builder_id];
				}
				
				if($this->builder_type){
					$value = $cast_to($value, $this->builder_type);
					
					if($cast_to($s, $this->builder_type) === $value){
						$selected = true;
						break;
					}
				}
	         else{
	            $v = is_integer($s) ? (int)$value : $value;
					
	            if(((is_integer($v) && $s !=='' && !is_bool($s) && !is_null($s))?(int)$s:$s) === $v){
						$selected = true;
						break;
					}
	         }
         }
         
         switch($type){
            case 'select':
               $s = $selected?"selected='true'":'';
               if(strpos($value,'#optgroup') === 0){
                  if($opt_group_active){
                     $options .= "</optgroup>";
                  }
                  $options .= "<optgroup label='$display'>";
                  $opt_group_active = true;
               }
               else{
                  $options .= "<option value='$value' $s>$display</option>";
               }
               break;
               
            case 'radio':
               $s = $selected?'checked="checked"':'';
               $options .= "<span><input type='radio' id='radio-$name-$value' name='$name' value='$value' $s /><label for='radio-$name-$value'>$display</label></span>";
               break;
            
				case 'multiselect':
					$s = $selected?'checked="checked"':'';
					$options .= "<li><input id='checkbox_$name-$value' type='checkbox' name='$name"."[]' value='$value' $s /><label for='checkbox_$name-$value'>$display</label></li>";
					break;
					
            case 'clickable_list':
               if($selected){
                  $selected_options .= "<div onclick='clickable_list_remove($(this))'><span>$display</span><input type='hidden' value='$value' name='$value' /><img src='view/image/remove.png' /></div>";
               }
               else{
                  $options .= "<div onclick='clickable_list_add($(this), '$value')'><span>$display</span><img src='view/image/add.png' /></div>";
               }
            default:
               break;
         }
      }
      
      switch($type){
         case 'select':
            if($opt_group_active){
               $options .= "</optgroup>";
            }
            return "<select name='$name' $attrs>$options</select>";
            
         case 'radio':
            return "<span $attrs>$options</span>";
           
			case 'multiselect':
				return "<ul class='scrollbox' $attrs>$options</ul>" .
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

    function build_custom_select_dropdown($data, $option_name, $default, $select, $id='', $class=''){
         $options = '';
         $selected_value = isset($default)?$default['value']:"";
         $selected_name = isset($default)?$default['display_name']:"";
         
         foreach($data as $value => $display){
            is_array($display)?extract($display):'';
            $display_name = is_array($display)?$display_name:$display;
            $after = isset($after)?$after:"";
            $before = isset($before)?$before:"";
            $item_class = isset($item_class)?$item_class:"";
            
            if($select == $value){
               $selected_value = $value;
               $selected_name = $display_name;
            }
            $options .= "<li onclick='select_menu_item(this)' class='$item_class' data='$value'>" . $before . $display_name . $after . "</li>";
         }
         
         return <<<HTML
         <div id='$id' class='select_dd $class' onclick="toggleDD(this)">
            <div class='select_bar'><div class='current_selection'>$selected_name</div> <div class='dd_separator'></div><div class='dd_down_arrow'></div></div>
            <ul>
               $options
            </ul>
            <input type='hidden' name='$option_name' value='$selected_value'/>
         </div>
HTML;
    }


   public function js($js){
      $args = func_get_args();
      array_shift($args);
            
      ob_start();
      include('builder_js.php');
      $script = ob_get_contents();
      ob_end_clean();
      return $script;
   }
  
}