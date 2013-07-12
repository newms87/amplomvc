<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_key; ?></td>
						<td><input type="text" name="ups_key" value="<?= $ups_key; ?>" />
							<? if ($error_key) { ?>
							<span class="error"><?= $error_key; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_username; ?></td>
						<td><input type="text" name="ups_username" value="<?= $ups_username; ?>" />
							<? if ($error_username) { ?>
							<span class="error"><?= $error_username; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_password; ?></td>
						<td><input type="text" name="ups_password" value="<?= $ups_password; ?>" />
							<? if ($error_password) { ?>
							<span class="error"><?= $error_password; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_pickup; ?></td>
						<td><select name="ups_pickup">
								<? foreach ($pickups as $pickup) { ?>
								<? if ($pickup['value'] == $ups_pickup) { ?>
								<option value="<?= $pickup['value']; ?>" selected="selected"><?= $pickup['text']; ?></option>
								<? } else { ?>
								<option value="<?= $pickup['value']; ?>"><?= $pickup['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_packaging; ?></td>
						<td><select name="ups_packaging">
								<? foreach ($packages as $package) { ?>
								<? if ($package['value'] == $ups_packaging) { ?>
								<option value="<?= $package['value']; ?>" selected="selected"><?= $package['text']; ?></option>
								<? } else { ?>
								<option value="<?= $package['value']; ?>"><?= $package['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_classification; ?></td>
						<td><select name="ups_classification">
								<? foreach ($classifications as $classification) { ?>
								<? if ($classification['value'] == $ups_classification) { ?>
								<option value="<?= $classification['value']; ?>" selected="selected"><?= $classification['text']; ?></option>
								<? } else { ?>
								<option value="<?= $classification['value']; ?>"><?= $classification['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_origin; ?></td>
						<td><select name="ups_origin">
								<? foreach ($origins as $origin) { ?>
								<? if ($origin['value'] == $ups_origin) { ?>
								<option value="<?= $origin['value']; ?>" selected="selected"><?= $origin['text']; ?></option>
								<? } else { ?>
								<option value="<?= $origin['value']; ?>"><?= $origin['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_city; ?></td>
						<td><input type="text" name="ups_city" value="<?= $ups_city; ?>" />
							<? if ($error_city) { ?>
							<span class="error"><?= $error_city; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_state; ?></td>
						<td><input type="text" name="ups_state" value="<?= $ups_state; ?>" maxlength="2" size="4" />
							<? if ($error_state) { ?>
							<span class="error"><?= $error_state; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_country; ?></td>
						<td><input type="text" name="ups_country" value="<?= $ups_country; ?>" maxlength="2" size="4" />
							<? if ($error_country) { ?>
							<span class="error"><?= $error_country; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_postcode; ?></td>
						<td><input type="text" name="ups_postcode" value="<?= $ups_postcode; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><? if ($ups_test) { ?>
							<input type="radio" name="ups_test" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_test" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="ups_test" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_test" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_quote_type; ?></td>
						<td><select name="ups_quote_type">
								<? foreach ($quote_types as $quote_type) { ?>
								<? if ($quote_type['value'] == $ups_quote_type) { ?>
								<option value="<?= $quote_type['value']; ?>" selected="selected"><?= $quote_type['text']; ?></option>
								<? } else { ?>
								<option value="<?= $quote_type['value']; ?>"><?= $quote_type['text']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_service; ?></td>
						<td id="service"><div id="US">
								<div class="scrollbox">
									<? $class = 'odd'; ?>
									<div class="even">
										<? if ($ups_us_01) { ?>
										<input type="checkbox" name="ups_us_01" value="1" checked="checked" />
										<?= $text_next_day_air; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_01" value="1" />
										<?= $text_next_day_air; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_us_02) { ?>
										<input type="checkbox" name="ups_us_02" value="1" checked="checked" />
										<?= $text_2nd_day_air; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_02" value="1" />
										<?= $text_2nd_day_air; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_03) { ?>
										<input type="checkbox" name="ups_us_03" value="1" checked="checked" />
										<?= $text_ground; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_03" value="1" />
										<?= $text_ground; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_us_07) { ?>
										<input type="checkbox" name="ups_us_07" value="1" checked="checked" />
										<?= $text_worldwide_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_07" value="1" />
										<?= $text_worldwide_express; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_08) { ?>
										<input type="checkbox" name="ups_us_08" value="1" checked="checked" />
										<?= $text_worldwide_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_08" value="1" />
										<?= $text_worldwide_expedited; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_us_11) { ?>
										<input type="checkbox" name="ups_us_11" value="1" checked="checked" />
										<?= $text_standard; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_11" value="1" />
										<?= $text_standard; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_12) { ?>
										<input type="checkbox" name="ups_us_12" value="1" checked="checked" />
										<?= $text_3_day_select; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_12" value="1" />
										<?= $text_3_day_select; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_13) { ?>
										<input type="checkbox" name="ups_us_13" value="1" checked="checked" />
										<?= $text_next_day_air_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_13" value="1" />
										<?= $text_next_day_air_saver; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_us_14) { ?>
										<input type="checkbox" name="ups_us_14" value="1" checked="checked" />
										<?= $text_next_day_air_early_am; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_14" value="1" />
										<?= $text_next_day_air_early_am; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_54) { ?>
										<input type="checkbox" name="ups_us_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_us_59) { ?>
										<input type="checkbox" name="ups_us_59" value="1" checked="checked" />
										<?= $text_2nd_day_air_am; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_59" value="1" />
										<?= $text_2nd_day_air_am; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_us_65) { ?>
										<input type="checkbox" name="ups_us_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_us_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<div id="PR">
								<div class="scrollbox">
									<div class="even">
										<? if ($ups_pr_01) { ?>
										<input type="checkbox" name="ups_pr_01" value="1" checked="checked" />
										<?= $text_next_day_air; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_01" value="1" />
										<?= $text_next_day_air; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_pr_02) { ?>
										<input type="checkbox" name="ups_pr_02" value="1" checked="checked" />
										<?= $text_2nd_day_air; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_02" value="1" />
										<?= $text_2nd_day_air; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_pr_03) { ?>
										<input type="checkbox" name="ups_pr_03" value="1" checked="checked" />
										<?= $text_ground; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_03" value="1" />
										<?= $text_ground; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_pr_07) { ?>
										<input type="checkbox" name="ups_pr_07" value="1" checked="checked" />
										<?= $text_worldwide_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_07" value="1" />
										<?= $text_worldwide_express; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_pr_08) { ?>
										<input type="checkbox" name="ups_pr_08" value="1" checked="checked" />
										<?= $text_worldwide_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_08" value="1" />
										<?= $text_worldwide_expedited; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_pr_14) { ?>
										<input type="checkbox" name="ups_pr_14" value="1" checked="checked" />
										<?= $text_next_day_air_early_am; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_14" value="1" />
										<?= $text_next_day_air_early_am; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_pr_54) { ?>
										<input type="checkbox" name="ups_pr_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_pr_65) { ?>
										<input type="checkbox" name="ups_pr_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_pr_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<div id="CA">
								<div class="scrollbox">
									<div class="even">
										<? if ($ups_ca_01) { ?>
										<input type="checkbox" name="ups_ca_01" value="1" checked="checked" />
										<?= $text_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_01" value="1" />
										<?= $text_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_ca_02) { ?>
										<input type="checkbox" name="ups_ca_02" value="1" checked="checked" />
										<?= $text_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_02" value="1" />
										<?= $text_expedited; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_ca_07) { ?>
										<input type="checkbox" name="ups_ca_07" value="1" checked="checked" />
										<?= $text_worldwide_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_07" value="1" />
										<?= $text_worldwide_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_ca_08) { ?>
										<input type="checkbox" name="ups_ca_08" value="1" checked="checked" />
										<?= $text_worldwide_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_08" value="1" />
										<?= $text_worldwide_expedited; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_ca_11) { ?>
										<input type="checkbox" name="ups_ca_11" value="1" checked="checked" />
										<?= $text_standard; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_11" value="1" />
										<?= $text_standard; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_ca_12) { ?>
										<input type="checkbox" name="ups_ca_12" value="1" checked="checked" />
										<?= $text_3_day_select; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_12" value="1" />
										<?= $text_3_day_select; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_ca_13) { ?>
										<input type="checkbox" name="ups_ca_13" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_13" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_ca_14) { ?>
										<input type="checkbox" name="ups_ca_14" value="1" checked="checked" />
										<?= $text_express_early_am; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_14" value="1" />
										<?= $text_express_early_am; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_ca_54) { ?>
										<input type="checkbox" name="ups_ca_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_ca_65) { ?>
										<input type="checkbox" name="ups_ca_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_ca_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<div id="MX">
								<div class="scrollbox">
									<div class="even">
										<? if ($ups_mx_07) { ?>
										<input type="checkbox" name="ups_mx_07" value="1" checked="checked" />
										<?= $text_worldwide_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_mx_07" value="1" />
										<?= $text_worldwide_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_mx_08) { ?>
										<input type="checkbox" name="ups_mx_08" value="1" checked="checked" />
										<?= $text_worldwide_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_mx_08" value="1" />
										<?= $text_worldwide_expedited; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_mx_54) { ?>
										<input type="checkbox" name="ups_mx_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_mx_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_mx_65) { ?>
										<input type="checkbox" name="ups_mx_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_mx_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<div id="EU">
								<div class="scrollbox">
									<div class="even">
										<? if ($ups_eu_07) { ?>
										<input type="checkbox" name="ups_eu_07" value="1" checked="checked" />
										<?= $text_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_07" value="1" />
										<?= $text_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_eu_08) { ?>
										<input type="checkbox" name="ups_eu_08" value="1" checked="checked" />
										<?= $text_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_08" value="1" />
										<?= $text_expedited; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_eu_11) { ?>
										<input type="checkbox" name="ups_eu_11" value="1" checked="checked" />
										<?= $text_standard; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_11" value="1" />
										<?= $text_standard; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_eu_54) { ?>
										<input type="checkbox" name="ups_eu_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_eu_65) { ?>
										<input type="checkbox" name="ups_eu_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_eu_82) { ?>
										<input type="checkbox" name="ups_eu_82" value="1" checked="checked" />
										<?= $text_today_standard; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_82" value="1" />
										<?= $text_today_standard; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_eu_83) { ?>
										<input type="checkbox" name="ups_eu_83" value="1" checked="checked" />
										<?= $text_today_dedicated_courier; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_83" value="1" />
										<?= $text_today_dedicated_courier; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_eu_84) { ?>
										<input type="checkbox" name="ups_eu_84" value="1" checked="checked" />
										<?= $text_today_intercity; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_84" value="1" />
										<?= $text_today_intercity; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_eu_85) { ?>
										<input type="checkbox" name="ups_eu_85" value="1" checked="checked" />
										<?= $text_today_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_85" value="1" />
										<?= $text_today_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_eu_86) { ?>
										<input type="checkbox" name="ups_eu_86" value="1" checked="checked" />
										<?= $text_today_express_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_eu_86" value="1" />
										<?= $text_today_express_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<div id="other">
								<div class="scrollbox">
									<div class="even">
										<? if ($ups_other_07) { ?>
										<input type="checkbox" name="ups_other_07" value="1" checked="checked" />
										<?= $text_express; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_other_07" value="1" />
										<?= $text_express; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_other_08) { ?>
										<input type="checkbox" name="ups_other_08" value="1" checked="checked" />
										<?= $text_expedited; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_other_08" value="1" />
										<?= $text_expedited; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_other_11) { ?>
										<input type="checkbox" name="ups_other_11" value="1" checked="checked" />
										<?= $text_standard; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_other_11" value="1" />
										<?= $text_standard; ?>
										<? } ?>
									</div>
									<div class="odd">
										<? if ($ups_other_54) { ?>
										<input type="checkbox" name="ups_other_54" value="1" checked="checked" />
										<?= $text_worldwide_express_plus; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_other_54" value="1" />
										<?= $text_worldwide_express_plus; ?>
										<? } ?>
									</div>
									<div class="even">
										<? if ($ups_other_65) { ?>
										<input type="checkbox" name="ups_other_65" value="1" checked="checked" />
										<?= $text_saver; ?>
										<? } else { ?>
										<input type="checkbox" name="ups_other_65" value="1" />
										<?= $text_saver; ?>
										<? } ?>
									</div>
								</div>
							</div>
							<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a></td>
					</tr>
					<tr>
						<td><?= $entry_insurance; ?></td>
						<td><? if ($ups_insurance) { ?>
							<input type="radio" name="ups_insurance" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_insurance" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="ups_insurance" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_insurance" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_display_weight; ?></td>
						<td><? if ($ups_display_weight) { ?>
							<input type="radio" name="ups_display_weight" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_display_weight" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="ups_display_weight" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="ups_display_weight" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_weight_code; ?></td>
						<td><select name="ups_weight_code">
								<? if ($ups_weight_code == 'LBS') { ?>
								<option value="LBS" selected="selected">LBS</option>
								<option value="KGS">KGS</option>
								<? } else { ?>
								<option value="LBS">LBS</option>
								<option value="KGS" selected="selected">KGS</option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_weight_class; ?></td>
						<td><select name="ups_weight_class_id">
								<? foreach ($weight_classes as $weight_class) { ?>
								<? if ($weight_class['weight_class_id'] == $ups_weight_class_id) { ?>
								<option value="<?= $weight_class['weight_class_id']; ?>" selected="selected"><?= $weight_class['title']; ?></option>
								<? } else { ?>
								<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_length_code; ?></td>
						<td><select name="ups_length_code">
								<? if ($ups_length_code == 'CM') { ?>
								<option value="CM" selected="selected">CM</option>
								<option value="IN">IN</option>
								<? } else { ?>
								<option value="CM">CM</option>
								<option value="IN" selected="selected">IN</option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_length_class; ?></td>
						<td><select name="ups_length_class">
								<? foreach ($length_classes as $length_class) { ?>
								<? if ($length_class['unit'] == $ups_length_class) { ?>
								<option value="<?= $length_class['unit']; ?>" selected="selected"><?= $length_class['title']; ?></option>
								<? } else { ?>
								<option value="<?= $length_class['unit']; ?>"><?= $length_class['title']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_dimension; ?></td>
						<td><input type="text" name="ups_length" value="<?= $ups_length; ?>" size="4" />
							<input type="text" name="ups_width" value="<?= $ups_width; ?>" size="4" />
							<input type="text" name="ups_height" value="<?= $ups_height; ?>" size="4" /></td>
				<? if ($error_dimension) { ?>
							<span class="error"><?= $error_dimension; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_tax_class; ?></td>
						<td><select name="ups_tax_class_id">
								<option value="0"><?= $text_none; ?></option>
								<? foreach ($tax_classes as $tax_class) { ?>
								<? if ($tax_class['tax_class_id'] == $ups_tax_class_id) { ?>
								<option value="<?= $tax_class['tax_class_id']; ?>" selected="selected"><?= $tax_class['title']; ?></option>
								<? } else { ?>
								<option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="ups_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
								<? if ($geo_zone['geo_zone_id'] == $ups_geo_zone_id) { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
								<? } else { ?>
								<option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="ups_status">
								<? if ($ups_status) { ?>
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
						<td><input type="text" name="ups_sort_order" value="<?= $ups_sort_order; ?>" size="1" /></td>
					</tr>
			<tr>
						<td><?= $entry_debug; ?></td>
						<td><select name="ups_debug">
							<? if ($ups_debug) { ?>
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
<script type="text/javascript"><!--
$('select[name=\'ups_origin\']').bind('change', function() {
	$('#service > div').hide();
										
	$('#' + this.value).show();
});

$('select[name=\'ups_origin\']').trigger('change');
//--></script>
<?= $footer; ?>