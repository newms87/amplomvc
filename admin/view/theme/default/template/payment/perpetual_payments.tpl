<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><span class="required"></span> <?= $entry_auth_id; ?></td>
						<td><input type="text" name="perpetual_payments_auth_id" value="<?= $perpetual_payments_auth_id; ?>" />
							<? if ($error_auth_id) { ?>
							<span class="error"><?= $error_auth_id; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_auth_pass; ?></td>
						<td><input type="text" name="perpetual_payments_auth_pass" value="<?= $perpetual_payments_auth_pass; ?>" />
							<? if ($error_auth_pass) { ?>
							<span class="error"><?= $error_auth_pass; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><? if ($perpetual_payments_test) { ?>
							<input type="radio" name="perpetual_payments_test" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="perpetual_payments_test" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="perpetual_payments_test" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="perpetual_payments_test" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="perpetual_payments_total" value="<?= $perpetual_payments_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td><select name="perpetual_payments_order_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $perpetual_payments_order_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="perpetual_payments_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $perpetual_payments_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="perpetual_payments_status">
								<? if ($perpetual_payments_status) { ?>
								<option value="1" selected="selected"><?= $text_enabled; ?></option>
								<option value="0"><?= $text_disabled; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_enabled; ?></option>
								<option value="0" selected="selected"><?= $text_disabled; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="perpetual_payments_sort_order" value="<?= $perpetual_payments_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 