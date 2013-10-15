<table class="form">
	<tr>
		<td><?= $entry_merchant_id; ?></td>
		<td><input type="text" name="settings[merchant_id]" value="<?= $settings['merchant_id']; ?>" /></td>
	</tr>
	<tr>
		<td><?= $entry_public_key; ?></td>
		<td><input type="text" name="settings[public_key]" value="<?= $settings['public_key']; ?>" /></td>
	</tr>
	<tr>
		<td><?= $entry_private_key; ?></td>
		<td><input type="text" name="settings[private_key]" value="<?= $settings['private_key']; ?>" /></td>
	</tr>
	<tr>
		<td><?= $entry_client_side_encryption_key; ?></td>
		<td><textarea rows="8" cols="70" name="settings[client_side_encryption_key]"><?= $settings['client_side_encryption_key']; ?></textarea></td>
	</tr>
	<tr>
		<td><?= $entry_mode; ?></td>
		<td><?= $this->builder->build('select', $data_modes, "settings[mode]", $settings['mode']); ?></td>
	</tr>
	<tr>
		<td><?= $entry_plan_id; ?></td>
		<td>
			<? if (!empty($data_braintree_plans)) { ?>
				<? $this->builder->setConfig('id', 'name'); ?>
				<?= $this->builder->build('select', $data_braintree_plans, "settings[plan_id]", $settings['plan_id']); ?>
			<? } else { ?>
				<p><?= $error_no_plans; ?></p>
			<? } ?>
		</td>
	</tr>
</table>
