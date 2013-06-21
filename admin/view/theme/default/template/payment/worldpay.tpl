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
						<td class="required"> <?= $entry_merchant; ?></td>
						<td><input type="text" name="worldpay_merchant" value="<?= $worldpay_merchant; ?>" />
							<? if ($error_merchant) { ?>
							<span class="error"><?= $error_merchant; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_password; ?></td>
						<td><input type="text" name="worldpay_password" value="<?= $worldpay_password; ?>" />
							<? if ($error_password) { ?>
							<span class="error"><?= $error_password; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_callback; ?></td>
						<td><textarea cols="40" rows="5"><?= $callback; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><select name="worldpay_test">
								<? if ($worldpay_test == '0') { ?>
								<option value="0" selected="selected"><?= $text_off; ?></option>
								<? } else { ?>
								<option value="0"><?= $text_off; ?></option>
								<? } ?>
								<? if ($worldpay_test == '100') { ?>
								<option value="100" selected="selected"><?= $text_successful; ?></option>
								<? } else { ?>
								<option value="100"><?= $text_successful; ?></option>
								<? } ?>
								<? if ($worldpay_test == '101') { ?>
								<option value="101" selected="selected"><?= $text_declined; ?></option>
								<? } else { ?>
								<option value="101"><?= $text_declined; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="worldpay_total" value="<?= $worldpay_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td><select name="worldpay_order_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $worldpay_order_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="worldpay_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $worldpay_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="worldpay_status">
								<? if ($worldpay_status) { ?>
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
						<td><input type="text" name="worldpay_sort_order" value="<?= $worldpay_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 