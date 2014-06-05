<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<style id="page-style">
	<? if (is_file($style)) { ?>
	<?= file_get_contents($style); ?>
	<? } ?>
</style>

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
			<? if ($content) { ?>
				<? include($content); ?>
			<? } ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>
