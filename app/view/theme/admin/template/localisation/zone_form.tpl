<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Zones}}</h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button">{{Save}}</a><a
					href="<?= $cancel; ?>" class="button">{{Cancel}}</a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> {{Zone Name:}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/>
							<? if (_l("Zone Name must be between 3 and 128 characters!")) { ?>
								<span class="error">{{Zone Name must be between 3 and 128 characters!}}</span>
							<? } ?></td>
					</tr>
					<tr>
						<td>{{Zone Code:}}</td>
						<td><input type="text" name="code" value="<?= $code; ?>"/></td>
					</tr>
					<tr>
						<td>{{Country:}}</td>
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
						<td>{{Zone Status:}}</td>
						<td><select name="status">
								<? if ($status) { ?>
									<option value="1" selected="selected">{{Enabled}}</option>
									<option value="0">{{Disabled}}</option>
								<? } else { ?>
									<option value="1">{{Enabled}}</option>
									<option value="0" selected="selected">{{Disabled}}</option>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
