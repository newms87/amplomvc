<?= $header; ?>
<? if ($success) { ?>
<div class="message_box success"><?= $success; ?><img src="<?= HTTP_THEME_IMAGE . 'close.png'; ?>" alt="" class="close" /></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<? if ($products) { ?>
	<table class="compare-info">
		<thead>
			<tr>
				<td class="compare-product" colspan="<?= count($products) + 1; ?>"><?= $text_product; ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $text_name; ?></td>
				<? foreach ($products as $product) { ?>
				<td class="name"><a href="<?= $products[$product['product_id']]['href']; ?>"><?= $products[$product['product_id']]['name']; ?></a></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_image; ?></td>
				<? foreach ($products as $product) { ?>
				<td><? if ($products[$product['product_id']]['thumb']) { ?>
					<img src="<?= $products[$product['product_id']]['thumb']; ?>" alt="<?= $products[$product['product_id']]['name']; ?>" />
					<? } ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_price; ?></td>
				<? foreach ($products as $product) { ?>
				<td><? if ($products[$product['product_id']]['price']) { ?>
					<? if (!$products[$product['product_id']]['special']) { ?>
					<?= $products[$product['product_id']]['price']; ?>
					<? } else { ?>
					<span class="retail"><?= $products[$product['product_id']]['price']; ?></span> <span class="special"><?= $products[$product['product_id']]['special']; ?></span>
					<? } ?>
					<? } ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_model; ?></td>
				<? foreach ($products as $product) { ?>
				<td><?= $products[$product['product_id']]['model']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_manufacturer; ?></td>
				<? foreach ($products as $product) { ?>
				<td><?= $products[$product['product_id']]['manufacturer']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_availability; ?></td>
				<? foreach ($products as $product) { ?>
				<td><?= $products[$product['product_id']]['availability']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_rating; ?></td>
				<? foreach ($products as $product) { ?>
				<td><img src="<?= HTTP_THEME_IMAGE . "stars-" .$products[$product['product_id']]['rating'] . ".png"; ?>" alt="<?= $products[$product['product_id']]['reviews']; ?>" /><br />
					<?= $products[$product['product_id']]['reviews']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_summary; ?></td>
				<? foreach ($products as $product) { ?>
				<td class="description"><?= $products[$product['product_id']]['description']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_weight; ?></td>
				<? foreach ($products as $product) { ?>
				<td><?= $products[$product['product_id']]['weight']; ?></td>
				<? } ?>
			</tr>
			<tr>
				<td><?= $text_dimension; ?></td>
				<? foreach ($products as $product) { ?>
				<td><?= $products[$product['product_id']]['length']; ?> x <?= $products[$product['product_id']]['width']; ?> x <?= $products[$product['product_id']]['height']; ?></td>
				<? } ?>
			</tr>
		</tbody>
		<? foreach ($attribute_groups as $attribute_group) { ?>
		<thead>
			<tr>
				<td class="compare-attribute" colspan="<?= count($products) + 1; ?>"><?= $attribute_group['name']; ?></td>
			</tr>
		</thead>
		<? foreach ($attribute_group['attributes'] as $key => $attribute) { ?>
		<tbody>
			<tr>
				<td><?= $attribute['name']; ?></td>
				<? foreach ($products as $product) { ?>
				<? if (isset($products[$product['product_id']]['attribute'][$key])) { ?>
				<td><?= $products[$product['product_id']]['attribute'][$key]; ?></td>
				<? } else { ?>
				<td></td>
				<? } ?>
				<? } ?>
			</tr>
		</tbody>
		<? } ?>
		<? } ?>
		<tr>
			<td></td>
			<? foreach ($products as $product) { ?>
			<td><input type="button" value="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button" /></td>
			<? } ?>
		</tr>
		<tr>
			<td></td>
			<? foreach ($products as $product) { ?>
			<td class="remove"><a href="<?= $product['remove']; ?>" class="button"><?= $button_remove; ?></a></td>
			<? } ?>
		</tr>
	</table>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<? } else { ?>
	<div class="content"><?= $text_empty; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<? } ?>
	<?= $content_bottom; ?></div>
<?= $footer; ?>