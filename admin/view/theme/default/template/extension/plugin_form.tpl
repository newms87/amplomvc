<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<h2><?= $name; ?></h2>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table id="module" class="list">
				<thead>
					<tr>
						<td class="left"><?= $entry_function; ?></td>
						<td class="left"><?= $entry_plugin_path; ?></td>
						<td class="left"><?= $entry_route; ?></td>
						<td class="left"><?= $entry_base_type; ?></td>
						<td class="left"><?= $entry_class_path; ?></td>
						<td class="left"><?= $entry_type; ?></td>
						<td class="left"><?= $entry_hooks; ?></td>
						<td class="left"><?= $entry_status; ?></td>
						<td></td>
					</tr>
				</thead>
				<? $row = 0; ?>
				<? foreach ($plugin_data as $data) { ?>
				<tbody id="plug-row<?= $row; ?>" row='<?= $row; ?>'>
					<tr>
						<td class="left"><input type="text" name="plugin_data[<?= $row; ?>][on_render]" value="<?= $data['on_render']; ?>" size="30" /></td>
						<td class="left"><input type="text" name="plugin_data[<?= $row; ?>][plugin_path]" value="<?= $data['plugin_path']; ?>" size="30" /></td>
						<td class="left"><input type="text" name="plugin_data[<?= $row; ?>][route]" value="<?= $data['route']; ?>" size="30" /></td>
						<td class="left">
							<?= $this->builder->build('select',$base_types, "plugin_data[$row][base_type]", $data['base_type']); ?>
						</td>
						<td class="left"><input type="text" name="plugin_data[<?= $row; ?>][class_path]" value="<?= $data['class_path']; ?>" size="30" /></td>
						<td class="left">
							<?= $this->builder->build('select',$types, "plugin_data[$row][type]", $data['type']); ?>
						</td>
						<td class="left">
							<? if(isset($data['hooks']) && $data['hooks']){?>
							<? foreach($data['hooks'] as $key=>$hook){?>
							<div class='hook_input'>
									<label for='hook-method-<?= $key; ?>'><?= $entry_hook_method; ?></label><input id='hook-method-<?= $key; ?>' type="text" name="plugin_data[<?= $row; ?>][hooks][<?= $key; ?>][callback]" value="<?= $hook['callback']; ?>" size="25" />
									<?= $this->builder->build('select',$call_when,"plugin_data[$row][hooks][$key][when]", $hook['when']); ?><br />
									<label for='hook-for-<?= $key; ?>'><?= $entry_hook_for; ?></label><input id='hook-for-<?= $key; ?>' type="text" name="plugin_data[<?= $row; ?>][hooks][<?= $key; ?>][for]" value="<?= $key; ?>" size="25" />
									<a onclick="$(this).parent().remove()"><?= $button_hook_remove; ?></a>
							</div>
							<? }?>
							<? }?>
							<a class='remove_hook' onclick='addHook(this);'><?= $button_add_hook; ?></a>
						</td>
						<td class="left"><?= $this->builder->build('select',$statuses, "plugin_data[$row][status]", (int)$data['status']); ?></td>
						<td class="left"><a onclick="$('#plug-row<?= $row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
					</tr>
				</tbody>
				<? $row++; ?>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="8"></td>
						<td class="left"><a onclick="addPlug();" class="button"><?= $button_add_plug; ?></a></td>
					</tr>
				</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>

<script type="text/javascript">//<!--
var plug_row = <?= $row; ?>;

function addPlug() {
	html	= '<tbody id="plug-row%plug_row%" row="%plug_row%">';
	html += '	<tr>';
	html += '		<td class="left"><input type="text" name="plugin_data[%plug_row%][function]" value="" size="30" /></td>';
	html += '		<td class="left"><input type="text" name="plugin_data[%plug_row%][plugin_path]" value="" size="30" /></td>';
	html += '		<td class="left"><input type="text" name="plugin_data[%plug_row%][route]" value="" size="30" /></td>';
	html += '		<td class="left">'+"<?= $this->builder->build('select',$base_types, "plugin_data[%plug_row%][base_type]"); ?>" + '</td>';
	html += '		<td class="left"><input type="text" name="plugin_data[%plug_row%][class_path]" value="" size="30" /></td>';
	html += '		<td class="left">'+"<?= $this->builder->build('select',$types, "plugin_data[%plug_row%][type]"); ?>" + '</td>';
	html += '		<td class="left"><a class="center" onclick="addHook(this);"><?= $button_add_hook; ?></a></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$statuses, "plugin_data[%plug_row%][status]"); ?>" + '</td>';
	html += '		<td class="left"><a onclick="$(\'#plug-row%plug_row%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%plug_row%/g,plug_row));
	plug_row++;
}

function addHook(c){
	html = '<div class="hook_input">';
	html +='	<label for="hook-method-%key%"><?= $entry_hook_method; ?></label><input id="hook-method-%key%" type="text" name="plugin_data[%row%][hooks][%key%][callback]" value="" size="25" />';
	html +="	<?= $this->builder->build('select',$call_when,"plugin_data[%row%][hooks][%key%][when]"); ?><br />";
	html +='	<label for="hook-for-%key%"><?= $entry_hook_for; ?></label><input id="hook-for-%key%" type="text" name="plugin_data[%row%][hooks][%key%][for]" value="" size="25" />';
	html +='	<a onclick="$(this).parent().remove()"><?= $button_hook_remove; ?></a>';
	html +='</div>';
	
	row = parseInt($(c).closest('tbody').attr('row')) || 0;
	key = ($(c).data('hook_rows')||0)+1;
	$(c).before(html.replace(/%key%/g,key).replace(/%row%/g,row));
	$(c).data('hook_rows',key);
}
//--></script>

<?= $this->builder->js('errors', $errors); ?>