<div class="listing-limits">
	<a class="limit-text md-hidden" href="<?= $show_more; ?>"><?= $limit_text; ?></a>

	<div class="limit-text md-visible"><?= $limit_text; ?></div>
	<div class="limit-items md-visible">
		<? foreach ($limits as $value => $text) { ?>
			<? if ((int)$value === (int)$limit) { ?>
				<a class="limit-item selected"><?= $text; ?></a>
			<? } else { ?>
				<a class="limit-item" href="<?= $limit_url . $value; ?>"><?= $text; ?></a>
			<? } ?>
		<? } ?>
	</div>
</div>
