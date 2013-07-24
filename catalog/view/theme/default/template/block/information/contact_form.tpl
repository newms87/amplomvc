<form id='contact_form' action="<?= $action; ?>" method="post" enctype="multipart/form-data">
	<div class="section">
		<div class="cf_item">
			<label for="contact_name"><?= $entry_name; ?></label>
			<input id="contact_name" type="text" name="name" value="<?= $name; ?>" />
		</div>
		<div class="cf_item">
			<label for="contact_email"><?= $entry_email; ?></label>
			<input id="contact_email" type="text" name="email" value="<?= $email; ?>" />
		</div>
		<div class="cf_item">
			<label for="contact_enquiry"><?= $entry_enquiry; ?></label>
			<textarea id="contact_enquiry" name="enquiry" cols="40" rows="10"><?= $enquiry; ?></textarea>
		</div>
		<div class="cf_item captcha">
			<label for="contact_captcha"><?= $entry_captcha; ?></label>
			<img src="<?= $captcha_url; ?>" alt="" />
			<input id="contact_captcha" type="text" name="captcha" value="<?= $captcha; ?>" />
		</div>
	</div>
	
	<div class="buttons">
		<div class="right"><input type="submit" value="<?= $button_submit; ?>" class="button" /></div>
	</div>
</form>

<?= $this->builder->js('errors', $errors); ?>