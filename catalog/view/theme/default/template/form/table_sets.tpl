<form action="<?= $action; ?>" method="<?= $method; ?>">

	<? $columns = array();

	foreach ($fields as $key => $f) {
		$columns[$f['column']][] = $f;
	}

	$col_width = (99 * (1 / $max_column)) . '%';

	foreach ($columns as $column) {
		?>
		<table class='form table_set_template' style="width:<?= $col_width; ?>">
			<? foreach ($column as $field) {
				if (empty($field)) {
					continue;
				}
				?>
				<tr>
					<td>
						<? if (isset($field['content_before'])) { ?>
							<?= $field['content_before']; ?>
						<? } ?>

						<? if (!in_array($field['type'], array(
						                                      'image',
						                                      'button',
						                                      'submit'
						                                 ))
						) { ?>
							<div class="field_title">
								<? if ($field['required']) {
									; ?>
									<span class="required"></span>
								<? } ?>
								<span class='form_entry'><?= $field['display_name']; ?></span>
							</div>
						<? } ?>
						<? switch ($field['type']) {

							case 'text':
							case 'password':
								?>
								<input type="<?= $field['type']; ?>" name="<?= $field['name']; ?>"
								       value="<?= $field['select']; ?>" <?= $field['html_attrs']; ?> />
								<? break;

							case 'select':
								$this->builder->set_config($field['builder_id'], $field['builder_name']);
								echo $this->builder->build('select', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
								break;

							case 'radio':
								$this->builder->set_config($field['builder_id'], $field['builder_name']);
								echo $this->builder->build('radio', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
								break;

							case 'checkbox':
								?>
								<? break;

							case 'button':
							case 'submit':
							case 'image':
								?>
								<input type="<?= $field['type']; ?>" name="<?= $field['name']; ?>"
								       value="<?= $field['display_name']; ?>" <?= $field['html_attrs']; ?> />
								<? break;

							default:
								break;
						} ?>

						<? if (isset($field['content_after'])) { ?>
							<?= $field['content_after']; ?>
						<? } ?>
					</td>
				</tr>
			<? } ?>
		</table>
	<? } ?>

</form>
