<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div class="vtabs"><a href="#tab-general"><?= $tab_general; ?></a>
				<? foreach ($geo_zones as $geo_zone) { ?>
				<a href="#tab-geo-zone<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></a>
				<? } ?>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general" class="vtabs-content">
					<table class="form">
						<tr>
							<td><?= $entry_tax_class; ?></td>
							<td><select name="weight_tax_class_id">
									<option value="0"><?= $text_none; ?></option>
									<? foreach ($tax_classes as $tax_class) { ?>
									<? if ($tax_class['tax_class_id'] == $weight_tax_class_id) { ?>
									<option value="<?= $tax_class['tax_class_id']; ?>" selected="selected"><?= $tax_class['title']; ?></option>
									<? } else { ?>
									<option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
									<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="weight_status">
									<? if ($weight_status) { ?>
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
							<td><input type="text" name="weight_sort_order" value="<?= $weight_sort_order; ?>" size="1" /></td>
						</tr>
					</table>
				</div>
				<? foreach ($geo_zones as $geo_zone) { ?>
				<div id="tab-geo-zone<?= $geo_zone['geo_zone_id']; ?>" class="vtabs-content">
					<table class="form">
						<tr>
							<td><?= $entry_rate; ?></td>
							<td><textarea name="weight_<?= $geo_zone['geo_zone_id']; ?>_rate" cols="40" rows="5"><?= ${'weight_' . $geo_zone['geo_zone_id'] . '_rate'}; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="weight_<?= $geo_zone['geo_zone_id']; ?>_status">
									<? if (${'weight_' . $geo_zone['geo_zone_id'] . '_status'}) { ?>
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
				<? } ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>
<?= $footer; ?> 