<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<div class="content">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="section">

		<h1><?= $title; ?></h1>

		<?= area('top'); ?>

		<div class="page-content"><?= $content; ?></div>
	</div>

	<?= area('bottom'); ?>
</div>

<?= $is_ajax ? '' : call('footer'); ?>
