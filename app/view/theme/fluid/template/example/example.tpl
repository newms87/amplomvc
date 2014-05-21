<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<div class="content">
	<?= breadcrumbs(); ?>

	<div class="section">

		<h1><?= $title; ?></h1>

		<?= area('top'); ?>

		<div class="page-content"><?= $content; ?></div>
	</div>

	<?= area('bottom'); ?>
</div>

<?= call('common/footer'); ?>
