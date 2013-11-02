<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>
<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
					href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_admin; ?></td>
						<td><? if ($store_admin) { ?>
								<input type="radio" name="store_admin" value="1" checked="checked"/>
								<?= $text_yes; ?>
								<input type="radio" name="store_admin" value="0"/>
								<?= $text_no; ?>
							<? } else { ?>
								<input type="radio" name="store_admin" value="1"/>
								<?= $text_yes; ?>
								<input type="radio" name="store_admin" value="0" checked="checked"/>
								<?= $text_no; ?>
							<? } ?></td>
					</tr>
				</table>
				<table id="module" class="list">
					<thead>
					<tr>
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
							<td class="left"><select name="store_module[<?= $module_row; ?>][layout_id]">
									<? foreach ($layouts as $layout) { ?>
										<? if ($layout['layout_id'] == $module['layout_id']) { ?>
											<option value="<?= $layout['layout_id']; ?>"
											        selected="selected"><?= $layout['name']; ?></option>
										<? } else { ?>
											<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
							<td class="left"><select name="store_module[<?= $module_row; ?>][position]">
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
							<td class="left"><select name="store_module[<?= $module_row; ?>][status]">
									<? if ($module['status']) { ?>
										<option value="1" selected="selected"><?= $text_enabled; ?></option>
										<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
										<option value="1"><?= $text_enabled; ?></option>
										<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
							<td class="right"><input type="text" name="store_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3"/></td>
							<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();"
							                    class="button"><?= $button_remove; ?></a></td>
						</tr>
						</tbody>
						<? $module_row++; ?>
					<? } ?>
					<tfoot>
					<tr>
						<td colspan="4"></td>
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
			html = '<tbody id="module-row' + module_row + '">';
			html += '	<tr>';
			html += '		<td class="left"><select name="store_module[' + module_row + '][layout_id]">';
			<? foreach ($layouts as $layout) { ?>
			html += '			<option value="<?= $layout['layout_id']; ?>"><?= addslashes($layout['name']); ?></option>';
			<? } ?>
			html += '		</select></td>';
			html += '		<td class="left"><select name="store_module[' + module_row + '][position]">';
			html += '			<option value="content_top"><?= $text_content_top; ?></option>';
			html += '			<option value="content_bottom"><?= $text_content_bottom; ?></option>';
			html += '			<option value="column_left"><?= $text_column_left; ?></option>';
			html += '			<option value="column_right"><?= $text_column_right; ?></option>';
			html += '		</select></td>';
			html += '		<td class="left"><select name="store_module[' + module_row + '][status]">';
			html += '			<option value="1" selected="selected"><?= $text_enabled; ?></option>';
			html += '			<option value="0"><?= $text_disabled; ?></option>';
			html += '		</select></td>';
			html += '		<td class="right"><input type="text" name="store_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
			html += '		<td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
			html += '	</tr>';
			html += '</tbody>';

			$('#module tfoot').before(html);

			module_row++;
		}
</script>
<?= $footer; ?>
