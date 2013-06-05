<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_email; ?></td>
						<td><input type="text" name="pp_standard_email" value="<?= $pp_standard_email; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><? if ($pp_standard_test) { ?>
							<input type="radio" name="pp_standard_test" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="pp_standard_test" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="pp_standard_test" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="pp_standard_test" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_transaction; ?></td>
						<td><select name="pp_standard_transaction">
								<? if (!$pp_standard_transaction) { ?>
								<option value="0" selected="selected"><?= $text_authorization; ?></option>
								<? } else { ?>
								<option value="0"><?= $text_authorization; ?></option>
								<? } ?>
								<? if ($pp_standard_transaction) { ?>
								<option value="1" selected="selected"><?= $text_sale; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_sale; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_debug; ?></td>
						<td><select name="pp_standard_debug">
								<? if ($pp_standard_debug) { ?>
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
						<td><input type="text" name="pp_standard_total" value="<?= $pp_standard_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_page_style; ?></td>
						<td><input type="text" name="pp_standard_page_style" value="<?= $pp_standard_page_style; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_canceled_reversal_status; ?></td>
						<td><select name="pp_standard_canceled_reversal_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_canceled_reversal_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_completed_status; ?></td>
						<td><select name="pp_standard_completed_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_completed_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_denied_status; ?></td>
						<td><select name="pp_standard_denied_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_denied_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_expired_status; ?></td>
						<td><select name="pp_standard_expired_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_expired_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_failed_status; ?></td>
						<td><select name="pp_standard_failed_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_failed_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_pending_status; ?></td>
						<td><select name="pp_standard_pending_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_pending_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_processed_status; ?></td>
						<td><select name="pp_standard_processed_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_processed_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_refunded_status; ?></td>
						<td><select name="pp_standard_refunded_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_refunded_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_reversed_status; ?></td>
						<td><select name="pp_standard_reversed_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_reversed_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_voided_status; ?></td>
						<td><select name="pp_standard_voided_status_id">
								<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $pp_standard_voided_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="pp_standard_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $pp_standard_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="pp_standard_status">
								<? if ($pp_standard_status) { ?>
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
						<td><input type="text" name="pp_standard_sort_order" value="<?= $pp_standard_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?> 