<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
<div class="content">
	<?= breadcrumbs(); ?>
	<?= area('top'); ?>

	<h1><?= _l("Search"); ?></h1>
	<b><?= _l("Search Criteria"); ?></b>

	<div class="section">
		<p><?= _l("Search:"); ?>
			<? if ($filter_name) { ?>
				<input type="text" name="filter_name" value="<?= $filter_name; ?>"/>
			<? } else { ?>
				<input type="text" name="filter_name" value="<?= $filter_name; ?>" onclick="this.value = '';"
					onkeydown="this.style.color = '000000'" style="color: #999;"/>
			<? } ?>
			<select name="filter_category_id">
				<option value="0"><?= _l("All Categories"); ?></option>
				<? foreach ($categories as $category_1) { ?>
					<? if ($category_1['category_id'] == $filter_category_id) { ?>
						<option value="<?= $category_1['category_id']; ?>"
							selected="selected"><?= $category_1['name']; ?></option>
					<? } else { ?>
						<option value="<?= $category_1['category_id']; ?>"><?= $category_1['name']; ?></option>
					<? } ?>
					<? foreach ($category_1['children'] as $category_2) { ?>
						<? if ($category_2['category_id'] == $filter_category_id) { ?>
							<option value="<?= $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $category_2['name']; ?></option>
						<? } else { ?>
							<option value="<?= $category_2['category_id']; ?>">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $category_2['name']; ?></option>
						<? } ?>
						<? foreach ($category_2['children'] as $category_3) { ?>
							<? if ($category_3['category_id'] == $filter_category_id) { ?>
								<option value="<?= $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $category_3['name']; ?></option>
							<? } else { ?>
								<option value="<?= $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $category_3['name']; ?></option>
							<? } ?>
						<? } ?>
					<? } ?>
				<? } ?>
			</select>
			<? if ($filter_sub_category) { ?>
				<input type="checkbox" name="filter_sub_category" value="1" id="sub_category" checked="checked"/>
			<? } else { ?>
				<input type="checkbox" name="filter_sub_category" value="1" id="sub_category"/>
			<? } ?>
			<label for="sub_category"><?= _l("Search in subcategories"); ?></label>
		</p>
		<? if ($filter_description) { ?>
			<input type="checkbox" name="filter_description" value="1" id="description" checked="checked"/>
		<? } else { ?>
			<input type="checkbox" name="filter_description" value="1" id="description"/>
		<? } ?>
		<label for="description"><?= _l("Search in product descriptions"); ?></label>
	</div>
	<div class="buttons">
		<div class="right"><input type="button" value="<?= _l("Search"); ?>" id="button-search" class="button"/>
		</div>
	</div>
	<h2><?= _l("Products meeting the search criteria"); ?></h2>
	<? if ($products) { ?>
		<div class="product-filter">
			<div class="display"><b><?= _l("Display:"); ?></b> <?= _l("List"); ?> <b>/</b> <a onclick="display('grid');"><?= _l("Grid"); ?></a></div>
			<div class="limit"><?= _l("Show:"); ?>
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
			<div class="sort"><?= _l("Sort By:"); ?>
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
		<div class="product-compare"><a href="<?= $compare; ?>" id="compare-total"><?= _l("Compare"); ?></a></div>
		<div class="product-list">
			<? foreach ($products as $product) { ?>
				<div>
					<? if ($product['thumb']) { ?>
						<div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>"
									title="<?= $product['name']; ?>"
									alt="<?= $product['name']; ?>"/></a>
						</div>
					<? } ?>
					<div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
					<div class="description"><?= $product['description']; ?></div>
					<? if ($product['price']) { ?>
						<div class="price">
							<? if (!$product['special']) { ?>
								<?= $product['price']; ?>
							<? } else { ?>
								<span class="retail"><?= $product['price']; ?></span> <span
									class="special"><?= $product['special']; ?></span>
							<? } ?>
							<? if ($product['tax']) { ?>
								<br/>
								<span class="price-tax"><?= _l("Ex Tax:"); ?> <?= $product['tax']; ?></span>
							<? } ?>
						</div>
					<? } ?>
					<? if ($product['rating']) { ?>
						<div class="rating"><img src="<?= URL_THEME_IMAGE . "stars-$product[rating].png"; ?>"
								alt="<?= $product['reviews']; ?>"/></div>
					<? } ?>
					<div class="cart"><input type="button" value="<?= _l("Add to Cart"); ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button"/></div>
					<div class="wishlist"><a onclick="addToWishList('<?= $product['product_id']; ?>');"><?= _l("Add to Wish List"); ?></a></div>
					<div class="compare"><a onclick="addToCompare('<?= $product['product_id']; ?>');"><?= _l("Add to Compare"); ?></a></div>
				</div>
			<? } ?>
		</div>
		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="section"><?= _l("There is no product that matches the search criteria."); ?></div>
	<? } ?>

	<?= area('bottom'); ?>
</div>

<script type="text/javascript">
	$('#content input[name=\'filter_name\']').keydown(function (e) {
		if (e.keyCode == 13) {
			$('#button-search').trigger('click');
		}
	});

	$('#button-search').bind('click', function () {
		url = "<?= HTTP_CATALOG . "index.php?route=product/search"; ?>";

		var filter_name = $('#content input[name=\'filter_name\']').attr('value');

		if (filter_name) {
			url += '&filter_name=" + encodeURIComponent(filter_name);
		}

		var filter_category_id = $("#content select[name=\'filter_category_id\']").attr('value');

		if (filter_category_id > 0) {
			url += '&filter_category_id=" + encodeURIComponent(filter_category_id);
		}

		var filter_sub_category = $("#content input[name=\'filter_sub_category\']:checked").attr('value');

		if (filter_sub_category) {
			url += '&filter_sub_category=true';
		}

		var filter_description = $('#content input[name=\'filter_description\']:checked').attr('value');

		if (filter_description) {
			url += '&filter_description=true';
		}

		location = url;
	});

	function display(view) {
		if (view == 'list') {
			$('.product-grid').attr('class', 'product-list');

			$('.product-list > div').each(function (index, element) {
				html = '<div class="right">';
				html += '	<div class="cart">' + $(element).find('.cart').html() + '</div>';
				html += '	<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
				html += '	<div class="compare">' + $(element).find('.compare').html() + '</div>';
				html += '</div>';

				html += '<div class="left">';

				var image = $(element).find('.image').html();

				if (image != null) {
					html += '<div class="image">' + image + '</div>';
				}

				var price = $(element).find('.price').html();

				if (price != null) {
					html += '<div class="price">' + price + '</div>';
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

			$('.display').html('<b><?= _l("Display:"); ?></b> <?= _l("List"); ?> <b>/</b> <a onclick="display(\'grid\');"><?= _l("Grid"); ?></a>');

			$.cookie('display', 'list');
		} else {
			$('.product-list').attr('class', 'product-grid');

			$('.product-grid > div').each(function (index, element) {
				html = '';

				var image = $(element).find('.image').html();

				if (image != null) {
					html += '<div class="image">' + image + '</div>';
				}

				html += '<div class="name">' + $(element).find('.name').html() + '</div>';
				html += '<div class="description">' + $(element).find('.description').html() + '</div>';

				var price = $(element).find('.price').html();

				if (price != null) {
					html += '<div class="price">' + price + '</div>';
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

			$('.display').html('<b><?= _l("Display:"); ?></b> <a onclick="display(\'list\');"><?= _l("List"); ?></a> <b>/</b> <?= _l("Grid"); ?>');

			$.cookie('display', 'grid');
		}
	}

	view = $.cookie('display');

	if (view) {
		display(view);
	} else {
		display('list');
	}
</script>
<?= call('common/footer'); ?>
