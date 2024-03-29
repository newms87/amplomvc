<div class="block block-information-contact">
	<? if (!empty($show_block_title)) { ?>
		<div class="box_heading">{{Contact Form}}</div>
	<? } ?>

	<form id="contact-form" action="<?= $action; ?>" method="post" class="form col xs-12 sm-8 md-6 lg-4 center">
		<div class="form-item">
			<input type="text" name="name" value="<?= $name; ?>" placeholder="{{Your Name}}"/>
		</div>
		<div class="form-item">
			<input type="text" name="email" value="<?= $email; ?>" placeholder="{{Your Email}}"/>
		</div>
		<div class="form-item">
			<textarea name="enquiry" cols="40" rows="10" placeholder="{{Your Message}}"><?= $enquiry; ?></textarea>
		</div>
		<div class="form-item captcha">
			<img src="<?= $captcha_url; ?>" alt=""/> <Br/>
			<input class="center" type="text" name="captcha" value="<?= $captcha; ?>" placeholder="{{Captcha Code}}"/>
		</div>

		<div class="buttons center">
			<button>{{Send Message}}</button>
		</div>
	</form>
</div>


