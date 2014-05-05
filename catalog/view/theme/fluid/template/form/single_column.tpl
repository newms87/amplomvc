<? if ($show_tag) { ?>
<form action="<?= $action; ?>" method="<?= $method; ?>">
	<? } ?>

	<table id="<?= $form_id; ?>" class="form form-single_column">
		<? foreach ($fields as $name => $field) { ?>
			<tr>
				<td>
					<? if ($field['required']) { ?>
						<span class="required"></span>
					<? } ?>
					<span class="form-entry"><?= $field['display_name']; ?></span><br/>

					<? switch ($field['type']) {

						case 'text':
							?>
							<input type="text" name="<?= $field['name']; ?>" value="<?= $field['value']; ?>"/>
							<? break;

						case 'password':
							?>
							<input type="password" name="<?= $field['name']; ?>" value=""/>
							<? break;

						case 'radio':
						case 'multiselect':
						case 'select':
							?>
							<? if (!empty($field['options'])) { ?>
							<? $this->builder->setConfig(key($field['build_config']), current($field['build_config'])); ?>
							<?= $this->builder->build($field['type'], $field['options'], $field['name'], $field['value'], $field['attrs']); ?>
						<? } elseif ($field['type'] == 'select') { ?>
							<select name="<?= $field['name']; ?>" <?= $field['html_attrs']; ?> <?= !empty($field['value']) ? "select_value=\"$field[value]\"" : ''; ?>></select>
						<? } ?>
							<? break;

						default:
							break;
					} ?>
				</td>
			</tr>
		<? } ?>

	</table>

	<? if ($show_tag) { ?>
</form>
<? } ?>