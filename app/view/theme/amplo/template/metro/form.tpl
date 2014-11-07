<form class="form" action="<?= site_url('metro/submit'); ?>" method="post">

	<div class="form-sections">
		<div class="form-section details-form">
			<h3><?= _l("Let's get some details..."); ?></h3>

			<ol class="questions">
				<? foreach ($questions as $qname => $q) { ?>
					<li class="question">
						<label><?= $q['question']; ?></label>
						<div class="mobile">
							<?= build('select', array(
								'name'   => "answers[$qname]",
								'data'   => $q['answer'],
								'select' => $q['default'],
							)); ?>
						</div>

						<div class="desktop">
							<?= build(is_array($q['default']) ? 'checkbox' : 'radio', array(
								'name'   => "answers[$qname]",
								'data'   => $q['answer'],
								'select' => $q['default'],
							)); ?>
						</div>
					</li>
				<? } ?>
			</ol>

			<div class="submit-button">
				<a class="button details-submit">
					<?= _l("Submit"); ?>
					<span class="sprite arrow"></span>
				</a>
			</div>
		</div>

		<div class="form-section contact-form">
			<h3><?= _l("Last Step!"); ?></h3>

			<p><?= _l("Enter your contact information below."); ?></p>

			<input type="text" name="name" class="name" value="<?= $name; ?>" placeholder="<?= _l("*Name"); ?>" />
			<input type="text" name="name" class="email" value="<?= $email; ?>" placeholder="<?= _l("*Email"); ?>" />
			<input type="text" name="name" class="address" value="<?= $address; ?>" placeholder="<?= _l("Project Address"); ?>" />
			<input type="text" name="name" class="zip-code" value="<?= $zip; ?>" placeholder="<?= _l("*Zip Code"); ?>" />
			<input type="text" name="name" class="phone" value="<?= $phone; ?>" placeholder="<?= _l("(555) 555-5555"); ?>" />
			<textarea name="details" placeholder="<?= _l("Let us know any other details about your project here..."); ?>"><?= $details; ?></textarea>

			<div class="required-fields"><?= _l("*Denotes required field"); ?></div>

			<label class="checkbox single">
				<input type="checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked' : ''; ?> />
				<span class="label"><?= _l("Sign me up for the Metro Construction Newsletter");?></span>
			</label>

			<div class="submit-button">
				<button>
					<?= _l("Get My Quote"); ?>
					<span class="sprite arrow"></span>
				</button>

				<div class="call-us"><?= _l("Or call %s to speak to a representative", option('config_telephone')); ?></div>
			</div>
		</div>

		<div class="form-section success-form">
			<h3><?= _l("Success!"); ?></h3>

			<p><?= _l("We'll be contacting you shortly with your FREE James Hardie siding quote. In the meantime, learn more about siding and the different options available on our <a href=\"http://pcnpro.com/homeowner-resources/\">blog</a>."); ?></p>

			<div class="resources">
				<? foreach ($resources as $resource) { ?>
					<a href="<?= $resource['url']; ?>" class="resource">
						<img src="<?= image($resource['image'], 200, 160); ?>" />
						<div class="text"><?= $resource['text']; ?></div>
					</a>
				<? } ?>
			</div>

			<div class="submit-button">
				<a class="button" href="http://pcnpro.com/homeowner-resources/">
					<?= _l("Explore Blog"); ?>
					<span class="sprite arrow"></span>
				</a>
			</div>
		</div>
	</div>

</form>

<script type="text/javascript">
	$(document).ready(function () {
		$('.ac-carousel').bxSlider({controls: false, auto: true});
	});

	$('.details-submit').click(function() {

		return false;
	});
</script>