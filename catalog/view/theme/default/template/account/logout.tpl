<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $area_top; ?>

	<h1><?= _l("Customer Logout"); ?></h1>

	<div class="success_message"><?= _l("You have been successfully logged out of your account!"); ?></div>

	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= $area_bottom; ?>
</div>

<?= $common_footer; ?>
