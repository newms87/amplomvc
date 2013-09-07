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
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="free_total" value="<?= $free_total; ?>"/></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td><select name="free_geo_zone_id">
								<option value="0"><?= $text_all_zones; ?></option>
								<? foreach ($geo_zones as $geo_zone) { ?>
									<? if ($geo_zone['geo_zone_id'] == $free_geo_zone_id) { ?>
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
						<td><select name="free_status">
								<? if ($free_status) { ?>
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
						<td><input type="text" name="free_sort_order" value="<?= $free_sort_order; ?>" size="1"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>