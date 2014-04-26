<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>

<div class="content">
	<?= _breadcrumbs(); ?>

	<div class="section">

		<h1><?= $title; ?></h1>

		<?= _area('top'); ?>

		<div class="page_content"><?= $content; ?></div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
