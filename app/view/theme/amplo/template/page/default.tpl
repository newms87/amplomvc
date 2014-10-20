<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<? if ($style) { ?>
<style id="page-style">
	<?= $style; ?>
</style>
<? } ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content">
	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<? if (!empty($display_title)) { ?>
				<h1 id="page-title"><?= $title; ?></h1>
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
