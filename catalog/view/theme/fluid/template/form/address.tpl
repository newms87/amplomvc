<? if ($show_tag) { ?>
<form action="<?= $action; ?>" method="<?= $method; ?>">
	<? } ?>

	<table id="<?= $form_id; ?>" class="form <?= $form_name; ?>">
		<? foreach ($fields as $name => $field) { ?>
			<? if ($name == 'default' || $name == 'submit_address') {
				continue;
			} //We add these fields at the bottom
			?>
			<tr>
				<td>
					<? if ($field['required']) { ?>
						<span class="required"></span>
					<? } ?>
					<span class="form-entry"><?= $field['display_name']; ?></span>
				</td>
				<td>
					<? switch ($field['type']) {

						case 'text':
							?>
							<input type="text" name="<?= $name; ?>" value="<?= $field['value']; ?>"/>
							<? break;

						case 'radio':
						case 'multiselect':
						case 'select':
							?>
							<? if (!empty($field['options'])) { ?>
							<? $this->builder->setConfig(key($field['build_config']), current($field['build_config'])); ?>
							<?= $this->builder->build($field['type'], $field['options'], $name, $field['value'], $field['attrs']); ?>
						<? } elseif ($field['type'] == 'select') { ?>
							<select name="<?= $name; ?>" <?= $field['html_attrs']; ?>></select>
						<? } ?>
							<? break;

						default:
							break;
					} ?>
				</td>
			</tr>
		<? } ?>
		<? if (!empty($fields['submit_address'])) { ?>
			<tr class="address-bottom-section">
				<td>
					<? if (!empty($fields['default'])) {
						$field = $fields['default']; ?>
						<div class="set-default-address">
							<div><?= $field['display_name']; ?></div>
							<?= $this->builder->build('radio', $field['options'], 'default', $field['value']); ?>
						</div>
					<? } ?>
				</td>
				<td>
					<? $field = $fields['submit_address']; ?>
					<input type="submit" name="submit_address" class="button" value="<?= $field['display_name']; ?>"/>
				</td>
			</tr>
		<? } ?>
	</table>

	<? if ($show_tag) { ?>
</form>
<? } ?>

<script type="text/javascript">
	$('#<?= $form_id; ?> .zone_select').ac_zoneselect({listen: '#<?= $form_id; ?> .country_select'});
</script>