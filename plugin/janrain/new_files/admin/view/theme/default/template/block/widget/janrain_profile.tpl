<table id="module" class="list">
	<thead>
		<tr>
			<td class="center"><?= _l("Layout"); ?></td>
			<td class="center"><?= _l("Display Type"); ?></td>
			<td class="center"><?= _l("Icon Size"); ?></td>
			<td class="center"><?= _l("Position"); ?></td>
			<td class="center"><?= _l("Status"); ?></td>
			<td class="center"><?= _l("Sort Order"); ?></td>
			<td></td>
		</tr>
	</thead>
	<? $module_row = 0; ?>
	<? foreach ($modules as $module) { ?>
		<tbody id="module-row<?= $module_row; ?>">
			<tr>
				<td class="center">
					<? $this->builder->setConfig('layout_id', 'name'); ?>
					<?= $this->builder->build('select', $layouts, "janrain_module[$module_row][layout_id]", $module['layout_id']); ?>
				</td>
				<td class="center">
					<?= $this->builder->build('select', $display_types, "janrain_module[$module_row][display_type]", $module['display_type']); ?>
				</td>
				<td class="center">
					<?= $this->builder->build('select', $icon_sizes, "janrain_module[$module_row][icon_size]", $module['icon_size']); ?>
				</td>
				<td class="center">
					<?= $this->builder->build('select', $positions, "janrain_module[$module_row][position]", $module['position']); ?>
				</td>
				<td class="center">
					<?= $this->builder->build('select', $statuses, "janrain_module[$module_row][status]", (int)$module['status']); ?>
				</td>
				<td class="center"><input type="text" name="janrain_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3"/></td>
				<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();"
						class="button"><span><?= _l("Remove"); ?></span></a></td>
			</tr>
		</tbody>
		<? $module_row++; ?>
	<? } ?>
	<tfoot>
		<tr>
			<td colspan="7"></td>
			<td class="left"><a onclick="addModule();" class="button"><span><?= _l("Add Module"); ?></span></a></td>
		</tr>
	</tfoot>
</table>
