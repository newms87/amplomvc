<div class="pagination clearfix">
	<? if ($text_position === 'before') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>

	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="sprite pager-nav first page" href="<?= $url_first; ?>"></a>
			<a class="sprite pager-nav prev page" href="<?= $url_prev; ?>"></a>
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
			<a class="sprite pager-nav next page" href="<?= $url_next; ?>"></a>
			<a class="sprite pager-nav last page" href="<?= $url_last; ?>"></a>
		<? } ?>
	</div>

	<? if ($text_position === 'after') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>
</div>

