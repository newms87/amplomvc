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
				<div id="tab-general" class="page">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_merchant; ?></td>
							<td><input type="text" name="klarna_merchant" value="<?= $klarna_merchant; ?>"/>
								<? if ($error_merchant) { ?>
									<span class="error"><?= $error_merchant; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_secret; ?></td>
							<td><input type="text" name="klarna_secret" value="<?= $klarna_secret; ?>"/>
								<? if ($error_secret) { ?>
									<span class="error"><?= $error_secret; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_server; ?></td>
							<td><select name="klarna_server">
									<? if ($klarna_server == 'live') { ?>
										<option value="live" selected="selected"><?= $text_live; ?></option>
									<? } else { ?>
										<option value="live"><?= $text_live; ?></option>
									<? } ?>
									<? if ($klarna_server == 'beta') { ?>
										<option value="beta" selected="selected"><?= $text_beta; ?></option>
									<? } else { ?>
										<option value="beta"><?= $text_beta; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_test; ?></td>
							<td><? if ($klarna_test) { ?>
									<input type="radio" name="klarna_test" value="1" checked="checked"/>
									<?= $text_yes; ?>
									<input type="radio" name="klarna_test" value="0"/>
									<?= $text_no; ?>
								<? } else { ?>
									<input type="radio" name="klarna_test" value="1"/>
									<?= $text_yes; ?>
									<input type="radio" name="klarna_test" value="0" checked="checked"/>
									<?= $text_no; ?>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_invoice; ?></td>
							<td><? if ($klarna_invoice) { ?>
									<input type="radio" name="klarna_invoice" value="1" checked="checked"/>
									<?= $text_yes; ?>
									<input type="radio" name="klarna_invoice" value="0"/>
									<?= $text_no; ?>
								<? } else { ?>
									<input type="radio" name="klarna_invoice" value="1"/>
									<?= $text_yes; ?>
									<input type="radio" name="klarna_invoice" value="0" checked="checked"/>
									<?= $text_no; ?>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_invoice_delay; ?></td>
							<td><input type="text" name="klarna_invoice_delay" value="<?= $klarna_invoice_delay; ?>" size="1"/>
							</td>
						</tr>
						<tr>
							<td><?= $entry_order_status; ?></td>
							<td><select name="klarna_order_status_id">
									<? foreach ($order_statuses as $order_status) { ?>
										<? if ($order_status['order_status_id'] == $klarna_order_status_id) { ?>
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
							<td><?= $entry_status; ?></td>
							<td><select name="klarna_status">
									<? if ($klarna_status) { ?>
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
							<td><input type="text" name="klarna_sort_order" value="<?= $klarna_sort_order; ?>" size="1"/></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>