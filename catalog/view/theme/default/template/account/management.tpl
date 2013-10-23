<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _("Account Manager"); ?></h1>

	<div class="section left">
		<h2><?= _("Customer Information"); ?></h2>
		<div class="name"><?= $customer['display_name']; ?></div>
		<div class="phone"><?= $customer['telephone']; ?></div>
		<div class="email"><?= $customer['email']; ?></div>
		<br />
		<h2><?= _("Default Shipping Address"); ?></h2>
		<div class="shipping_address"><?= $shipping_address['display']; ?></div>
		<br />
		<h2><?= _("Newsletter"); ?></h2>
		<div class="newsletter"><?= $newsletter_display; ?></div>
		<br />
		<a class="button small account_edit" href="<?= $edit_account; ?>"><?= _("Edit Information"); ?></a>
	</div>

	<? if (!empty($data_subscriptions)) { ?>
		<div class="section right">
			<h2><?= $section_subscription; ?></h2>
			<div id="subscription_list">
				<? foreach ($data_subscriptions as $subscription) { ?>
					<? if ($subscription['status']) { ?>
						<div class="subscription">
							<div class="info">
								<div class="image left">
									<img src="<?= $subscription['product']['thumb']; ?>" />
								</div>
								<div class="info_text left">
									<div class="name"><?= $subscription['product']['name']; ?></div>
									<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
									<div class="price"><?= $subscription['total_display']; ?></div>
								</div>
							</div>
							<a href="<?= $subscription['edit']; ?>" class="clear update small button"><?= $text_edit_subscription; ?></a>
						</div>
					<? } else { ?>
						<div class="subscription cancelled">
							<div class="info">
								<div class="image left">
									<img src="<?= $subscription['product']['thumb']; ?>" />
								</div>
								<div class="info_text left">
									<div class="name"><?= $subscription['product']['name']; ?></div>
									<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
								</div>
							</div>
							<div class="clear inactive"><?= $text_subscription_inactive; ?></div>
							<a href="<?= $subscription['edit']; ?>" class="clear reactivate small button"><?= $text_edit_cancelled; ?></a>
							<a href="<?= $subscription['remove']; ?>" class="small button delete"><?= $button_remove; ?></a>
						</div>
					<? } ?>
				<? } ?>
			</div>
		</div>
	<? } ?>

	<div class="clear buttons">
		<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
		<div class="right">
			<input type="submit" value="<?= $button_save; ?>" class="button"/>
		</div>
	</div>

	<?= $content_bottom; ?>
</div>

<script type="text/javascript">//<!--
$('.cancelled .button.delete').click(function(){
	return confirm("<?= $text_confirm_remove; ?>");
});
//--></script>

<?= $footer; ?>
