<div class="pagination clearfix">
	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="pager-nav first" href="<?= $url_first; ?>">|&lt;</a>
			<a class="pager-nav prev" href="<?= $url_prev; ?>">&lt;</a>
		<? } ?>

		<? if ($start > 1) { ?>
			<span class="more-before"> .... </span>
		<? } ?>

		<div class="pages">
			<? foreach ($pages as $num => $link) { ?>
				<a href="<?= $link; ?>" class="page <?= $num == $page ? 'current' : ''; ?>"><?= $num; ?></a>
			<? } ?>
		</div>

		<? if ($end < $num_pages) { ?>
			<span class="more-after"> .... </span>
		<? } ?>

		<? if ($page < $num_pages) { ?>
			<a class="pager-nav next" href="<?= $url_next; ?>">&gt;</a>
			<a class="pager-nav last" href="<?= $url_last; ?>">&gt;|</a>
		<? } ?>
	</div>

	<div class="text"><?= $text; ?></div>
</div>

