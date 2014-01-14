<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= _l("Tax Rates"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Tax Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/>
							<? if (_l("Tax Name must be between 3 and 32 characters!")) { ?>
								<span class="error"><?= _l("Tax Name must be between 3 and 32 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Tax Rate:"); ?></td>
						<td><input type="text" name="rate" value="<?= $rate; ?>"/>
							<? if (_l("Tax Rate required!")) { ?>
								<span class="error"><?= _l("Tax Rate required!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Type:"); ?></td>
						<td><select name="type">
								<? if ($type == 'P') { ?>
									<option value="P" selected="selected"><?= _l("Percentage"); ?></option>
								<? } else { ?>
									<option value="P"><?= _l("Percentage"); ?></option>
								<? } ?>
								<? if ($type == 'F') { ?>
									<option value="F" selected="selected"><?= _l("Fixed Amount"); ?></option>
								<? } else { ?>
									<option value="F"><?= _l("Fixed Amount"); ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= _l("Customer Group:"); ?></td>
						<td>
							<div class="scrollbox">
								<? $class = 'even'; ?>
								<? foreach ($customer_groups as $customer_group) { ?>
									<? $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?= $class; ?>">
										<? if (in_array($customer_group['customer_group_id'], $tax_rate_customer_group)) { ?>
											<input type="checkbox" name="tax_rate_customer_group[]" value="<?= $customer_group['customer_group_id']; ?>" checked="checked"/>
											<?= $customer_group['name']; ?>
										<? } else { ?>
											<input type="checkbox" name="tax_rate_customer_group[]" value="<?= $customer_group['customer_group_id']; ?>"/>
											<?= $customer_group['name']; ?>
										<? } ?>
									</div>
								<? } ?>
							</div>
						</td>
					</tr>
					<tr>
						<td><?= _l("Geo Zone:"); ?></td>
						<td><select name="geo_zone_id">
								<? foreach ($geo_zones as $geo_zone) { ?>
									<? if ($geo_zone['geo_zone_id'] == $geo_zone_id) { ?>
										<option value="<?= $geo_zone['geo_zone_id']; ?>"
											selected="selected"><?= $geo_zone['name']; ?></option>
									<? } else { ?>
										<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
									<? } ?>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>
