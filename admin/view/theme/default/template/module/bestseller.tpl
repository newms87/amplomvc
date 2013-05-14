<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
<div class="box">
  <div class="heading">
    <h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
  </div>
  <div class="content">
    <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
         <tbody>
            <tr>
               <td><?= $entry_show_add_to_cart;?></td>
               <td><?=$this->builder->build('select',$data_yes_no,'bestseller_option[show_add_to_cart]',$options['show_add_to_cart']);?></td>
            </tr>
            <tr>
               <td><?= $entry_choose_product; ?></td>
               <td><input type="text" id='product_chooser' name='filter_name' route='catalog/product/autocomplete' /></td>
            </tr>
            <tr>
               <td><?=$text_product_list;?></td>
               <td>
               <ul id="bestseller_list" class="scrollbox">
               <? foreach ($bestseller_list as $id=>$name) { ?>
                 <li>
                    <div class='designer_name'><?= $this->tool->limit_characters($name,50, '');?></div>
                    <img onclick='$(this).parent().remove();' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
                    <input type="hidden" name='bestseller_list[<?=$id;?>]' value="<?= $id;?>" />
                 </li>
               <? } ?>
               </ul>
            </tr>
         </tbody>
      </table>
      <table id="module" class="list">
        <thead>
          <tr>
            <td class="left"><?= $entry_limit; ?></td>
            <td class="left"><?= $entry_image; ?></td>
            <td class="left"><?= $entry_layout; ?></td>
            <td class="left"><?= $entry_position; ?></td>
            <td class="left"><?= $entry_status; ?></td>
            <td class="right"><?= $entry_sort_order; ?></td>
            <td></td>
          </tr>
        </thead>
        <? $module_row = 0; ?>
        <? foreach ($modules as $module) { ?>
        <tbody id="module-row<?= $module_row; ?>">
          <tr>
            <td class="left"><input type="text" name="bestseller_module[<?= $module_row; ?>][limit]" value="<?= $module['limit']; ?>" size="1" /></td>
            <td class="left">
              <input type="text" name="bestseller_module[<?= $module_row; ?>][image_width]" value="<?= $module['image_width']; ?>" size="3" />
              <input type="text" id='image-<?=$module_row;?>' name="bestseller_module[<?= $module_row; ?>][image_height]" value="<?= $module['image_height']; ?>" size="3" />
            </td>
            <td class="left">
               <? $this->builder->set_config('layout_id','name');?>
               <?=$this->builder->build('select',$layouts, "bestseller_module[$module_row][layout_id]", $module['layout_id']);?>
            </td>
            <td class="left">
               <?=$this->builder->build('select',$data_positions, "bestseller_module[$module_row][position]", $module['position']);?>
            </td>
            <td class="left">
               <?=$this->builder->build('select',$data_statuses, "bestseller_module[$module_row][status]", $module['status']);?>
            </td>
            <td class="right"><input type="text" name="bestseller_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
            <td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
          </tr>
        </tbody>
        <? $module_row++; ?>
        <? } ?>
        <tfoot>
          <tr>
            <td colspan="6"></td>
            <td class="left"><a onclick="addModule();" class="button"><?= $button_add_module; ?></a></td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</div>
<script type="text/javascript"><!--
var module_row = <?= $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><input type="text" name="bestseller_module[' + module_row + '][limit]" value="4" size="1" /></td>';
	html += '    <td class="left"><input type="text" name="bestseller_module[' + module_row + '][image_width]" value="174" size="3" /> <input type="text" name="bestseller_module[' + module_row + '][image_height]" value="138" size="3" /></td>';		
	 <? $this->builder->set_config('layout_id','name');?>
	html += '    <td class="left">' + "<?=$this->builder->build('select',$layouts, "bestseller_module[%modrow%][layout_id]");?>" + '</td>';
	html += '    <td class="left">' + "<?=$this->builder->build('select',$data_positions, "bestseller_module[%modrow%][position]",'content_bottom');?>" + '</td>';
	html += '    <td class="left">' + "<?=$this->builder->build('select',$data_statuses, "bestseller_module[%modrow%][status]",1);?>" + '</td>';
	html += '    <td class="right"><input type="text" name="bestseller_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	
	module_row++;
}
//--></script>

<?= $this->builder->js('autocomplete', '#product_chooser','name','product_id', 'add_to_list');?>

<script type="text/javascript">//<!--

$('#bestseller_list').sortable();

function add_to_list(selector, data){
   $('#product_chooser_warn').remove();
   if($('#bestseller_list input[value="'+data.value+'"]').length > 0){
      $('#product_chooser').after("<span id='product_chooser_warn' class='error'>" + data.label + " is already in the list!");
       return;
   }
   
   html =  '<li>';
   html += '<div class="designer_name">' + data.label + '</div>';
   html += '<img onclick="$(this).parent().remove();" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
   html += '<input type="hidden" name="bestseller_list[' + data.value + ']" value="' + data.value + '" />';
   html += '</li>';
   
   $('#bestseller_list').append(html);
}
//--></script> 
<?=$this->builder->js('errors',$errors);?>
<?= $footer; ?>