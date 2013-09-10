<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>
<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<div class="box">
<div class="heading">
	<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt=""/> <?= $head_title; ?></h1>

	<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
			href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
</div>
<div class="section">
<div class="vtabs"><a href="#tab-general"><?= $tab_general; ?></a><a
		href="#tab-1st-class-standard"><?= $tab_1st_class_standard; ?></a><a
		href="#tab-1st-class-recorded"><?= $tab_1st_class_recorded; ?></a><a
		href="#tab-2nd-class-standard"><?= $tab_2nd_class_standard; ?></a><a
		href="#tab-2nd-class-recorded"><?= $tab_2nd_class_recorded; ?></a><a
		href="#tab-special-delivery-500"><?= $tab_special_delivery_500; ?></a><a
		href="#tab-special-delivery-1000"><?= $tab_special_delivery_1000; ?></a><a
		href="#tab-special-delivery-2500"><?= $tab_special_delivery_2500; ?></a><a
		href="#tab-standard-parcels"><?= $tab_standard_parcels; ?></a><a href="#tab-airmail"><?= $tab_airmail; ?></a><a
		href="#tab-international-signed"><?= $tab_international_signed; ?></a><a
		href="#tab-airsure"><?= $tab_airsure; ?></a><a href="#tab-surface"><?= $tab_surface; ?></a></div>
<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
<div id="tab-general" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_display_weight; ?></td>
			<td><? if ($royal_mail_display_weight) { ?>
					<input type="radio" name="royal_mail_display_weight" value="1" checked="checked"/>
					<?= $text_yes; ?>
					<input type="radio" name="royal_mail_display_weight" value="0"/>
					<?= $text_no; ?>
				<? } else { ?>
					<input type="radio" name="royal_mail_display_weight" value="1"/>
					<?= $text_yes; ?>
					<input type="radio" name="royal_mail_display_weight" value="0" checked="checked"/>
					<?= $text_no; ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= $entry_display_insurance; ?></td>
			<td><? if ($royal_mail_display_insurance) { ?>
					<input type="radio" name="royal_mail_display_insurance" value="1" checked="checked"/>
					<?= $text_yes; ?>
					<input type="radio" name="royal_mail_display_insurance" value="0"/>
					<?= $text_no; ?>
				<? } else { ?>
					<input type="radio" name="royal_mail_display_insurance" value="1"/>
					<?= $text_yes; ?>
					<input type="radio" name="royal_mail_display_insurance" value="0" checked="checked"/>
					<?= $text_no; ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= $entry_weight_class; ?></td>
			<td><select name="royal_mail_weight_class_id">
					<? foreach ($weight_classes as $weight_class) { ?>
						<? if ($weight_class['weight_class_id'] == $royal_mail_weight_class_id) { ?>
							<option value="<?= $weight_class['weight_class_id']; ?>"
							        selected="selected"><?= $weight_class['title']; ?></option>
						<? } else { ?>
							<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= $entry_tax_class; ?></td>
			<td><select name="royal_mail_tax_class_id">
					<option value="0"><?= $text_none; ?></option>
					<? foreach ($tax_classes as $tax_class) { ?>
						<? if ($tax_class['tax_class_id'] == $royal_mail_tax_class_id) { ?>
							<option value="<?= $tax_class['tax_class_id']; ?>"
							        selected="selected"><?= $tax_class['title']; ?></option>
						<? } else { ?>
							<option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= $entry_geo_zone; ?></td>
			<td><select name="royal_mail_geo_zone_id">
					<option value="0"><?= $text_all_zones; ?></option>
					<? foreach ($geo_zones as $geo_zone) { ?>
						<? if ($geo_zone['geo_zone_id'] == $royal_mail_geo_zone_id) { ?>
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
			<td><select name="royal_mail_status">
					<? if ($royal_mail_status) { ?>
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
			<td><input type="text" name="royal_mail_sort_order" value="<?= $royal_mail_sort_order; ?>" size="1"/></td>
		</tr>
	</table>
</div>
<div id="tab-1st-class-standard" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_1st_class_standard_rate" cols="40"
			              rows="5"><?= $royal_mail_1st_class_standard_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_1st_class_standard_insurance" cols="40"
			              rows="5"><?= $royal_mail_1st_class_standard_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_1st_class_standard_status">
					<? if ($royal_mail_1st_class_standard_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-1st-class-recorded" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_1st_class_recorded_rate" cols="40"
			              rows="5"><?= $royal_mail_1st_class_recorded_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_1st_class_recorded_insurance" cols="40"
			              rows="5"><?= $royal_mail_1st_class_recorded_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_1st_class_recorded_status">
					<? if ($royal_mail_1st_class_recorded_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-2nd-class-standard" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_2nd_class_standard_rate" cols="40"
			              rows="5"><?= $royal_mail_2nd_class_standard_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_2nd_class_standard_status">
					<? if ($royal_mail_2nd_class_standard_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-2nd-class-recorded" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_2nd_class_recorded_rate" cols="40"
			              rows="5"><?= $royal_mail_2nd_class_recorded_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_2nd_class_recorded_insurance" cols="40"
			              rows="5"><?= $royal_mail_2nd_class_recorded_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_2nd_class_recorded_status">
					<? if ($royal_mail_2nd_class_recorded_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-special-delivery-500" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_special_delivery_500_rate" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_500_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_special_delivery_500_insurance" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_500_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_special_delivery_500_status">
					<? if ($royal_mail_special_delivery_500_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-special-delivery-1000" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_special_delivery_1000_rate" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_1000_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_special_delivery_1000_insurance" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_1000_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_special_delivery_1000_status">
					<? if ($royal_mail_special_delivery_1000_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-special-delivery-2500" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_special_delivery_2500_rate" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_2500_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_special_delivery_2500_insurance" cols="40"
			              rows="5"><?= $royal_mail_special_delivery_2500_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_special_delivery_2500_status">
					<? if ($royal_mail_special_delivery_2500_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-standard-parcels" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_standard_parcels_rate" cols="40"
			              rows="5"><?= $royal_mail_standard_parcels_rate; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_insurance; ?></td>
			<td><textarea name="royal_mail_standard_parcels_insurance" cols="40"
			              rows="5"><?= $royal_mail_standard_parcels_insurance; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_standard_parcels_status">
					<? if ($royal_mail_standard_parcels_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-airmail" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_airmail_rate_1; ?></td>
			<td><textarea name="royal_mail_airmail_rate_1" cols="40" rows="5"><?= $royal_mail_airmail_rate_1; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= $entry_airmail_rate_2; ?></td>
			<td><textarea name="royal_mail_airmail_rate_2" cols="40" rows="5"><?= $royal_mail_airmail_rate_2; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_airmail_status">
					<? if ($royal_mail_airmail_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-international-signed" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_international_signed_rate_1; ?></td>
			<td><textarea name="royal_mail_international_signed_rate_1" cols="40"
			              rows="5"><?= $royal_mail_international_signed_rate_1; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_international_signed_insurance_1; ?></td>
			<td><textarea name="royal_mail_international_signed_insurance_1" cols="40"
			              rows="5"><?= $royal_mail_international_signed_insurance_1; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_international_signed_rate_2; ?></td>
			<td><textarea name="royal_mail_international_signed_rate_2" cols="40"
			              rows="5"><?= $royal_mail_international_signed_rate_2; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_international_signed_insurance_2; ?></td>
			<td><textarea name="royal_mail_international_signed_insurance_2" cols="40"
			              rows="5"><?= $royal_mail_international_signed_insurance_2; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_international_signed_status">
					<? if ($royal_mail_international_signed_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-airsure" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_airsure_rate_1; ?></td>
			<td><textarea name="royal_mail_airsure_rate_1" cols="40" rows="5"><?= $royal_mail_airsure_rate_1; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= $entry_airsure_insurance_1; ?></td>
			<td><textarea name="royal_mail_airsure_insurance_1" cols="40"
			              rows="5"><?= $royal_mail_airsure_insurance_1; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_airsure_rate_2; ?></td>
			<td><textarea name="royal_mail_airsure_rate_2" cols="40" rows="5"><?= $royal_mail_airsure_rate_2; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= $entry_airsure_insurance_2; ?></td>
			<td><textarea name="royal_mail_airsure_insurance_2" cols="40"
			              rows="5"><?= $royal_mail_airsure_insurance_2; ?></textarea></td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_airsure_status">
					<? if ($royal_mail_airsure_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-surface" class="vtabs-content">
	<table class="form">
		<tr>
			<td><?= $entry_rate; ?></td>
			<td><textarea name="royal_mail_surface_rate" cols="40" rows="5"><?= $royal_mail_surface_rate; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= $entry_status; ?></td>
			<td><select name="royal_mail_surface_status">
					<? if ($royal_mail_surface_status) { ?>
						<option value="1" selected="selected"><?= $text_enabled; ?></option>
						<option value="0"><?= $text_disabled; ?></option>
					<? } else { ?>
						<option value="1"><?= $text_enabled; ?></option>
						<option value="0" selected="selected"><?= $text_disabled; ?></option>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
</form>
</div>
</div>
</div>
<script type="text/javascript"><!--
	$('.vtabs a').tabs();
//--></script>
<?= $footer; ?>