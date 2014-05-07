<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<section id="page-<?= $page_id; ?>" class="page page-<?= $template; ?> content">
	<? if (!empty($css)) { ?>
		<style><?= $css; ?></style>
	<? } ?>

	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<? if (!empty($display_title)) { ?>
				<h1><?= $title; ?></h1>
			<? } ?>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="page-content row">
		<div class="wrap">
			<?= $content; ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>
