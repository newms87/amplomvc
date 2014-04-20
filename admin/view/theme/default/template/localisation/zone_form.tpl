<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'country.png'; ?>" alt=""/> <?= _l("Zones"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Zone Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/>
							<? if (_l("Zone Name must be between 3 and 128 characters!")) { ?>
								<span class="error"><?= _l("Zone Name must be between 3 and 128 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Zone Code:"); ?></td>
						<td><input type="text" name="code" value="<?= $code; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Country:"); ?></td>
						<td><select name="country_id">
								<? foreach ($countries as $country) { ?>
									<? if ($country['country_id'] == $country_id) { ?>
										<option value="<?= $country['country_id']; ?>"
											selected="selected"><?= $country['name']; ?></option>
									<? } else { ?>
										<option value="<?= $country['country_id']; ?>"><?= $country['name']; ?></option>
									<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= _l("Zone Status:"); ?></td>
						<td><select name="status">
								<? if ($status) { ?>
									<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
									<option value="0"><?= _l("Disabled"); ?></option>
								<? } else { ?>
									<option value="1"><?= _l("Enabled"); ?></option>
									<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $this->call('common/footer'); ?>
