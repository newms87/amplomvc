<?= $header; ?>
<div class="content">
<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
					href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table id="module" class="list">
					<thead>
					<tr>
						<td class="left"><?= $entry_limit; ?></td>
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
							<td class="left"><input type="text" name="designer_sidebar_module[<?= $module_row; ?>][limit]"
							                        value="<?= $module['limit']; ?>" size="1" maxlength='2'/></td>
							<td class='left'>
								<? $this->builder->set_config('layout_id', 'name'); ?>
								<?= $this->builder->build('select', $layouts, "designer_sidebar_module[$module_row][layout_id]", (int)$module['layout_id']); ?>
							</td>
							<td
								class='left'><?= $this->builder->build('select', $positions, "designer_sidebar_module[$module_row][position]", $module['position']); ?></td>
							<td
								class="left"><?= $this->builder->build('select', $data_statuses, "designer_sidebar_module[$module_row][status]", (int)$module['status']); ?></td>
							<td class="right"><input type="text"
							                         name="designer_sidebar_module[<?= $module_row; ?>][sort_order]"
							                         value="<?= $module['sort_order']; ?>" size="3"/></td>
							<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();"
							                    class="button"><?= $button_remove; ?></a></td>
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
	<script type="text/javascript"><!--
		var module_row = <?= $module_row; ?>;

		function addModule() {
			html = '<tbody id="module-row%modrow%">';
			html += '	<tr>';
			html += '		<td class="left"><input type="text" name="designer_sidebar_module[%modrow%][limit]" value="5" size="1" maxlength="2" /></td>';
			html += '		<td class="left">' + "<?= $this->builder->build('select',$layouts,'designer_sidebar_module[%modrow%][layout_id]'); ?>" + '</td>';
			html += '		<td class="left">' + "<?= $this->builder->build('select',$positions,'designer_sidebar_module[%modrow%][position]'); ?>" + '</td>';
			html += '		<td class="left">' + "<?= $this->builder->build('select',$statuses, 'designer_sidebar_module[%modrow%][status]'); ?>" + '</td>';
			html += '		<td class="right"><input type="text" name="designer_sidebar_module[%modrow%][sort_order]" size="3" /></td>';
			html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
			html += '	</tr>';
			html += '</tbody>';

			$('#module tfoot').before(html.replace(/%modrow%/g, module_row));

			module_row++;
		}
		//--></script>
<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>