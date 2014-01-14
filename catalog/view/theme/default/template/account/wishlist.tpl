<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= _l("My Wish List"); ?></h1>
		<? if ($products) { ?>
			<div class="wishlist-info">
				<table>
					<thead>
						<tr>
							<td class="image"><?= _l("Image"); ?></td>
							<td class="name"><?= _l("Product Name"); ?></td>
							<td class="model"><?= _l("Model"); ?></td>
							<td class="stock"><?= _l("Stock"); ?></td>
							<td class="price"><?= _l("Unit Price"); ?></td>
							<td class="action"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<? foreach ($products as $product) { ?>
						<tbody id="wishlist-row<?= $product['product_id']; ?>">
							<tr>
								<td class="image"><? if ($product['thumb']) { ?>
										<a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>"
												alt="<?= $product['name']; ?>"
												title="<?= $product['name']; ?>"/></a>
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
								<td class="action"><img src="<?= HTTP_THEME_IMAGE . 'cart-add.png'; ?>" alt="<?= _l("Add to Cart"); ?>"
										title="<?= _l("Add to Cart"); ?>" onclick="addToCart('<?= $product['product_id']; ?>');"/>&nbsp;&nbsp;<a
										href="<?= $product['remove']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>"
											alt="<?= _l("Remove"); ?>"
											title="<?= _l("Remove"); ?>"/></a></td>
							</tr>
						</tbody>
					<? } ?>
				</table>
			</div>
			<div class="buttons">
				<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
			</div>
		<? } else { ?>
			<div class="section"><?= _l("Your wish list is empty."); ?></div>
			<div class="buttons">
				<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
			</div>
		<? } ?>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>
