<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?><?= area('right'); ?>
<div class="content">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<?= area('top'); ?>

	<h1><?= $page_title; ?></h1>

	<div class="success-message"><?= $message; ?></div>

	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button">{{Continue}}</a></div>
	</div>


	<?= area('bottom'); ?>
</div>

<?= $is_ajax ? '' : call('footer'); ?>
