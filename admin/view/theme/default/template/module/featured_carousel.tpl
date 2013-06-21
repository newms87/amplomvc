<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form" style="border-bottom: black 2px solid">
				<tr>
					<td>
						<div><?= $entry_featured_items; ?></div><br />
						<div>
								<input type="text" class="autocomplete" for='featured_product_list' filter='filter_name' route='catalog/product/autocomplete'/><br />
								<?= $text_autocomplete; ?>
						</div>
					</td>
					<td>
						<ul id="featured_product_list" class="item_list">
						<? foreach ($featured_product_list as $id => $item) { ?>
							<li item_id='<?= $id; ?>'>
									<div class='item_image'>
										<?= $this->builder->image_input("featured_product_list[$id][image]", $item['image']); ?>
									</div>
									<div class='item_name'>
										<input type='text' size="50" value='<?= $item['name']; ?>' name='featured_product_list[<?= $id; ?>][name]' />
									</div>
									<img class='remove' onclick='$(this).parent().remove();' width='30px' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
						<? } ?>
						</ul>
				</tr>
			</table>
			<table class="form">
				<tr>
					<td>
						<div><?= $entry_carousel_blocks; ?></div><br />
						<div>
								<input type="text" class="autocomplete" for='featured_carousel_list' filter='filter_name' route='catalog/product/autocomplete'/><br />
								<?= $text_autocomplete; ?>
						</div>
					</td>
					<td>
						<ul id="featured_carousel_list" class="item_list">
						<? foreach ($featured_carousel_list as $id => $item) { ?>
							<li item_id='<?= $id; ?>'>
									<div class='item_image'>
										<?= $this->builder->image_input("featured_carousel_list[$id][image]", $item['image']); ?>
									</div>
									<div class='item_name'><input type='text' size="50" value='<?= $item['name']; ?>' name='featured_carousel_list[<?= $id; ?>][name]' /></div>
									<img class='remove' onclick='$(this).parent().remove();' width='30px' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
						<? } ?>
						</ul>
				</tr>
			</table>
			<table id="module" class="list">
				<thead>
					<tr>
				<td class="left"><?= $entry_limit; ?></td>
				<td class="left"><?= $entry_size; ?></td>
				<td class="left"><?= $entry_style; ?></td>
						<td class="left"><?= $entry_layout; ?></td>
						<td class="left"><?= $entry_position; ?></td>
						<td class="left"><?= $entry_status; ?></td>
						<td class="right"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
				</thead>
				<? $module_row = 0; ?>
				<? foreach ($featured_carousel_module as $module) { ?>
				<tbody id="module-row<?= $module_row; ?>">
					<tr>
					<td class="left"><input type="text" name="featured_carousel_module[<?= $module_row; ?>][limit]" value="<?= $module['limit']; ?>" size="1" maxlength='2' /></td>
					<td class="left"><input type="text" name="featured_carousel_module[<?= $module_row; ?>][size]" value="<?= $module['size']; ?>" size="1" maxlength='3' /></td>
					<td class='left'><?= $this->builder->build('select',$data_styles, "featured_carousel_module[$module_row][style]", $module['style']); ?></td>
							<? $this->builder->set_config('layout_id','name');?>
						<td class='left'><?= $this->builder->build('select',$data_layouts, "featured_carousel_module[$module_row][layout_id]", (int)$module['layout_id']); ?></td>
				<td class='left'><?= $this->builder->build('select',$data_positions, "featured_carousel_module[$module_row][position]", $module['position']); ?></td>
						<td class="left"><?= $this->builder->build('select',$data_statuses, "featured_carousel_module[$module_row][status]", (int)$module['status']); ?></td>
						<td class="right"><input type="text" name="featured_carousel_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
						<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
					</tr>
				</tbody>
				<? $module_row++; ?>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="8"></td>
						<td class="left"><a onclick="addModule();" class="button"><?= $button_add_module; ?></a></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">//<!--
$(document).ready(function(){
	$('#featured_carousel_list, #featured_product_list').sortable({revert:true});
});
--></script>

<script type="text/javascript">//<!--
designer_list = <?= json_encode($data_designers); ?>;
function add_selected_designer(){
	id = $('#designer_list').val();
	item = { label: designer_list[id], value: 'designer'+id };
		
	add_to_list({item: item});
}

function add_to_list(selector, data){
	selector.find('.error').remove();
	
	list = $('#' + selector.attr('for'));
	
	if(list.find('li[item_id=' + data.value + ']').length > 0){
			selector.after("<span class='error'>" + data.label + " is already in the list!</span>");
			return;
	}
	
	html =	'<li item_id="%row_id%">';
	html += '	<div class="item_image">'
	html += "			<?= $this->builder->image_input("%id%[%row_id%][image]", '%image%', null, null, null, null, true); ?>";
	html += '	</div>';
	html += '	<div class="item_name"><input type="text" size="50" value="' + data.label + '" name="%id%[%row_id%][name]" /></div>';
	html += '	<img class="remove" onclick="$(this).parent().remove();" width="30px" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%id%/g, list.attr('id'))
			.replace(/%row_id%/g,data.value)
			.replace(/%image%/g,data.image);
			
	list.append(html);
}
//--></script>

<script type="text/javascript">//<!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
	html += '		<td class="left"><input type="text" name="featured_carousel_module[%modrow%][limit]" value="5" size="1" maxlength="2" /></td>';
	html += '		<td class="left"><input type="text" name="featured_carousel_module[%modrow%][size]" value="260" size="1" maxlength="3" /></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$data_styles, 'featured_carousel_module[%modrow%][style]'); ?>" +'</td>';
					<? $this->builder->set_config('layout_id','name');?>
	html += '		<td class="left">' + "<?= $this->builder->build('select',$data_layouts,'featured_carousel_module[%modrow%][layout_id]'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$data_positions,'featured_carousel_module[%modrow%][position]','content_top'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$data_statuses,'featured_carousel_module[%modrow%][status]',1); ?>" + '</td>';
	html += '		<td class="right"><input type="text" name="featured_carousel_module[%modrow%][sort_order]" value="0" size="3" /></td>';
	html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	module_row++;
}
//--></script>

<?= $this->builder->js('autocomplete', '.autocomplete','name','product_id', 'add_to_list'); ?>

<?= $footer; ?>