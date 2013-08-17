<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="content">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_vendor; ?></td>
							<td><input type="text" name="sagepay_vendor" value="<?= $sagepay_vendor; ?>"/>
								<? if ($error_vendor) { ?>
									<span class="error"><?= $error_vendor; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_password; ?></td>
							<td><input type="text" name="sagepay_password" value="<?= $sagepay_password; ?>"/>
								<? if ($error_password) { ?>
									<span class="error"><?= $error_password; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_test; ?></td>
							<td><select name="sagepay_test">
									<? if ($sagepay_test == 'sim') { ?>
										<option value="sim" selected="selected"><?= $text_sim; ?></option>
									<? } else { ?>
										<option value="sim"><?= $text_sim; ?></option>
									<? } ?>
									<? if ($sagepay_test == 'test') { ?>
										<option value="test" selected="selected"><?= $text_test; ?></option>
									<? } else { ?>
										<option value="test"><?= $text_test; ?></option>
									<? } ?>
									<? if ($sagepay_test == 'live') { ?>
										<option value="live" selected="selected"><?= $text_live; ?></option>
									<? } else { ?>
										<option value="live"><?= $text_live; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_transaction; ?></td>
							<td><select name="sagepay_transaction">
									<? if ($sagepay_transaction == 'PAYMENT') { ?>
										<option value="PAYMENT" selected="selected"><?= $text_payment; ?></option>
									<? } else { ?>
										<option value="PAYMENT"><?= $text_payment; ?></option>
									<? } ?>
									<? if ($sagepay_transaction == 'DEFERRED') { ?>
										<option value="DEFERRED" selected="selected"><?= $text_defered; ?></option>
									<? } else { ?>
										<option value="DEFERRED"><?= $text_defered; ?></option>
									<? } ?>
									<? if ($sagepay_transaction == 'AUTHENTICATE') { ?>
										<option value="AUTHENTICATE" selected="selected"><?= $text_authenticate; ?></option>
									<? } else { ?>
										<option value="AUTHENTICATE"><?= $text_authenticate; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_total; ?></td>
							<td><input type="text" name="sagepay_total" value="<?= $sagepay_total; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_order_status; ?></td>
							<td><select name="sagepay_order_status_id">
									<? foreach ($order_statuses as $order_status) { ?>
										<? if ($order_status['order_status_id'] == $sagepay_order_status_id) { ?>
											<option value="<?= $order_status['order_status_id']; ?>"
											        selected="selected"><?= $order_status['name']; ?></option>
										<? } else { ?>
											<option
												value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_geo_zone; ?></td>
							<td><select name="sagepay_geo_zone_id">
									<option value="0"><?= $text_all_zones; ?></option>
									<? foreach ($geo_zones as $geo_zone) { ?>
										<? if ($geo_zone['geo_zone_id'] == $sagepay_geo_zone_id) { ?>
											<option value="<?= $geo_zone['geo_zone_id']; ?>"
											        selected="selected"><?= $geo_zone['name']; ?></option>
										<? } else { ?>
											<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="sagepay_status">
									<? if ($sagepay_status) { ?>
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
							<td><input type="text" name="sagepay_sort_order" value="<?= $sagepay_sort_order; ?>" size="1"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>