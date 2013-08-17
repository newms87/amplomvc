<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>

	<? if (!empty($manufacturers)) { ?>
		<div class="manufacturers">

			<div class="pagination"><?= $pagination; ?></div>
		</div>

	<? } else { ?>
		<div class="section"><?= $text_empty; ?></div>

		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	<? } ?>

	<?= $content_bottom; ?>
</div>