<div id="pp_standard_settings">
	<table class="form">
		<tr>
			<td>
				<?= _l("Image Title:"); ?>
				<span class="help"><?= _l("The Paypal Button Graphic image to replace the Paypal title during checkout. Leave blank to use default title."); ?></span>
			</td>
			<td>
				<input type="text" size="80" name="settings[button_graphic]" value="<?= $settings['button_graphic']; ?>"/>
				<img id="button_graphic_img" src="<?= $settings['button_graphic']; ?>"/>
			</td>
		</tr>
		<tr>
			<td class="required"> <?= _l("E-Mail:"); ?></td>
			<td><input type="text" name="settings[email]" size="50" value="<?= $settings['email']; ?>"/></td>
		</tr>
		<tr>
			<td>
				<?= _l("Test Account E-Mail:"); ?>
				<span class="help"><?= _l("(if different than your primary account)"); ?></span>
			</td>
			<td><input type="text" name="settings[test_email]" size="50" value="<?= $settings['test_email']; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("Sandbox Mode:"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'settings[test]', $settings['test']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Transaction Method:"); ?></td>
			<td><?= $this->builder->build('select', $data_auth_sale, 'settings[transaction]', $settings['transaction']); ?></td>
		</tr>
		<tr>
			<td>
				<?= _l("PDT is enabled?"); ?>
				<span class="help"><?= _l("This will allow the user to be instantly returned to your site after payment."); ?></span>
			</td>
			<td>
				<?= $this->builder->build('select', $data_statuses, "settings[pdt_enabled]", $settings['pdt_enabled']); ?>
				<span class="help"><?= _l("To enabled PDT on your account you must"); ?> <a target="_blank" href="http://www.paypal.com/"><?= _l("login to your paypal account"); ?></a>.<br/>
					<?= _l("Go to Profile > Website payments preferences."); ?><br/>
					<?= _l("From here enable PDT and Auto Return."); ?>
				</span>
			</td>
		</tr>
		<tr>
			<td class="required"><?= _l("PDT Identity Token"); ?></td>
			<td><input type="text" name="settings[pdt_token]" value="<?= $settings['pdt_token']; ?>" size="80"/></td>
		</tr>
		<tr>
			<td>
				<div><?= _l("Debug Mode:"); ?></div>
				<span class="help"><?= _l("Logs additional information to the system log."); ?></span>
			</td>
			<td><?= $this->builder->build('select', $data_statuses, "settings[debug]", $settings['debug']); ?></td>
		</tr>
		<tr>
			<td>
				<div><?= _l("Total:"); ?></div>
				<span class="help"><?= _l("The checkout total the order must reach before this payment method becomes active."); ?></span>
			</td>
			<td><input type="text" name="settings[total]" value="<?= $settings['total']; ?>"/></td>
		</tr>
		<tr>
			<td>
				<?= _l("Page Style:"); ?>
				<span class="help"><?= _l("Enter 'primary' for the primary style set on your Paypal account, or enter the name of the style as you named it on your paypal account. Leave blank to use the default"); ?></span>
			</td>
			<td><input type="text" name="settings[page_style]" value="<?= $settings['page_style']; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("Canceled Reversal Status:"); ?></td>
			<? $this->builder->setConfig(false, 'title'); ?>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[canceled_reversal_status_id]", $settings['canceled_reversal_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Denied Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[denied_status_id]", $settings['denied_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Expired Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[expired_status_id]", $settings['expired_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Failed Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[failed_status_id]", $settings['failed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Pending Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[pending_status_id]", $settings['pending_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Processed Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[processed_status_id]", $settings['processed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Refunded Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[refunded_status_id]", $settings['refunded_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Reversed Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[reversed_status_id]", $settings['reversed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Voided Status:"); ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[voided_status_id]", $settings['voided_status_id']); ?></td>
		</tr>
	</table>
</div>

<script type="text/javascript">
	$('[name="settings[button_graphic]"]').change(function () {
		$('#button_graphic_img').attr('src', $(this).val());
	});

	$('[name="settings[pdt_enabled]"]').change(function () {
		token_row = $('[name="settings[pdt_token]"]').closest('tr');

		if ($(this).val() === '1') {
			token_row.show();
		} else {
			token_row.hide();
		}
	}).change();
</script>

<?= $this->builder->js('errors', $errors); ?>
