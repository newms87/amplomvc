<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div class="vtabs">
					<? $module_row = 1; ?>
					<? foreach ($modules as $module) { ?>
					<a href="#tab-module-<?= $module_row; ?>" id="module-<?= $module_row; ?>"><?= $tab_module . ' ' . $module_row; ?>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$('.vtabs a:first').trigger('click'); $('#module-<?= $module_row; ?>').remove(); $('#tab-module-<?= $module_row; ?>').remove(); return false;" /></a>
					<? $module_row++; ?>
					<? } ?>
					<span id="module-add"><?= $button_add_module; ?>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="" onclick="addModule();" /></span> </div>
				<? $module_row = 1; ?>
				<? foreach ($modules as $module) { ?>
				<div id="tab-module-<?= $module_row; ?>" class="vtabs-content">
					<div id="language-<?= $module_row; ?>" class="htabs">
						<? foreach ($languages as $language) { ?>
						<a href="#tab-language-<?= $module_row; ?>-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
						<? } ?>
					</div>
					<? foreach ($languages as $language) { ?>
					<div id="tab-language-<?= $module_row; ?>-<?= $language['language_id']; ?>">
						<table class="form">
							<tr>
								<td><?= $entry_description; ?></td>
								<td><textarea name="welcome_module[<?= $module_row; ?>][description][<?= $language['language_id']; ?>]" id="description-<?= $module_row; ?>-<?= $language['language_id']; ?>"><?= isset($module['description'][$language['language_id']]) ? $module['description'][$language['language_id']] : ''; ?></textarea></td>
							</tr>
						</table>
					</div>
					<? } ?>
					<table class="form">
						<tr>
							<td><?= $entry_layout; ?></td>
							<td><select name="welcome_module[<?= $module_row; ?>][layout_id]">
									<? foreach ($layouts as $layout) { ?>
									<? if ($layout['layout_id'] == $module['layout_id']) { ?>
									<option value="<?= $layout['layout_id']; ?>" selected="selected"><?= $layout['name']; ?></option>
									<? } else { ?>
									<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
									<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_position; ?></td>
							<td><select name="welcome_module[<?= $module_row; ?>][position]">
									<? if ($module['position'] == 'content_top') { ?>
									<option value="content_top" selected="selected"><?= $text_content_top; ?></option>
									<? } else { ?>
									<option value="content_top"><?= $text_content_top; ?></option>
									<? } ?>
									<? if ($module['position'] == 'content_bottom') { ?>
									<option value="content_bottom" selected="selected"><?= $text_content_bottom; ?></option>
									<? } else { ?>
									<option value="content_bottom"><?= $text_content_bottom; ?></option>
									<? } ?>
									<? if ($module['position'] == 'column_left') { ?>
									<option value="column_left" selected="selected"><?= $text_column_left; ?></option>
									<? } else { ?>
									<option value="column_left"><?= $text_column_left; ?></option>
									<? } ?>
									<? if ($module['position'] == 'column_right') { ?>
									<option value="column_right" selected="selected"><?= $text_column_right; ?></option>
									<? } else { ?>
									<option value="column_right"><?= $text_column_right; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="welcome_module[<?= $module_row; ?>][status]">
									<? if ($module['status']) { ?>
									<option value="1" selected="selected"><?= $text_enabled; ?></option>
									<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
									<option value="1"><?= $text_enabled; ?></option>
									<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="welcome_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
						</tr>
					</table>
				</div>
				<? $module_row++; ?>
				<? } ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
<? $module_row = 1; ?>
<? foreach ($modules as $module) { ?>
<? foreach ($languages as $language) { ?>
CKEDITOR.replace('description-<?= $module_row; ?>-<?= $language['language_id']; ?>', {
	filebrowserImageBrowseUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
	filebrowserFlashBrowseUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
	filebrowserUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
	filebrowserImageUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
	filebrowserFlashUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>"
});
<? } ?>
<? $module_row++; ?>
<? } ?>
//--></script>
<script type="text/javascript"><!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<div id="tab-module-' + module_row + '" class="vtabs-content">';
	html += '	<div id="language-' + module_row + '" class="htabs">';
		<? foreach ($languages as $language) { ?>
		html += '		<a href="#tab-language-'+ module_row + '-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>';
		<? } ?>
	html += '	</div>';

	<? foreach ($languages as $language) { ?>
	html += '		<div id="tab-language-'+ module_row + '-<?= $language['language_id']; ?>">';
	html += '			<table class="form">';
	html += '				<tr>';
	html += '					<td><?= $entry_description; ?></td>';
	html += '					<td><textarea name="welcome_module[' + module_row + '][description][<?= $language['language_id']; ?>]" id="description-' + module_row + '-<?= $language['language_id']; ?>"></textarea></td>';
	html += '				</tr>';
	html += '			</table>';
	html += '		</div>';
	<? } ?>

	html += '	<table class="form">';
	html += '		<tr>';
	html += '			<td><?= $entry_layout; ?></td>';
	html += '			<td><select name="welcome_module[' + module_row + '][layout_id]">';
	<? foreach ($layouts as $layout) { ?>
	html += '					<option value="<?= $layout['layout_id']; ?>"><?= addslashes($layout['name']); ?></option>';
	<? } ?>
	html += '			</select></td>';
	html += '		</tr>';
	html += '		<tr>';
	html += '			<td><?= $entry_position; ?></td>';
	html += '			<td><select name="welcome_module[' + module_row + '][position]">';
	html += '				<option value="content_top"><?= $text_content_top; ?></option>';
	html += '				<option value="content_bottom"><?= $text_content_bottom; ?></option>';
	html += '				<option value="column_left"><?= $text_column_left; ?></option>';
	html += '				<option value="column_right"><?= $text_column_right; ?></option>';
	html += '			</select></td>';
	html += '		</tr>';
	html += '		<tr>';
	html += '			<td><?= $entry_status; ?></td>';
	html += '			<td><select name="welcome_module[' + module_row + '][status]">';
	html += '				<option value="1"><?= $text_enabled; ?></option>';
	html += '				<option value="0"><?= $text_disabled; ?></option>';
	html += '			</select></td>';
	html += '		</tr>';
	html += '		<tr>';
	html += '			<td><?= $entry_sort_order; ?></td>';
	html += '			<td><input type="text" name="welcome_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '		</tr>';
	html += '	</table>';
	html += '</div>';
	
	$('#form').append(html);
	
	<? foreach ($languages as $language) { ?>
	CKEDITOR.replace('description-' + module_row + '-<?= $language['language_id']; ?>', {
		filebrowserImageBrowseUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
		filebrowserFlashBrowseUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
		filebrowserUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
		filebrowserImageUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>",
		filebrowserFlashUploadUrl: "<?= HTTP_ADMIN . "index.php?route=common/filemanager"; ?>"
	});
	<? } ?>
	
	$('#language-' + module_row + ' a').tabs();
	
	$('#module-add').before('<a href="#tab-module-' + module_row + '" id="module-' + module_row + '"><?= $tab_module; ?> ' + module_row + '&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$(\'.vtabs a:first\').trigger(\'click\'); $(\'#module-' + module_row + '\').remove(); $(\'#tab-module-' + module_row + '\').remove(); return false;" /></a>');
	
	$('.vtabs a').tabs();
	
	$('#module-' + module_row).trigger('click');
	
	module_row++;
}
//--></script>
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>
<script type="text/javascript"><!--
<? $module_row = 1; ?>
<? foreach ($modules as $module) { ?>
$('#language-<?= $module_row; ?> a').tabs();
<? $module_row++; ?>
<? } ?>
//--></script>
<?= $footer; ?>