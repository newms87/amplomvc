<span class="limit-text"><?= $limit_text; ?></span>
<? foreach ($limits as $value => $text) { ?>
	<? if ((int)$value === (int)$limit) { ?>
		<a class="limit-item selected"><?= $text; ?></a>
	<? } else { ?>
		<a class="limit-item" href="<?= $limit_url . $value; ?>"><?= $text; ?></a>
	<? } ?>
<? } ?>