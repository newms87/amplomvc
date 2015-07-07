<div class="pagination simple clearfix">
	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="page pager-nav first" href="<?= $url_first; ?>">
				<b class="amp-sprite si-chevron-dark-gray-horiz rotate-180"></b>
				<b class="amp-sprite si-chevron-dark-gray-horiz rotate-180"></b>
			</a>
			<a class="page pager-nav prev" href="<?= $url_prev; ?>">
				<b class="amp-sprite si-chevron-dark-gray-horiz rotate-180"></b>
			</a>
		<? } ?>

		<div class="pages">
			<div class="page page-count">
				<?= $page . ' of ' . $num_pages; ?>
			</div>
		</div>

		<? if ($page < $num_pages) { ?>
			<a class="pager-nav next page" href="<?= $url_next; ?>">
				<b class="amp-sprite si-chevron-dark-gray-horiz"></b>
			</a>
			<a class="pager-nav last page" href="<?= $url_last; ?>">
				<b class="amp-sprite si-chevron-dark-gray-horiz"></b>
				<b class="amp-sprite si-chevron-dark-gray-horiz"></b>
			</a>
		<? } ?>
	</div>
</div>

