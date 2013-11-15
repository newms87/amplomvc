<? if ($use_form_tag) { ?>
<form action="<?= $action; ?>" method="<?= $method; ?>" <?= $form_tag_attrs; ?>>
	<? } ?>
	<table class="form">
		<?
		$column_set = array();

		foreach ($fields as $key => $f) {
			$col_set_num                              = (int)(($f['column'] - 1) / $max_column);
			$column_set[$col_set_num][$f['column']][] = $f;
		}


		foreach ($column_set as $column) {
			$max_rows = 0;
			foreach ($column as $c) {
				$max_rows = max(count($c), $max_rows);
			}

			$column2 = $column;

			for ($row = 0; $row < $max_rows; $row++) {
				?>
				<tr>
					<? foreach ($column as &$field_list) {
						if (!empty($field_list)) {
							$field = array_shift($field_list);
						} else {
							$field = false;
						}
						?>
						<td>
							<? if (!empty($field) && !in_array($field['type'], array(
							                                                        'image',
							                                                        'button',
							                                                        'submit'
							                                                   ))
							) {
								?>
								<? if ($field['required']) {
									; ?>
									<span class="required"></span>
								<? } ?>
								<span class="form_entry"><?= $field['display_name']; ?></span>
							<? } ?>
						</td>
					<? } ?>
				</tr>

				<tr>
					<? foreach ($column2 as &$field_list2) {
						if (!empty($field_list2)) {
							$field = array_shift($field_list2);
						} else {
							$field = false;
						}
						?>
						<td>
							<? if (isset($field['content_before'])) { ?>
								<?= $field['content_before']; ?>
							<? } ?>

							<? switch ($field['type']) {

								case 'text':
								case 'password':
									?>
									<input type="<?= $field['type']; ?>" name="<?= $field['name']; ?>" value="<?= $field['select']; ?>" <?= $field['html_attrs']; ?> />
									<? break;

								case 'select':
									$this->builder->setConfig($field['builder_id'], $field['builder_name']);
									echo $this->builder->build('select', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
									break;

								case 'radio':
									$this->builder->setConfig($field['builder_id'], $field['builder_name']);
									echo $this->builder->build('radio', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
									break;

								case 'checkbox':
									?>
									<? break;

								case 'button':
								case 'submit':
								case 'image':
									?>
									<input type="<?= $field['type']; ?>" name="<?= $field['name']; ?>" value="<?= $field['display_name']; ?>" <?= $field['html_attrs']; ?> />
									<? break;

								default:
									break;
							} ?>

							<? if (isset($field['content_after'])) { ?>
								<?= $field['content_after']; ?>
							<? } ?>
						</td>
					<? } ?>
				</tr>
			<?
			}
			unset($field_list);
			unset($field_list2); ?>
		<? } ?>

	</table>
	<? if ($use_form_tag) { ?>
</form>
<? } ?>
