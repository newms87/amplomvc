<?= $is_ajax ? '' : call('header'); ?>

<? if ($style) { ?>
	<style id="page-style">
		<?= $style; ?>
	</style>
<? } ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content">
	<? if (!empty($content_file) && is_file($content_file)) {
		require_once($content_file);
	} else {
		echo render_content($content);
	} ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
