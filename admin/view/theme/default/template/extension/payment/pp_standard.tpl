<div id="pp_standard_settings">
	<table class="form">
		<tr>
			<td><?= $entry_button_graphic; ?></td>
			<td>
				<input type="text" size="80" name="settings[button_graphic]" value="<?= $settings['button_graphic']; ?>" />
				<img id="button_graphic_img" src="<?= $settings['button_graphic']; ?>" />
			</td>
		</tr>
		<tr>
			<td class="required"> <?= $entry_email; ?></td>
			<td><input type="text" name="settings[email]" size="50" value="<?= $settings['email']; ?>"/></td>
		</tr>
		<tr>
			<td> <?= $entry_test_email; ?></td>
			<td><input type="text" name="settings[test_email]" size="50" value="<?= $settings['test_email']; ?>"/></td>
		</tr>
		<tr>
			<td><?= $entry_test; ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'settings[test]', $settings['test']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_transaction; ?></td>
			<td><?= $this->builder->build('select', $data_auth_sale, 'settings[transaction]', $settings['transaction']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_pdt_enabled; ?></td>
			<td>
				<?= $this->builder->build('select', $data_statuses, "settings[pdt_enabled]", $settings['pdt_enabled']); ?>
				<span class="help"><?= $entry_pdt_enabled_help; ?></span>
			</td>
		</tr>
		<tr>
			<td class="required"><?= $entry_pdt_token; ?></td>
			<td><input type="text" name="settings[pdt_token]" value="<?= $settings['pdt_token']; ?>" size="80"/></td>
		</tr>
		<tr>
			<td><?= $entry_debug; ?></td>
			<td><?= $this->builder->build('select', $data_statuses, "settings[debug]", $settings['debug']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_total; ?></td>
			<td><input type="text" name="settings[total]" value="<?= $settings['total']; ?>"/></td>
		</tr>
		<tr>
			<td><?= $entry_page_style; ?></td>
			<td><input type="text" name="settings[page_style]" value="<?= $settings['page_style']; ?>"/></td>
		</tr>
		<tr>
			<td><?= $entry_canceled_reversal_status; ?></td>
			<? $this->builder->setConfig(false, 'title'); ?>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[canceled_reversal_status_id]", $settings['canceled_reversal_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_denied_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[denied_status_id]", $settings['denied_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_expired_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[expired_status_id]", $settings['expired_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_failed_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[failed_status_id]", $settings['failed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_pending_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[pending_status_id]", $settings['pending_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_processed_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[processed_status_id]", $settings['processed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_refunded_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[refunded_status_id]", $settings['refunded_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_reversed_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[reversed_status_id]", $settings['reversed_status_id']); ?></td>
		</tr>
		<tr>
			<td><?= $entry_voided_status; ?></td>
			<td><?= $this->builder->build('select', $data_order_statuses, "settings[voided_status_id]", $settings['voided_status_id']); ?></td>
		</tr>
	</table>
</div>

<script type="text/javascript">//<!--
	$('[name="settings[button_graphic]"]').change(function(){
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
//--></script>

<?= $this->builder->js('errors', $errors); ?>
