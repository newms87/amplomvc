<div <?= $attrs; ?>>
	<? if($page > 1) { ?>
	<a class="pager_nav first" href="<?= $url_first; ?>"><?= $text_pager_first; ?></a>
	<a class="pager_nav prev" href="<?= $url_prev; ?>"><?= $text_pager_prev; ?></a>
	<? } ?>
	
	<? if($start > 1) { ?>
	<span class="pager_more_before"><?= $text_pager_more_before; ?></span>
	<? } ?>
	
	<div class="pager_pages">
	<? foreach($pages as $num => $link) { ?>
		<? if($num == $page) { ?>
		<a href="<?= $link; ?>" class="current"><?= $num; ?></a>
		<? } else { ?>
		<a href="<?= $link; ?>"><?= $num; ?></a>
		<? } ?>
	<? } ?>
	</div>
	
	<? if($end < $num_pages) { ?>
		<span class="pager_more_after"><?= $text_pager_more_after; ?></span>
	<? } ?>
	
	<? if($page < $num_pages) { ?>
	<a class="pager_nav next" href="<?= $url_next; ?>"><?= $text_pager_next; ?></a>
	<a class="pager_nav last" href="<?= $url_last; ?>"><?= $text_pager_last; ?></a>
	<? } ?>
</div>

<div class="pager_text"><?= $text_pager; ?></div>