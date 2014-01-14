<span class="limit_text"><?= _l("Limit"); ?></span>
<? foreach ($limits as $value => $text) { ?>
	<? if ((int)$value === (int)$limit) { ?>
		<a class="limit_item selected"><?= $text; ?></a>
	<? } else { ?>
		<a class="limit_item" href="<?= $limit_url . $value; ?>"><?= $text; ?></a>
	<? } ?>
<? } ?>
