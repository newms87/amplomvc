<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content">
	<?= _breadcrumbs(); ?>
	<?= _area('top'); ?>

	<h1><?= _l("Registration Success!"); ?></h1>

	<div class="success-message">
		<? if ($approved) { ?>
			<p><?= _l("Congratulations! Your new account has been successfully created!"); ?></p>
			<p><?= _l("You can now take advantage of member privileges to enhance your online shopping experience with us."); ?></p>
			<p><?= _l("If you have ANY questions about the operation of this online shop, please email us."); ?></p>
			<p>
				<?= _l("A confirmation has been sent to the provided email address. If you have not received it within the hour, please"); ?>
				<a href="<?= $contact; ?>"><?= _l("contact us"); ?></a>.
			</p>
		<? } else { ?>
			<p><?= _l("Thank you for registering with us!"); ?></p>
			<p><?= _l("You will be notified by email once your account has been activated by the store owner."); ?></p>
			<p>
				<?= _l("If you have ANY questions about the operation of this online shop, please"); ?>
				<a href="<?= $contact; ?>"><?= _l("contact the store owner"); ?></a>.
			</p>
		<? } ?>
	</div>

	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>


	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
