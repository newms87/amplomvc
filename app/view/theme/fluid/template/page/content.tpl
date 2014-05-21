<? if ($style) { ?>
<style>
	<?= file_get_contents($style); ?>
</style>
<? } ?>

<section id="page-<?= $name; ?>" class="page page-<?= $name; ?> page-<?= $page_id; ?> content content-only">
	<header class="row top-row">
		<div class="wrap">
			<? if (!empty($display_title)) { ?>
				<h1><?= $title; ?></h1>
			<? } ?>
		</div>
	</header>

	<div class="page-content row">
		<div class="wrap">
			<? include($content); ?>
		</div>
	</div>
</section>