<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
	<div class="content">
		<?= breadcrumbs(); ?>
		<?= area('top'); ?>

		<h1><?= $page_title; ?></h1>

		<?= $description; ?>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= area('bottom'); ?>
	</div>

<?= call('common/footer'); ?>
