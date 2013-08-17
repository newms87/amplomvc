<?= $header; ?>
<div class="content">
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
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_rate; ?></td>
						<td><textarea name="parcelforce_48_rate" cols="40" rows="5"><?= $parcelforce_48_rate; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= $entry_insurance; ?></td>
						<td><textarea name="parcelforce_48_insurance" cols="40"
						              rows="5"><?= $parcelforce_48_insurance; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_display_weight; ?></td>
						<td><? if ($parcelforce_48_display_weight) { ?>
								<input type="radio" name="parcelforce_48_display_weight" value="1" checked="checked"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_weight" value="0"/>
								<?= $text_no; ?>
							<? } else { ?>
								<input type="radio" name="parcelforce_48_display_weight" value="1"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_weight" value="0" checked="checked"/>
								<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_display_insurance; ?></td>
						<td><? if ($parcelforce_48_display_insurance) { ?>
								<input type="radio" name="parcelforce_48_display_insurance" value="1" checked="checked"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_insurance" value="0"/>
								<?= $text_no; ?>
							<? } else { ?>
								<input type="radio" name="parcelforce_48_display_insurance" value="1"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_insurance" value="0" checked="checked"/>
								<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_display_time; ?></td>
						<td><? if ($parcelforce_48_display_time) { ?>
								<input type="radio" name="parcelforce_48_display_time" value="1" checked="checked"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_time" value="0"/>
								<?= $text_no; ?>
							<? } else { ?>
								<input type="radio" name="parcelforce_48_display_time" value="1"/>
								<?= $text_yes; ?>
								<input type="radio" name="parcelforce_48_display_time" value="0" checked="checked"/>
								<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_tax_class; ?></td>
						<td><select name="parcelforce_48_tax_class_id">
								<option value="0"><?= $text_none; ?></option>
								<? foreach ($tax_classes as $tax_class) { ?>
									<? if ($tax_class['tax_class_id'] == $parcelforce_48_tax_class_id) { ?>
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
						<td><select name="parcelforce_48_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
									<? if ($geo_zone['geo_zone_id'] == $parcelforce_48_geo_zone_id) { ?>
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
						<td><select name="parcelforce_48_status">
								<? if ($parcelforce_48_status) { ?>
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
						<td><input type="text" name="parcelforce_48_sort_order" value="<?= $parcelforce_48_sort_order; ?>"
						           size="1"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>