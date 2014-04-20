<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->area->render('top'); ?>

	<h1><?= _l("Customer Logout"); ?></h1>

	<div class="success_message"><?= _l("You have been successfully logged out of your account!"); ?></div>

	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->call('common/footer'); ?>
