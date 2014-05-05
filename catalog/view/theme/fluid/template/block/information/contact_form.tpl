<form id="contact_form" action="<?= $action; ?>" method="post" enctype="multipart/form-data">
	<div class="section">
		<div class="cf_item">
			<label for="contact_name"><?= _l("Name"); ?></label>
			<input id="contact_name" type="text" name="name" value="<?= $name; ?>"/>
		</div>
		<div class="cf_item">
			<label for="contact_email"><?= _l("Email"); ?></label>
			<input id="contact_email" type="text" name="email" value="<?= $email; ?>"/>
		</div>
		<div class="cf_item">
			<label for="contact_enquiry"><?= _l("Enquiry"); ?></label>
			<textarea id="contact_enquiry" name="enquiry" cols="40" rows="10"><?= $enquiry; ?></textarea>
		</div>
		<div class="cf_item captcha">
			<label for="contact_captcha"><?= _l("Captcha"); ?></label>
			<img src="<?= $captcha_url; ?>" alt=""/>
			<input id="contact_captcha" type="text" name="captcha" value="<?= $captcha; ?>"/>
		</div>
	</div>

	<div class="buttons">
		<div class="right"><input type="submit" value="<?= _l("Submit"); ?>" class="button"/></div>
	</div>
</form>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>
