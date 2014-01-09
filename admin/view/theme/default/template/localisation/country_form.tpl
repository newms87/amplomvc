<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'country.png'; ?>" alt=""/> <?= _l("Country"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Country Name:"); ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/>
								<? if (_l("Country Name must be between 3 and 128 characters!")) { ?>
									<span class="error"><?= _l("Country Name must be between 3 and 128 characters!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= _l("ISO Code (2):"); ?></td>
							<td><input type="text" name="iso_code_2" value="<?= $iso_code_2; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("ISO Code (3):"); ?></td>
							<td><input type="text" name="iso_code_3" value="<?= $iso_code_3; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Address Format:<br /><span class =\"help\">
First Name =
{firstname}<br />
Last Name = {lastname}<br />
Company = {company}<br />
Address 1 = {address_1}<br />
Address 2 = {address_2}<br />
City = {city}<br />
Postcode = {postcode}<br />
Zone = {zone}<br />
Zone Code = {zone_code}<br />
Country = {country}</span>"); ?></td>
							<td><textarea name="address_format" cols="40" rows="5"><?= $address_format; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Postcode Required:"); ?></td>
							<td><? if ($postcode_required) { ?>
									<input type="radio" name="postcode_required" value="1" checked="checked"/>
									<?= _l("Yes"); ?>
									<input type="radio" name="postcode_required" value="0"/>
									<?= _l("No"); ?>
								<? } else { ?>
									<input type="radio" name="postcode_required" value="1"/>
									<?= _l("Yes"); ?>
									<input type="radio" name="postcode_required" value="0" checked="checked"/>
									<?= _l("No"); ?>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
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
<?= $footer; ?>