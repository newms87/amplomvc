<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_merchant; ?></td>
						<td><input type="text" name="liqpay_merchant" value="<?= $liqpay_merchant; ?>" />
							<? if ($error_merchant) { ?>
							<span class="error"><?= $error_merchant; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_signature; ?></td>
						<td><input type="text" name="liqpay_signature" value="<?= $liqpay_signature; ?>" />
							<? if ($error_signature) { ?>
							<span class="error"><?= $error_signature; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_type; ?></td>
						<td><select name="liqpay_type">
								<? if ($liqpay_type == 'liqpay') { ?>
								<option value="liqpay" selected="selected"><?= $text_pay; ?></option>
								<? } else { ?>
								<option value="liqpay"><?= $text_pay; ?></option>
								<? } ?>
								<? if ($liqpay_type == 'card') { ?>
								<option value="card" selected="selected"><?= $text_card; ?></option>
								<? } else { ?>
								<option value="card"><?= $text_card; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="liqpay_total" value="<?= $liqpay_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td><select name="liqpay_order_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $liqpay_order_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="liqpay_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $liqpay_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="liqpay_status">
								<? if ($liqpay_status) { ?>
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
						<td><input type="text" name="liqpay_sort_order" value="<?= $liqpay_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>