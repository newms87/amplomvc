<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>

<section id="page-<?= $page_id; ?>" class="page page-<?= $template; ?> content">
	<? if (!empty($css)) { ?>
		<style><?= $css; ?></style>
	<? } ?>

	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<? if (!empty($display_title)) { ?>
				<h1><?= $title; ?></h1>
			<? } ?>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="page-content row">
		<div class="wrap">
			<?= $content; ?>
		</div>
	</div>

	<?= _area('bottom'); ?>
</section>

<?= _call('common/footer'); ?>
