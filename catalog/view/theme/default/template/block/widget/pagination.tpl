<div <?= $attrs; ?>>
	<? if ($page > 1) { ?>
		<a class="pager_nav first" href="<?= $url_first; ?>"><?= _l("|&lt;"); ?></a>
		<a class="pager_nav prev" href="<?= $url_prev; ?>"><?= _l("&lt;"); ?></a>
	<? } ?>

	<? if ($start > 1) { ?>
		<span class="pager_more_before"><?= _l(" .... "); ?></span>
	<? } ?>

	<div class="pager_pages">
		<? foreach ($pages as $num => $link) { ?>
			<? if ($num == $page) { ?>
				<a href="<?= $link; ?>" class="current"><?= $num; ?></a>
			<? } else { ?>
				<a href="<?= $link; ?>"><?= $num; ?></a>
			<? } ?>
		<? } ?>
	</div>

	<? if ($end < $num_pages) { ?>
		<span class="pager_more_after"><?= _l(" .... "); ?></span>
	<? } ?>

	<? if ($page < $num_pages) { ?>
		<a class="pager_nav next" href="<?= $url_next; ?>"><?= _l("&gt;"); ?></a>
		<a class="pager_nav last" href="<?= $url_last; ?>"><?= _l("&gt;|"); ?></a>
	<? } ?>
</div>

<div class="pager_text"><?= $text_pager; ?></div>