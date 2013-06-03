<div class="item-filter">
			<div class="list_grid_toggle">
				<span><?= $text_display; ?></span>
				<a id="toggle_list"><?= $text_list; ?></a> <b>/</b> <a id="toggle_grid" class="active"><?= $text_grid; ?></a>
			</div>
			<div class="limit">
				<span><?= $text_limit; ?></span>
				<? foreach($limits as $value => $limit_text){ ?>
					<a <?= $value == $limit ? "class='selected'" : "href=\"" . $limit_url . $value . "\""; ?>><?= $limit_text; ?></a>
				<? } ?>
			</div>
			<div class="sort">
				<span class="sort_text"><?= $text_sort; ?></span>
				<?= $this->builder->build('select', $sorts, 'sort_list', $sort_select); ?>
			</div>
		</div>
		<div id="catalog_list" class='grid'>
			<? foreach ($collections as $collection) { ?>
			<div class="item_block">
				<? if ($collection['thumb']) { ?>
				<div class="image"><a href="<?= $collection['href']; ?>"><img src="<?= $collection['thumb']; ?>" title="<?= $collection['name']; ?>" alt="<?= $collection['name']; ?>" /></a></div>
				<? } ?>
				<div class="item_text">
					<div class="name"><a href="<?= $collection['href']; ?>"><?= $collection['name']; ?></a></div>
					<div class="description"><?= $collection['description']; ?></div>
				</div>
				<? if ($collection['price']) { ?>
				<div class="price">
					<? if (!$collection['special']) { ?>
					<?= $collection['price']; ?>
					<? } else { ?>
					<span class="retail"><?= $collection['price']; ?></span>
					<span class="special"><?= $collection['special']; ?></span>
					<? } ?>
				</div>
				<? } ?>
				<? if ($collection['rating']) { ?>
				<div class="rating">
					<img src="<?= HTTP_THEME_IMAGE . "stars-$collection[rating].png"; ?>" alt="<?= $collection['reviews']; ?>" />
				</div>
				<? } ?>
			</div>
			<? } ?>
		</div>
		<div class="pagination"><?= $pagination; ?></div>