<table class="form">
	<tr>
		<td><?= _l("Merchant ID:"); ?></td>
		<td><input type="text" name="settings[merchant_id]" value="<?= $settings['merchant_id']; ?>" /></td>
	</tr>
	<tr>
		<td><?= _l("Public Key:"); ?></td>
		<td><input type="text" name="settings[public_key]" value="<?= $settings['public_key']; ?>" /></td>
	</tr>
	<tr>
		<td><?= _l("Private Key:"); ?></td>
		<td><input type="text" name="settings[private_key]" value="<?= $settings['private_key']; ?>" /></td>
	</tr>
	<tr>
		<td><?= _l("Client-Side Encryption Key"); ?></td>
		<td><textarea rows="8" cols="70" name="settings[client_side_encryption_key]"><?= $settings['client_side_encryption_key']; ?></textarea></td>
	</tr>
	<tr>
		<td><?= _l("Environment Mode"); ?></td>
		<td><?= $this->builder->build('select', $data_modes, "settings[mode]", $settings['mode']); ?></td>
	</tr>
	<tr>
		<td>
			<?= _l("Recurring Billing Plan ID"); ?>
			<span class="help">
				<?= _l("Log into"); ?> <a target="_blank" href="https://sandbox.braintreegateway.com/"><?= _l("your braintree account"); ?></a>
				<?= _l(" and "); ?>
				<a target="_blank" href="https://www.braintreepayments.com/docs/php/guide/recurring_billing"><?= _l("make a plan"); ?></a>.
				<?= _l("Enter the plan ID here. Set the Price to $1.00."); ?>
			</span>
		</td>
		<td>
			<? if (!empty($data_braintree_plans)) { ?>
				<? $this->builder->setConfig('id', 'name'); ?>
				<?= $this->builder->build('select', $data_braintree_plans, "settings[plan_id]", $settings['plan_id']); ?>
			<? } else { ?>
				<p>
					<a target="_blank" href="https://sandbox.braintreegateway.com/"><?= _l("Go Setup a Recurring Billing Plan Now!"); ?></a>
				</p>
			<? } ?>
		</td>
	</tr>
</table>
