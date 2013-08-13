<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_user_id; ?></td>
						<td><input type="text" name="usps_user_id" value="<?= $usps_user_id; ?>" />
							<? if ($error_user_id) { ?>
							<span class="error"><?= $error_user_id; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_postcode; ?></td>
						<td><input type="text" name="usps_postcode" value="<?= $usps_postcode; ?>" />
							<? if ($error_postcode) { ?>
							<span class="error"><?= $error_postcode; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_domestic; ?></td>
						<td><div class="scrollbox">
								<? $class = 'odd'; ?>
								<div class="even">
									<? if ($usps_domestic_00) { ?>
									<input type="checkbox" name="usps_domestic_00" value="1" checked="checked" />
									<?= $text_domestic_00; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_00" value="1" />
									<?= $text_domestic_00; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_01) { ?>
									<input type="checkbox" name="usps_domestic_01" value="1" checked="checked" />
									<?= $text_domestic_01; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_01" value="1" />
									<?= $text_domestic_01; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_02) { ?>
									<input type="checkbox" name="usps_domestic_02" value="1" checked="checked" />
									<?= $text_domestic_02; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_02" value="1" />
									<?= $text_domestic_02; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_03) { ?>
									<input type="checkbox" name="usps_domestic_03" value="1" checked="checked" />
									<?= $text_domestic_03; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_03" value="1" />
									<?= $text_domestic_03; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_1) { ?>
									<input type="checkbox" name="usps_domestic_1" value="1" checked="checked" />
									<?= $text_domestic_1; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_1" value="1" />
									<?= $text_domestic_1; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_2) { ?>
									<input type="checkbox" name="usps_domestic_2" value="1" checked="checked" />
									<?= $text_domestic_2; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_2" value="1" />
									<?= $text_domestic_2; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_3) { ?>
									<input type="checkbox" name="usps_domestic_3" value="1" checked="checked" />
									<?= $text_domestic_3; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_3" value="1" />
									<?= $text_domestic_3; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_4) { ?>
									<input type="checkbox" name="usps_domestic_4" value="1" checked="checked" />
									<?= $text_domestic_4; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_4" value="1" />
									<?= $text_domestic_4; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_5) { ?>
									<input type="checkbox" name="usps_domestic_5" value="1" checked="checked" />
									<?= $text_domestic_5; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_5" value="1" />
									<?= $text_domestic_5; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_6) { ?>
									<input type="checkbox" name="usps_domestic_6" value="1" checked="checked" />
									<?= $text_domestic_6; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_6" value="1" />
									<?= $text_domestic_6; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_7) { ?>
									<input type="checkbox" name="usps_domestic_7" value="1" checked="checked" />
									<?= $text_domestic_7; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_7" value="1" />
									<?= $text_domestic_7; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_12) { ?>
									<input type="checkbox" name="usps_domestic_12" value="1" checked="checked" />
									<?= $text_domestic_12; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_12" value="1" />
									<?= $text_domestic_12; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_13) { ?>
									<input type="checkbox" name="usps_domestic_13" value="1" checked="checked" />
									<?= $text_domestic_13; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_13" value="1" />
									<?= $text_domestic_13; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_16) { ?>
									<input type="checkbox" name="usps_domestic_16" value="1" checked="checked" />
									<?= $text_domestic_16; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_16" value="1" />
									<?= $text_domestic_16; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_17) { ?>
									<input type="checkbox" name="usps_domestic_17" value="1" checked="checked" />
									<?= $text_domestic_17; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_17" value="1" />
									<?= $text_domestic_17; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_18) { ?>
									<input type="checkbox" name="usps_domestic_18" value="1" checked="checked" />
									<?= $text_domestic_18; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_18" value="1" />
									<?= $text_domestic_18; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_19) { ?>
									<input type="checkbox" name="usps_domestic_19" value="1" checked="checked" />
									<?= $text_domestic_19; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_19" value="1" />
									<?= $text_domestic_19; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_22) { ?>
									<input type="checkbox" name="usps_domestic_22" value="1" checked="checked" />
									<?= $text_domestic_22; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_22" value="1" />
									<?= $text_domestic_22; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_23) { ?>
									<input type="checkbox" name="usps_domestic_23" value="1" checked="checked" />
									<?= $text_domestic_23; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_23" value="1" />
									<?= $text_domestic_23; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_25) { ?>
									<input type="checkbox" name="usps_domestic_25" value="1" checked="checked" />
									<?= $text_domestic_25; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_25" value="1" />
									<?= $text_domestic_25; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_domestic_27) { ?>
									<input type="checkbox" name="usps_domestic_27" value="1" checked="checked" />
									<?= $text_domestic_27; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_27" value="1" />
									<?= $text_domestic_27; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_domestic_28) { ?>
									<input type="checkbox" name="usps_domestic_28" value="1" checked="checked" />
									<?= $text_domestic_28; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_domestic_28" value="1" />
									<?= $text_domestic_28; ?>
									<? } ?>
								</div>
							</div>
							<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a></td>
					</tr>
					<tr>
						<td><?= $entry_international; ?></td>
						<td><div class="scrollbox">
								<? $class = 'odd'; ?>
								<div class="even">
									<? if ($usps_international_1) { ?>
									<input type="checkbox" name="usps_international_1" value="1" checked="checked" />
									<?= $text_international_1; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_1" value="1" />
									<?= $text_international_1; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_2) { ?>
									<input type="checkbox" name="usps_international_2" value="1" checked="checked" />
									<?= $text_international_2; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_2" value="1" />
									<?= $text_international_2; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_4) { ?>
									<input type="checkbox" name="usps_international_4" value="1" checked="checked" />
									<?= $text_international_4; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_4" value="1" />
									<?= $text_international_4; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_5) { ?>
									<input type="checkbox" name="usps_international_5" value="1" checked="checked" />
									<?= $text_international_5; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_5" value="1" />
									<?= $text_international_5; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_6) { ?>
									<input type="checkbox" name="usps_international_6" value="1" checked="checked" />
									<?= $text_international_6; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_6" value="1" />
									<?= $text_international_6; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_7) { ?>
									<input type="checkbox" name="usps_international_7" value="1" checked="checked" />
									<?= $text_international_7; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_7" value="1" />
									<?= $text_international_7; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_8) { ?>
									<input type="checkbox" name="usps_international_8" value="1" checked="checked" />
									<?= $text_international_8; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_8" value="1" />
									<?= $text_international_8; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_9) { ?>
									<input type="checkbox" name="usps_international_9" value="1" checked="checked" />
									<?= $text_international_9; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_9" value="1" />
									<?= $text_international_9; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_10) { ?>
									<input type="checkbox" name="usps_international_10" value="1" checked="checked" />
									<?= $text_international_10; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_10" value="1" />
									<?= $text_international_10; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_11) { ?>
									<input type="checkbox" name="usps_international_11" value="1" checked="checked" />
									<?= $text_international_11; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_11" value="1" />
									<?= $text_international_11; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_12) { ?>
									<input type="checkbox" name="usps_international_12" value="1" checked="checked" />
									<?= $text_international_12; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_12" value="1" />
									<?= $text_international_12; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_13) { ?>
									<input type="checkbox" name="usps_international_13" value="1" checked="checked" />
									<?= $text_international_13; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_13" value="1" />
									<?= $text_international_13; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_14) { ?>
									<input type="checkbox" name="usps_international_14" value="1" checked="checked" />
									<?= $text_international_14; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_14" value="1" />
									<?= $text_international_14; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_15) { ?>
									<input type="checkbox" name="usps_international_15" value="1" checked="checked" />
									<?= $text_international_15; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_15" value="1" />
									<?= $text_international_15; ?>
									<? } ?>
								</div>
								<div class="even">
									<? if ($usps_international_16) { ?>
									<input type="checkbox" name="usps_international_16" value="1" checked="checked" />
									<?= $text_international_16; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_16" value="1" />
									<?= $text_international_16; ?>
									<? } ?>
								</div>
								<div class="odd">
									<? if ($usps_international_21) { ?>
									<input type="checkbox" name="usps_international_21" value="1" checked="checked" />
									<?= $text_international_21; ?>
									<? } else { ?>
									<input type="checkbox" name="usps_international_21" value="1" />
									<?= $text_international_21; ?>
									<? } ?>
								</div>
							</div>
							<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a></td>
					</tr>
					<tr>
						<td><?= $entry_size; ?></td>
						<td><select name="usps_size">
								<? foreach ($sizes as $size) { ?>
								<? if ($size['value'] == $usps_size) { ?>
								<option value="<?= $size['value']; ?>" selected="selected"><?= $size['text']; ?></option>
								<? } else { ?>
								<option value="<?= $size['value']; ?>"><?= $size['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_container; ?></td>
						<td><select name="usps_container">
								<? foreach ($containers as $container) { ?>
								<? if ($container['value'] == $usps_container) { ?>
								<option value="<?= $container['value']; ?>" selected="selected"><?= $container['text']; ?></option>
								<? } else { ?>
								<option value="<?= $container['value']; ?>"><?= $container['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_machinable; ?></td>
						<td><select name="usps_machinable">
								<? if ($usps_machinable) { ?>
								<option value="1" selected="selected"><?= $text_yes; ?></option>
								<option value="0"><?= $text_no; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_yes; ?></option>
								<option value="0" selected="selected"><?= $text_no; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_dimension; ?></td>
						<td>
					<input type="text" name="usps_length" value="<?= $usps_length; ?>" size="4" />
							<input type="text" name="usps_width" value="<?= $usps_width; ?>" size="4" />
							<input type="text" name="usps_height" value="<?= $usps_height; ?>" size="4" />
				<? if ($error_width) { ?>
							<span class="error"><?= $error_width; ?></span>
							<? } ?>
				<? if ($error_length) { ?>
							<span class="error"><?= $error_length; ?></span>
							<? } ?>
				<? if ($error_height) { ?>
							<span class="error"><?= $error_height; ?></span>
							<? } ?>
				</td>
					</tr>
			<tr>
						<td><?= $entry_display_time; ?></td>
						<td><? if ($usps_display_time) { ?>
							<input type="radio" name="usps_display_time" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="usps_display_time" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="usps_display_time" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="usps_display_time" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_display_weight; ?></td>
						<td><? if ($usps_display_weight) { ?>
							<input type="radio" name="usps_display_weight" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="usps_display_weight" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="usps_display_weight" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="usps_display_weight" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_weight_class; ?></td>
						<td><select name="usps_weight_class_id">
								<? foreach ($weight_classes as $weight_class) { ?>
								<? if ($weight_class['weight_class_id'] == $usps_weight_class_id) { ?>
								<option value="<?= $weight_class['weight_class_id']; ?>" selected="selected"><?= $weight_class['title']; ?></option>
								<? } else { ?>
								<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_tax; ?></td>
						<td><select name="usps_tax_class_id">
								<option value="0"><?= $text_none; ?></option>
								<? foreach ($tax_classes as $tax_class) { ?>
								<? if ($tax_class['tax_class_id'] == $usps_tax_class_id) { ?>
								<option value="<?= $tax_class['tax_class_id']; ?>" selected="selected"><?= $tax_class['title']; ?></option>
								<? } else { ?>
								<option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="usps_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $usps_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="usps_status">
								<? if ($usps_status) { ?>
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
						<td><input type="text" name="usps_sort_order" value="<?= $usps_sort_order; ?>" size="1" /></td>
					</tr>
			<tr>
						<td><?= $entry_debug; ?></td>
						<td><select name="usps_debug">
							<? if ($usps_debug) { ?>
							<option value="1" selected="selected"><?= $text_enabled; ?></option>
							<option value="0"><?= $text_disabled; ?></option>
							<? } else { ?>
							<option value="1"><?= $text_enabled; ?></option>
							<option value="0" selected="selected"><?= $text_disabled; ?></option>
							<? } ?>
						</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>