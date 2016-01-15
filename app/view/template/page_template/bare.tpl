<?= $is_ajax ? '' : call('header'); ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content">
	<? if ($style) { ?>
		<style scoped><?= $style; ?></style>
	<? } ?>

	<? if (!empty($content_file) && is_file($content_file)) {
		require_once($content_file);
	} else {
		echo render_content($content);
	} ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
