<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content">
	<?= _breadcrumbs(); ?>
	<?= _area('top'); ?>

	<h1><?= !empty($page_title) ? $page_title : _l("Page Not Found"); ?></h1>

	<div class="section"><?= _l("The page you requested cannot be found."); ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
