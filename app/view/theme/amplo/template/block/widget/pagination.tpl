<div class="pagination clearfix">
	<? if ($text_position === 'before') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>

	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="pager-nav first page" href="<?= $url_first; ?>">
				<b class="fa fa-chevron-left"></b>
				<b class="fa fa-chevron-left"></b>
			</a>
			<a class="pager-nav prev page" href="<?= $url_prev; ?>">
				<b class="fa fa-chevron-left"></b>
			</a>
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
			<a class="pager-nav next page" href="<?= $url_next; ?>">
				<b class="fa fa-chevron-right"></b>
			</a>
			<a class="pager-nav last page" href="<?= $url_last; ?>">
				<b class="fa fa-chevron-right"></b>
				<b class="fa fa-chevron-right"></b>
			</a>
		<? } ?>
	</div>

	<? if ($text_position === 'after') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>
</div>

