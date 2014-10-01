<style id="page-style">
	<? if (!empty($style_file)) { ?>
	<?= file_get_contents($style_file); ?>
	<? } elseif (!empty($style)) { ?>
	<?= $style; ?>
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

	<div class="page-content row">
		<div class="wrap">
			<? if (!empty($content_file)) { ?>
				<? include($content_file); ?>
			<? } elseif (!empty($content)) { ?>
				<?= $content; ?>
			<? } ?>
		</div>
	</div>

</section>
