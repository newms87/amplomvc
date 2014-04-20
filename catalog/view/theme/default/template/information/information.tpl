<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $this->area->render('top'); ?>

		<h1><?= $page_title; ?></h1>

		<?= $description; ?>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= $this->area->render('bottom'); ?>
	</div>

<?= $this->call('common/footer'); ?>
