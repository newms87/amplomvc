<?= $header; ?>
<? if ($success) { ?>
<div class="message_box success"><?= $success; ?><img src="<?= HTTP_THEME_IMAGE . 'close.png'; ?>" alt="" class="close" /></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content" style='padding-top:0'><?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	<h1><?= $heading_title; ?></h1>
	<? if ($products) { ?>
	<div class="wishlist-info">
		<table>
			<thead>
				<tr>
					<td class="image"><?= $column_image; ?></td>
					<td class="name"><?= $column_name; ?></td>
					<td class="model"><?= $column_model; ?></td>
					<td class="stock"><?= $column_stock; ?></td>
					<td class="price"><?= $column_price; ?></td>
					<td class="action"><?= $column_action; ?></td>
				</tr>
			</thead>
			<? foreach ($products as $product) { ?>
			<tbody id="wishlist-row<?= $product['product_id']; ?>">
				<tr>
					<td class="image"><? if ($product['thumb']) { ?>
						<a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" title="<?= $product['name']; ?>" /></a>
						<? } ?></td>
					<td class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></td>
					<td class="model"><?= $product['model']; ?></td>
					<td class="stock"><?= $product['stock']; ?></td>
					<td class="price"><? if ($product['price']) { ?>
						<div class="price">
							<? if (!$product['special']) { ?>
							<?= $product['price']; ?>
							<? } else { ?>
							<s><?= $product['price']; ?></s> <b><?= $product['special']; ?></b>
							<? } ?>
						</div>
						<? } ?></td>
					<td class="action"><img src="<?= HTTP_THEME_IMAGE . 'cart-add.png'; ?>" alt="<?= $button_cart; ?>" title="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" />&nbsp;&nbsp;<a href="<?= $product['remove']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>" alt="<?= $button_remove; ?>" title="<?= $button_remove; ?>" /></a></td>
				</tr>
			</tbody>
			<? } ?>
		</table>
	</div>
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