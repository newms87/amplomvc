<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'layout.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" /></td>
					</tr>
				</table>
				<table id="route" class="list">
					<thead>
						<tr>
							<td class="left"><?= $entry_store; ?></td>
							<td class="left"><?= $entry_route; ?></td>
							<td></td>
						</tr>
					</thead>
					
					<? $route_row = 0; ?>
					<? foreach ($routes as $layout_route) { ?>
					<tbody id="route-row<?= $route_row; ?>">
						<tr>
							<td class="left">
					<? $this->builder->set_config('store_id', 'name');?>
					<?= $this->builder->build('select', $data_stores, "routes[$route_row][store_id]", $layout_route['store_id']); ?>
					</td>
							<td class="left"><input type="text" name="routes[<?= $route_row; ?>][route]" value="<?= $layout_route['route']; ?>" /></td>
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