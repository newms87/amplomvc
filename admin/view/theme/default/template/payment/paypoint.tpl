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
							<td class="required"> <?= $entry_merchant; ?></td>
							<td><input type="text" name="paypoint_merchant" value="<?= $paypoint_merchant; ?>"/>
								<? if ($error_merchant) { ?>
									<span class="error"><?= $error_merchant; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_password; ?></td>
							<td><input type="text" name="paypoint_password" value="<?= $paypoint_password; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_test; ?></td>
							<td><select name="paypoint_test">
									<? if ($paypoint_test == 'live') { ?>
										<option value="live" selected="selected"><?= $text_live; ?></option>
									<? } else { ?>
										<option value="live"><?= $text_live; ?></option>
									<? } ?>
									<? if ($paypoint_test == 'successful') { ?>
										<option value="successful" selected="selected"><?= $text_successful; ?></option>
									<? } else { ?>
										<option value="successful"><?= $text_successful; ?></option>
									<? } ?>
									<? if ($paypoint_test == 'fail') { ?>
										<option value="fail" selected="selected"><?= $text_fail; ?></option>
									<? } else { ?>
										<option value="fail"><?= $text_fail; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_total; ?></td>
							<td><input type="text" name="paypoint_total" value="<?= $paypoint_total; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_order_status; ?></td>
							<td><select name="paypoint_order_status_id">
									<? foreach ($order_statuses as $order_status) { ?>
										<? if ($order_status['order_status_id'] == $paypoint_order_status_id) { ?>
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
							<td><select name="paypoint_geo_zone_id">
									<option value="0"><?= $text_all_zones; ?></option>
									<? foreach ($geo_zones as $geo_zone) { ?>
										<? if ($geo_zone['geo_zone_id'] == $paypoint_geo_zone_id) { ?>
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
							<td><select name="paypoint_status">
									<? if ($paypoint_status) { ?>
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
							<td><input type="text" name="paypoint_sort_order" value="<?= $paypoint_sort_order; ?>" size="1"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>