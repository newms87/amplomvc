<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td><?= $entry_designer; ?></td>
					<td><?= $this->builder->build('select',$designers, 'designer',null,array('id'=>'designer_list')); ?><a onclick='add_selected_designer();' class='button designer'>Add Designer</a></td>
				</tr>
				<tr>
					<td><?= $entry_choose_product; ?></td>
					<td><input type="text" name="choose_product" value="" /></td>
				</tr>
				<tr>
					<td><?= $text_designer_help; ?></td>
					<td>
						<ul id="featured_list" class="scrollbox">
						<? foreach ($featured_list as $id=>$name) { ?>
							<li>
									<div class='designer_name'><?= $this->tool->limit_characters($name,50, ''); ?></div>
									<img onclick='$(this).parent().remove();' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
									<input type="hidden" name='featured_list[<?= $id; ?>]' value="<?= $name; ?>" />
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
				<? foreach ($modules as $module) { ?>
				<tbody id="module-row<?= $module_row; ?>">
					<tr>
					<td class="left"><input type="text" name="featured_flashsale_module[<?= $module_row; ?>][limit]" value="<?= $module['limit']; ?>" size="1" maxlength='2' /></td>
					<td class="left"><input type="text" name="featured_flashsale_module[<?= $module_row; ?>][size]" value="<?= $module['size']; ?>" size="1" maxlength='3' /></td>
					<td class='left'><?= $this->builder->build('select',$styles, "featured_flashsale_module[$module_row][style]", $module['style']); ?></td>
						<td class='left'><?= $this->builder->build('select',$layouts, "featured_flashsale_module[$module_row][layout_id]", (int)$module['layout_id']); ?></td>
				<td class='left'><?= $this->builder->build('select',$positions, "featured_flashsale_module[$module_row][position]", $module['position']); ?></td>
						<td class="left"><?= $this->builder->build('select',$statuses, "featured_flashsale_module[$module_row][status]", (int)$module['status']); ?></td>
						<td class="right"><input type="text" name="featured_flashsale_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
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
	$('#featured_list').sortable({revert:true});
});
--></script>

<script type="text/javascript">//<!--
designer_list = <?= json_encode($designers); ?>;
function add_selected_designer(){
	id = $('#designer_list').val();
	add_to_list('designer'+id,designer_list[id]);
}

function add_to_list(id,name){
	$('#'+id).remove();
	html =	'<li id="'+id+'">';
	html += '	<div class="designer_name">' + name + '</div>';
	html += '	<img onclick="$(this).parent().remove();" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '	<input type="hidden" name="featured_list['+id+']" value="'+name+'" />';
	html += '</li>';
	
	$('#featured_list').append(html);
}
//--></script>
<script type="text/javascript">//<!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
	html += '		<td class="left"><input type="text" name="featured_flashsale_module[%modrow%][limit]" value="5" size="1" maxlength="2" /></td>';
	html += '		<td class="left"><input type="text" name="featured_flashsale_module[%modrow%][size]" value="260" size="1" maxlength="3" /></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$styles, 'featured_flashsale_module[%modrow%][style]'); ?>" +'</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$layouts,'featured_flashsale_module[%modrow%][layout_id]'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$positions,'featured_flashsale_module[%modrow%][position]','content_top'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$statuses,'featured_flashsale_module[%modrow%][status]',1); ?>" + '</td>';
	html += '		<td class="right"><input type="text" name="featured_flashsale_module[%modrow%][sort_order]" value="0" size="3" /></td>';
	html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	module_row++;
}
//--></script>

<script type="text/javascript">//<!--
$('input[name=\'choose_product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' +	encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {
						response($.map(json, function(item) {
							return {
									label: item.name,
									value: item.product_id
							}
						}));
				}
			});
			
	},
	select: function(event, ui) {
			add_to_list('product'+ui.item.value,ui.item.label);
			return false;
	}
});
//--></script>
<?= $footer; ?>