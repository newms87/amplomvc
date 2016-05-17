<div class="block-widget-limit">
	<? if ($show_more) { ?>
		<a class="limit-text md-hidden" href="<?= $show_more; ?>"><?= $limit_text; ?></a>
	<? } ?>

	<div class="limit-text md-visible"><?= $limit_text; ?></div>
	<div class="limit-items md-visible">
		<? foreach ($limits as $value => $text) { ?>
			<a class="limit-item <?= 'limit-' . $value . ((int)$value === (int)$limit ? ' selected' : ''); ?>" data-limit="<?= $value; ?>" href="<?= $limit_url . $value; ?>"><?= $text; ?></a>
		<? } ?>
	</div>
</div>
