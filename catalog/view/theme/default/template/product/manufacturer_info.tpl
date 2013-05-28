<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs();?>
	<h1><?= $heading_title; ?></h1>
	<? if ($products) { ?>
	<div class="product-filter">
		<div class="display"><b><?= $text_display; ?></b> <?= $text_list; ?> <b>/</b> <a onclick="display('grid');"><?= $text_grid; ?></a></div>
		<div class="limit"><?= $text_limit; ?>
			<select onchange="location = this.value;">
				<? foreach ($limits as $limits) { ?>
				<? if ($limits['value'] == $limit) { ?>
				<option value="<?= $limits['href']; ?>" selected="selected"><?= $limits['text']; ?></option>
				<? } else { ?>
				<option value="<?= $limits['href']; ?>"><?= $limits['text']; ?></option>
				<? } ?>
				<? } ?>
			</select>
		</div>
		<div class="sort"><?= $text_sort; ?>
			<select onchange="location = this.value;">
				<? foreach ($sorts as $sorts) { ?>
				<? if ($sorts['value'] == $sort . '-' . $order) { ?>
				<option value="<?= $sorts['href']; ?>" selected="selected"><?= $sorts['text']; ?></option>
				<? } else { ?>
				<option value="<?= $sorts['href']; ?>"><?= $sorts['text']; ?></option>
				<? } ?>
				<? } ?>
			</select>
		</div>
	</div>
	<!--<div class="product-compare"><a href="<?= $compare; ?>" id="compare-total"><?= $text_compare; ?></a></div>-->
	<div class="product-list">
		<? foreach ($products as $product) { ?>
		<div>
			<? if ($product['thumb']) { ?>
			<div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" title="<?= $product['name']; ?>" alt="<?= $product['name']; ?>" /></a></div>
			<? } ?>
			<div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
			<div class="description"><?= $product['description']; ?></div>
			<? if ($product['price']) { ?>
			<div class="price">
				<? if (!$product['special']) { ?>
				<?= $product['price']; ?>
				<? } else { ?>
				<span class="retail"><?= $product['price']; ?></span> <span class="special"><?= $product['special']; ?></span>
				<? } ?>
				<? if ($product['tax']) { ?>
				<br />
				<span class="price-tax"><?= $text_tax; ?> <?= $product['tax']; ?></span>
				<? } ?>
			</div>
			<? } ?>
			<? if ($product['rating']) { ?>
			<div class="rating"><img src="<?= HTTP_THEME_IMAGE . "stars-$product[rating].png"; ?>" alt="<?= $product['reviews']; ?>" /></div>
			<? } ?>
			<div class="cart"><input type="button" value="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button" /></div>
			<!--<div class="wishlist"><a onclick="addToWishList('<?= $product['product_id']; ?>');"><?= $button_wishlist; ?></a></div>
			<div class="compare"><a onclick="addToCompare('<?= $product['product_id']; ?>');"><?= $button_compare; ?></a></div>-->
		</div>
		<? } ?>
	</div>
	<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
	<div class="content"><?= $text_empty; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<? }?>
	<?= $content_bottom; ?></div>
<script type="text/javascript">
//<!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			html	= '<div class="right">';
			html += '	<div class="cart">' + $(element).find('.cart').html() + '</div>';
			//html += '	<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			//html += '	<div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price	+ '</div>';
			}
					
			html += '	<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '	<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
				
			html += '</div>';

						
			$(element).html(html);
		});
		
		$('.display').html('<b><?= $text_display; ?></b> <?= $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?= $text_grid; ?></a>');
		
		$.cookie('display', 'list');
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price	+ '</div>';
			}
						
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			
			$(element).html(html);
		});
					
		$('.display').html('<b><?= $text_display; ?></b> <a onclick="display(\'list\');"><?= $text_list; ?></a> <b>/</b> <?= $text_grid; ?>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script>
<?= $footer; ?>