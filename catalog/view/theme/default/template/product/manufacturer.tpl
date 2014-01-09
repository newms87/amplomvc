<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _l("Find Your Favorite Brand"); ?></h1>

	<? if (!empty($manufacturers)) { ?>
		<div class="manufacturers">

			<div class="pagination"><?= $pagination; ?></div>
		</div>

	<? } else { ?>
		<div class="section"><?= _l("There are no manufacturers to list."); ?></div>

		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>
	<? } ?>

	<?= $content_bottom; ?>
</div>