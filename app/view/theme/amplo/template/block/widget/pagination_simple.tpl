<div class="pagination simple clearfix">
	<div class="pager">
		<? if ($page > 1) { ?>
			<a class="page pager-nav first" href="<?= $url_first; ?>">
				<b class="fa fa-chevron-left"></b>
				<b class="fa fa-chevron-left"></b>
			</a>
			<a class="page pager-nav prev" href="<?= $url_prev; ?>">
				<b class="fa fa-chevron-left"></b>
			</a>
		<? } ?>

		<div class="pages">
			<div class="page page-count">
				<?= $page . ' of ' . $num_pages; ?>
			</div>
		</div>

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
</div>

