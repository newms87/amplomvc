<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content">
	<? if ($style) { ?>
		<style scoped><?= $style; ?></style>
	<? } ?>

	<? if (!empty($meta['show_title']) || !empty($meta['show_breadcrumbs'])) { ?>
		<header class="row top-row page-header">
			<div class="wrap">
				<? if (!empty($meta['show_breadcrumbs'])) { ?>
					<div class="breadcrumbs">
						<?= $is_ajax ? '' : breadcrumbs(); ?>
					</div>
				<? } ?>

				<? if (!empty($meta['show_title'])) { ?>
					<h1 id="page-title"><?= $title; ?></h1>
				<? } ?>
			</div>
		</header>
	<? } ?>

	<?= area('top'); ?>

	<div class="page-content row">
		<div class="wrap">
			<? if (!empty($content_file) && is_file($content_file)) {
				require_once($content_file);
			} else {
				echo render_content($content);
			} ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
