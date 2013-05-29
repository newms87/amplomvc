<div id="list_grid_toggle">
	<span><?= $text_display; ?></span>
	<a id="toggle_list" class="active"><?= $text_list; ?></a> <b>/</b> <a id="toggle_grid"><?= $text_grid; ?></a>
</div>

<div id="catalog_list" class='grid'>
	<? foreach ($products as $product) { ?>
	<div class="item_block">
		<? if ($product['thumb']) { ?>
		<div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" title="<?= $product['name']; ?>" alt="<?= $product['name']; ?>" /></a></div>
		<? } ?>
		<div class="item_text">
			<div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
			<div class="description"><?= $product['description']; ?></div>
		</div>
		<? if (!empty($product['price'])) { ?>
			<div class="price">
				<? if (empty($product['special'])) { ?>
					<?= $product['price']; ?>
				<? } else { ?>
					<span class="retail"><?= $product['price']; ?></span> <span class="special"><?= $product['special']; ?></span>
				<? } ?>
				
				<? if ($show_price_tax) { ?>
					<br />
					<span class="price-tax"><?= $text_tax; ?> <?= $product['tax']; ?></span>
				<? } ?>
			</div>
		<? } ?>
		
		<? if ($review_status) { ?>
			<div class="rating"><img src="<?= HTTP_THEME_IMAGE . "stars-$product[rating].png"; ?>" alt="<?= $product['reviews']; ?>" /></div>
		<? } ?>
		
		<? if($list_show_add_to_cart) { ?>
		<div class="cart">
			<input type="button" value="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button" />
		</div>
		<? } ?>
		
		<? if($wishlist_status) { ?>
			<div class="wishlist"><a onclick="addToWishList('<?= $product['product_id']; ?>');"><?= $button_wishlist; ?></a></div>
		<? } ?>
		<? if($compare_status) { ?>
			<div class="compare"><a onclick="addToCompare('<?= $product['product_id']; ?>');"><?= $button_compare; ?></a></div>
		<? } ?>
	</div>
	<? } ?>
</div>

<script type="text/javascript">//<!--
$('[name=sort_list]').change(function(){
	location = "<?= $sort_url ; ?>" + $(this).val();
});

$('#list_grid_toggle > a').click(function(){
	if($(this).hasClass('active')) return;
	
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
//--></script>