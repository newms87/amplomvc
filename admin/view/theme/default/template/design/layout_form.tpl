<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'layout.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="languages" class="htabs">
							<? foreach ($languages as $language) { ?>
							<a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
							<? } ?>
				</div>
				<table class="form">
					<tr>
						<td><span class="required"></span> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" />
							<? if ($error_name) { ?>
							<span class="error"><?= $error_name; ?></span>
							<? } ?></td>
					</tr>
				<tr>
								<td><?= $entry_headers; ?></td>
								<? foreach ($languages as $language) {?>
									<td id='language<?= $language['language_id']; ?>'><textarea class='ckedit' name="layout_header[<?= $language['language_id']; ?>][page_header]"><?= isset($layout_header[$language['language_id']]) ? $layout_header[$language['language_id']]['page_header'] : ''; ?></textarea></td>
								<? } ?>
						</tr>
				</table>
				<br />
				<table id="route" class="list">
					<thead>
						<tr>
							<td class="left"><?= $entry_store; ?></td>
							<td class="left"><?= $entry_route; ?></td>
							<td></td>
						</tr>
					</thead>
					<? $route_row = 0; ?>
					<? foreach ($layout_routes as $layout_route) { ?>
					<tbody id="route-row<?= $route_row; ?>">
						<tr>
							<td class="left">
					<? $this->builder->set_config('store_id', 'name');?>
					<?= $this->builder->build('select', $data_stores, "layout_route[$route_row][store_id]", $layout_route['store_id']); ?>
					</td>
							<td class="left"><input type="text" name="layout_route[<?= $route_row; ?>][route]" value="<?= $layout_route['route']; ?>" /></td>
							<td class="left"><a onclick="$('#route-row<?= $route_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
						</tr>
					</tbody>
					<? $route_row++; ?>
					<? } ?>
					<tfoot>
						<tr>
							<td colspan="2"></td>
							<td class="left"><a onclick="addRoute();" class="button"><?= $button_add_route; ?></a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>


<?= $this->builder->js('ckeditor'); ?>
 
<script type="text/javascript"><!--
$('#languages a').tabs();

var route_row = <?= $route_row; ?>;

function addRoute() {
	html	= '<tbody id="route-row' + route_row + '">';
	html += '	<tr>';
	html += '		<td class="left"><select name="layout_route[' + route_row + '][store_id]">';
	<? foreach ($data_stores as $store) { ?>
	html += '<option value="<?= $store['store_id']; ?>"><?= addslashes($store['name']); ?></option>';
	<? } ?>
	html += '		</select></td>';
	html += '		<td class="left"><input type="text" name="layout_route[' + route_row + '][route]" value="" /></td>';
	html += '		<td class="left"><a onclick="$(\'#route-row' + route_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#route > tfoot').before(html);
	
	route_row++;
}
//--></script>
<?= $footer; ?>