<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content">
	<header class="row top-row">
		<div class="wrap">
			<?= IS_AJAX ? '' : breadcrumbs(); ?>

			<? if (!empty($display_title)) { ?>
				<h1><?= $title; ?></h1>
			<? } ?>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="page-content row">
		<div class="wrap">
			<? if (!empty($content_file) && is_file($content_file)) {
				require_once($content_file);
			} else {
				echo $content;
			} ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>