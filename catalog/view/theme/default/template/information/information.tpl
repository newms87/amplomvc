<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
	<div class="content">
		<?= _breadcrumbs(); ?>
		<?= _area('top'); ?>

		<h1><?= $page_title; ?></h1>

		<?= $description; ?>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= _area('bottom'); ?>
	</div>

<?= _call('common/footer'); ?>
