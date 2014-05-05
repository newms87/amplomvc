<div id="list_grid_toggle">
	<span><?= _l("Display"); ?></span>
	<a id="toggle_list"><?= _l("List"); ?></a> <b>/</b> <a class="active" id="toggle_grid"><?= _l("Grid"); ?></a>
</div>

<div id="catalog_list" class="grid">
	<? foreach ($products as $product) { ?>
		<a class="item_block" href="<?= $product['href']; ?>">

			<? if ($product['thumb']) { ?>
				<div class="image">
					<img class="primary" src="<?= $product['thumb']; ?>" title="<?= $product['name']; ?>"
						alt="<?= $product['name']; ?>"/>

					<? if (!empty($product['backup_thumb'])) { ?>
						<img class="backup" src="<?= $product['backup_thumb']; ?>" title="<?= $product['name']; ?>"
							alt="<?= $product['name']; ?>"/>
					<? } ?>
				</div>
			<? } ?>

			<div class="item_text">
				<div class="name"><?= $product['name']; ?></div>

				<? if (!empty($product['teaser'])) { ?>
					<div class="teaser"><?= $product['teaser']; ?></div>
				<? } ?>
			</div>
			<? if (!empty($product['price'])) { ?>
				<div class="price">
					<? if (empty($product['special'])) { ?>
						<?= $product['price']; ?>
					<? } else { ?>
						<span class="retail"><?= $product['price']; ?></span> <span
							class="special"><?= $product['special']; ?></span>
					<? } ?>

					<? if ($show_price_tax) { ?>
						<br/>
						<span class="price-tax"><?= _l("Tax:"); ?> <?= $product['tax']; ?></span>
					<? } ?>
				</div>
			<? } ?>

			<? if ($review_status) { ?>
				<div class="rating"><img src="<?= URL_THEME_IMAGE . "stars-$product[rating].png"; ?>"
						alt="<?= $product['reviews']; ?>"/></div>
			<? } ?>

			<? if ($list_show_add_to_cart) { ?>
				<div class="cart">
					<input type="button" value="<?= _l("Add to Cart"); ?>" onclick="addToCart('<?= $product['product_id']; ?>');"
						class="button"/>
				</div>
			<? } ?>

			<? if ($wishlist_status) { ?>
				<div class="wishlist"><a onclick="addToWishList('<?= $product['product_id']; ?>');"><?= _l("Add to Wishlist"); ?></a></div>
			<? } ?>
			<? if ($compare_status) { ?>
				<div class="compare"><a onclick="addToCompare('<?= $product['product_id']; ?>');"><?= _l("Add to Compare"); ?></a></div>
			<? } ?>
		</a>
	<? } ?>
</div>

<script type="text/javascript">
	$('[name=sort_list]').change(function () {
		location = "<?= $sort_url ; ?>" + $(this).val();
	});

	$('#list_grid_toggle > a').click(function () {
		if ($(this).hasClass('active')) return;

		view = $(this).attr('id');

		$('#list_grid_toggle a.active').removeClass('active');
		$(this).addClass('active');

		if (view == 'toggle_list') {
			$('#catalog_list').removeClass('grid').addClass('list');

			$.cookie('display', 'list');
		} else {
			$('#catalog_list').removeClass('list').addClass('grid');

			$.cookie('display', 'grid');
		}
	});

	view = $.cookie('display') || 'grid';

	$('#toggle_' + view).click();
</script>