<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'country.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/>
								<? if ($error_name) { ?>
									<span class="error"><?= $error_name; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_iso_code_2; ?></td>
							<td><input type="text" name="iso_code_2" value="<?= $iso_code_2; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_iso_code_3; ?></td>
							<td><input type="text" name="iso_code_3" value="<?= $iso_code_3; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_address_format; ?></td>
							<td><textarea name="address_format" cols="40" rows="5"><?= $address_format; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_postcode_required; ?></td>
							<td><? if ($postcode_required) { ?>
									<input type="radio" name="postcode_required" value="1" checked="checked"/>
									<?= $text_yes; ?>
									<input type="radio" name="postcode_required" value="0"/>
									<?= $text_no; ?>
								<? } else { ?>
									<input type="radio" name="postcode_required" value="1"/>
									<?= $text_yes; ?>
									<input type="radio" name="postcode_required" value="0" checked="checked"/>
									<?= $text_no; ?>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="status">
									<? if ($status) { ?>
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