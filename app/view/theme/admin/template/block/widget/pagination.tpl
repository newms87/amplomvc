<div class="pagination clearfix">
	<? if ($text_position === 'before') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>

	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="pager-nav first" href="<?= $url_first; ?>" data-listing-scroll-top>&lt&lt;</a>
			<a class="pager-nav prev" href="<?= $url_prev; ?>" data-listing-scroll-top>&lt;</a>
		<? } ?>

		<? if ($start > 1) { ?>
			<span class="more-before"> .... </span>
		<? } ?>

		<div class="pages">
			<? foreach ($pages as $num => $link) { ?>
				<a href="<?= $link; ?>" class="page <?= $num == $page ? 'current' : ''; ?>" data-listing-scroll-top><?= $num; ?></a>
			<? } ?>
		</div>

		<? if ($end < $num_pages) { ?>
			<span class="more-after"> .... </span>
		<? } ?>

		<? if ($page < $num_pages) { ?>
			<a class="pager-nav next" href="<?= $url_next; ?>" data-listing-scroll-top>&gt;</a>
			<a class="pager-nav last" href="<?= $url_last; ?>" data-listing-scroll-top>&gt;&gt;</a>
		<? } ?>
	</div>

	<? if ($text_position === 'after') { ?>
		<div class="text"><?= $text; ?></div>
	<? } ?>
</div>

