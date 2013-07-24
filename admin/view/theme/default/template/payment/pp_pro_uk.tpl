<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
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
						<td class="required"> <?= $entry_vendor; ?></td>
						<td><input type="text" name="pp_pro_uk_vendor" value="<?= $pp_pro_uk_vendor; ?>" />
							<? if ($error_vendor) { ?>
							<span class="error"><?= $error_vendor; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_user; ?></td>
						<td><input type="text" name="pp_pro_uk_user" value="<?= $pp_pro_uk_user; ?>" />
							<? if ($error_user) { ?>
							<span class="error"><?= $error_user; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_password; ?></td>
						<td><input type="text" name="pp_pro_uk_password" value="<?= $pp_pro_uk_password; ?>" />
							<? if ($error_password) { ?>
							<span class="error"><?= $error_password; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_partner; ?></td>
						<td><input type="text" name="pp_pro_uk_partner" value="<?= $pp_pro_uk_partner; ?>" />
							<? if ($error_partner) { ?>
							<span class="error"><?= $error_partner; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><? if ($pp_pro_uk_test) { ?>
							<input type="radio" name="pp_pro_uk_test" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="pp_pro_uk_test" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="pp_pro_uk_test" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="pp_pro_uk_test" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_transaction; ?></td>
						<td><select name="pp_pro_uk_transaction">
								<? if (!$pp_pro_uk_transaction) { ?>
								<option value="0" selected="selected"><?= $text_authorization; ?></option>
								<? } else { ?>
								<option value="0"><?= $text_authorization; ?></option>
								<? } ?>
								<? if ($pp_pro_uk_transaction) { ?>
								<option value="1" selected="selected"><?= $text_sale; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_sale; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="pp_pro_uk_total" value="<?= $pp_pro_uk_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td><select name="pp_pro_uk_order_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_pro_uk_order_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="pp_pro_uk_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $pp_pro_uk_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="pp_pro_uk_status">
								<? if ($pp_pro_uk_status) { ?>
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
						<td><input type="text" name="pp_pro_uk_sort_order" value="<?= $pp_pro_uk_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>