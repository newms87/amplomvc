<div id='register_account'>
	<form action='<?= $action_register; ?>' method="post">
		<div class="left">
			<h2><?= $text_your_details; ?></h2>

			<div class="checkout_form">
				<?= $form_register; ?>
			</div>

			<h2><?= $text_your_password; ?></h2>

			<div class="checkout_form">
				<?= $form_password; ?>
			</div>
		</div>
		<div class="right">
			<h2><?= $text_your_address; ?></h2>

			<div id='register_address' class="checkout_form">
				<?= $form_address; ?>
			</div>
		</div>
		<div class="checkout_newsletter_signup">
			<input type="checkbox" name="newsletter" value="1" id="newsletter" checked='checked'/>
			<label for="newsletter"><?= $entry_newsletter; ?></label>
		</div>
		<div class="buttons">
			<div class="right">
				<? if (!empty($agree_to_terms)) { ?>
					<?= $text_agree; ?>
					<input type="checkbox" name="agree" value=""/>
				<? } ?>
				<input type="submit" value="<?= $button_continue; ?>" id="button_register" class="button"/>
			</div>
		</div>
	</form>
</div>

<?= $this->builder->js('load_zones', "#register_address", '.country_select', '.zone_select'); ?>

<script type="text/javascript">//<!--
	$('#button_register').click(function () {
		validate_form($('#register_account form'));

		return false;
	});
	//--></script>