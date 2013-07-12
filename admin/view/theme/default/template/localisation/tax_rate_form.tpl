<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" />
							<? if ($error_name) { ?>
							<span class="error"><?= $error_name; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_rate; ?></td>
						<td><input type="text" name="rate" value="<?= $rate; ?>" />
							<? if ($error_rate) { ?>
							<span class="error"><?= $error_rate; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_type; ?></td>
						<td><select name="type">
								<? if ($type == 'P') { ?>
								<option value="P" selected="selected"><?= $text_percent; ?></option>
								<? } else { ?>
								<option value="P"><?= $text_percent; ?></option>
								<? } ?>
								<? if ($type == 'F') { ?>
								<option value="F" selected="selected"><?= $text_amount; ?></option>
								<? } else { ?>
								<option value="F"><?= $text_amount; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_customer_group; ?></td>
						<td><div class="scrollbox">
								<? $class = 'even'; ?>
								<? foreach ($customer_groups as $customer_group) { ?>
								<? $class = ($class == 'even' ? 'odd' : 'even'); ?>
								<div class="<?= $class; ?>">
									<? if (in_array($customer_group['customer_group_id'], $tax_rate_customer_group)) { ?>
									<input type="checkbox" name="tax_rate_customer_group[]" value="<?= $customer_group['customer_group_id']; ?>" checked="checked" />
									<?= $customer_group['name']; ?>
									<? } else { ?>
									<input type="checkbox" name="tax_rate_customer_group[]" value="<?= $customer_group['customer_group_id']; ?>" />
									<?= $customer_group['name']; ?>
									<? } ?>
								</div>
								<? } ?>
							</div></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="geo_zone_id">
								<? foreach ($geo_zones as $geo_zone) { ?>
								<?	if ($geo_zone['geo_zone_id'] == $geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
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