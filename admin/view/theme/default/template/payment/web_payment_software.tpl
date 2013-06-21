<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_login; ?></td>
					<td><input type="text" name="web_payment_software_merchant_name" value="<?= $web_payment_software_merchant_name; ?>" />
						<? if ($error_login) { ?>
						<span class="error"><?= $error_login; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_key; ?></td>
					<td><input type="text" name="web_payment_software_merchant_key" value="<?= $web_payment_software_merchant_key; ?>" />
						<? if ($error_key) { ?>
						<span class="error"><?= $error_key; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><?= $entry_mode; ?></td>
					<td><select name="web_payment_software_mode">
							<? if ($web_payment_software_mode == 'live') { ?>
							<option value="live" selected="selected"><?= $text_live; ?></option>
							<? } else { ?>
							<option value="live"><?= $text_live; ?></option>
							<? } ?>
							<? if ($web_payment_software_mode == 'test') { ?>
							<option value="test" selected="selected"><?= $text_test; ?></option>
							<? } else { ?>
							<option value="test"><?= $text_test; ?></option>
							<? } ?>
						</select></td>
				</tr>
				<tr>
					<td><?= $entry_method; ?></td>
					<td><select name="web_payment_software_method">
							<? if ($web_payment_software_method == 'authorization') { ?>
							<option value="authorization" selected="selected"><?= $text_authorization; ?></option>
							<? } else { ?>
							<option value="authorization"><?= $text_authorization; ?></option>
							<? } ?>
							<? if ($web_payment_software_method == 'capture') { ?>
							<option value="capture" selected="selected"><?= $text_capture; ?></option>
							<? } else { ?>
							<option value="capture"><?= $text_capture; ?></option>
							<? } ?>
						</select></td>
				</tr>
				<tr>
					<td><?= $entry_order_status; ?></td>
					<td><select name="web_payment_software_order_status_id">
							<? foreach ($order_statuses as $order_status) { ?>
							<? if ($order_status['order_status_id'] == $web_payment_software_order_status_id) { ?>
							<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
							<? } else { ?>
							<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
							<? } ?>
							<? } ?>
						</select></td>
				</tr>
				<tr>
					<td><?= $entry_geo_zone; ?></td>
					<td><select name="web_payment_software_geo_zone_id">
							<option value="0"><?= $text_all_zones; ?></option>
							<? foreach ($geo_zones as $geo_zone) { ?>
							<? if ($geo_zone['geo_zone_id'] == $web_payment_software_geo_zone_id) { ?>
							<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
							<? } else { ?>
							<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
							<? } ?>
							<? } ?>
						</select></td>
				</tr>
				<tr>
					<td><?= $entry_status; ?></td>
					<td><select name="web_payment_software_status">
							<? if ($web_payment_software_status) { ?>
							<option value="1" selected="selected"><?= $text_enabled; ?></option>
							<option value="0"><?= $text_disabled; ?></option>
							<? } else { ?>
							<option value="1"><?= $text_enabled; ?></option>
							<option value="0" selected="selected"><?= $text_disabled; ?></option>
							<? } ?>
						</select></td>
				</tr>
				<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="web_payment_software_total" value="<?= $web_payment_software_total; ?>" /></td>
				</tr>
				<tr>
					<td><?= $entry_sort_order; ?></td>
					<td><input type="text" name="web_payment_software_sort_order" value="<?= $web_payment_software_sort_order; ?>" size="1" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?= $footer; ?>