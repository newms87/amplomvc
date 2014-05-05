<div class="block">
	<? if (!empty($block_product_list)) { ?>
		<div class="item-filter">
			<div class="limit"><?= $limits; ?></div>
			<div class="sort"><?= $sorts; ?></div>
		</div>

		<?= $block_product_list; ?>

		<div class="pagination"><?= $pagination; ?></div>

	<? } else { ?>
		<div class="section"><?= _l("There are no specials at this time."); ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>
	<? } ?>
</div>
