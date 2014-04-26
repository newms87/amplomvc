<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content">
	<?= _breadcrumbs(); ?>
	<?= _area('top'); ?>

	<h1><?= _l("Customer Logout"); ?></h1>

	<div class="success_message"><?= _l("You have been successfully logged out of your account!"); ?></div>

	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
