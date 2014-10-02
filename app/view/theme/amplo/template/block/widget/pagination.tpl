<div class="pagination clearfix">
	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="pager-nav first" href="<?= $url_first; ?>"><?= _l("|&lt;"); ?></a>
			<a class="pager-nav prev" href="<?= $url_prev; ?>"><?= _l("&lt;"); ?></a>
		<? } ?>

		<? if ($start > 1) { ?>
			<span class="more-before"><?= _l(" .... "); ?></span>
		<? } ?>

		<div class="pages">
			<? foreach ($pages as $num => $link) { ?>
				<a href="<?= $link; ?>" class="page <?= $num == $page ? 'current' : ''; ?>"><?= $num; ?></a>
			<? } ?>
		</div>

		<? if ($end < $num_pages) { ?>
			<span class="more-after"><?= _l(" .... "); ?></span>
		<? } ?>

		<? if ($page < $num_pages) { ?>
			<a class="pager-nav next" href="<?= $url_next; ?>"><?= _l("&gt;"); ?></a>
			<a class="pager-nav last" href="<?= $url_last; ?>"><?= _l("&gt;|"); ?></a>
		<? } ?>
	</div>

	<div class="text"><?= $text; ?></div>
</div>

