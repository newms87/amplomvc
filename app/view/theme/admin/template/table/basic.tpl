<table class="form">
	<? foreach ($fields as $key => $field) { ?>
		<tr>
			<td class="required"><?= !empty($field['display_name']) ? $field['display_name'] : $key; ?></td>
			<td>
				<? if (!empty($field['readonly'])) { ?>
					<span class="read-only"><?= $field['value']; ?></span>
				<? } else { ?>
					<? switch ($field['type']) {
						case 'select':
						case 'radio':
						case 'multiselect':
						case 'checkbox':
						case 'text':
							$build = array(
								'name'   => $key,
								'data'   => isset($field['build_data']) ? $field['build_data'] : array(),
								'select' => $field['value'],
							);

							if (!empty($field['build_config'])) {
								$build['key']   = $field['build_config'][0];
								$build['value'] = $field['build_config'][1];
							}

							echo build($field['type'], $build);

							break;

						case 'date':
						case 'time':
						case 'datetime':
							?>
							<input type="text" name="<?= $key; ?>" class="<?= $field['type']; ?>picker" value="<?= $field['value']; ?>" />
							<? break;

						case 'int':
						case 'float':
						case 'decimal':
							?>
							<input type="text" name="<?= $key; ?>" class="<?= $field['type']; ?>" value="<?= $field['value']; ?>" />
							<? break;
					} ?>
				<? } ?>
			</td>
		</tr>
	<? } ?>
</table>