<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $area_top; ?>

		<h1><?= _l("Product Comparison"); ?></h1>
		<? if ($products) { ?>
			<table class="compare-info">
				<thead>
					<tr>
						<td class="compare-product" colspan="<?= count($products) + 1; ?>"><?= _l("Product Details"); ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?= _l("Product"); ?></td>
						<? foreach ($products as $product) { ?>
							<td class="name"><a
									href="<?= $products[$product['product_id']]['href']; ?>"><?= $products[$product['product_id']]['name']; ?></a>
							</td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Image"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><? if ($products[$product['product_id']]['thumb']) { ?>
									<img src="<?= $products[$product['product_id']]['thumb']; ?>"
										alt="<?= $products[$product['product_id']]['name']; ?>"/>
								<? } ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Price"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><? if ($products[$product['product_id']]['price']) { ?>
									<? if (!$products[$product['product_id']]['special']) { ?>
										<?= $products[$product['product_id']]['price']; ?>
									<? } else { ?>
										<span class="retail"><?= $products[$product['product_id']]['price']; ?></span> <span
											class="special"><?= $products[$product['product_id']]['special']; ?></span>
									<? } ?>
								<? } ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Model"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><?= $products[$product['product_id']]['model']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Brand"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><?= $products[$product['product_id']]['manufacturer']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Availability"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><?= $products[$product['product_id']]['availability']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Rating"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><img
									src="<?= URL_THEME_IMAGE . "stars-" . $products[$product['product_id']]['rating'] . ".png"; ?>"
									alt="<?= $products[$product['product_id']]['reviews']; ?>"/><br/>
								<?= $products[$product['product_id']]['reviews']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Summary"); ?></td>
						<? foreach ($products as $product) { ?>
							<td class="description"><?= $products[$product['product_id']]['description']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Weight"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><?= $products[$product['product_id']]['weight']; ?></td>
						<? } ?>
					</tr>
					<tr>
						<td><?= _l("Dimensions (L x W x H)"); ?></td>
						<? foreach ($products as $product) { ?>
							<td><?= $products[$product['product_id']]['length']; ?>
								x <?= $products[$product['product_id']]['width']; ?>
								x <?= $products[$product['product_id']]['height']; ?></td>
						<? } ?>
					</tr>
				</tbody>
				<? foreach ($attribute_groups as $attribute_group) { ?>
					<thead>
						<tr>
							<td class="compare-attribute"
								colspan="<?= count($products) + 1; ?>"><?= $attribute_group['name']; ?></td>
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
						<td><input type="button" value="<?= _l("Add to Cart"); ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button"/></td>
					<? } ?>
				</tr>
				<tr>
					<td></td>
					<? foreach ($products as $product) { ?>
						<td class="remove"><a href="<?= $product['remove']; ?>" class="button"><?= _l("Remove"); ?></a></td>
					<? } ?>
				</tr>
			</table>
			<div class="buttons">
				<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
			</div>
		<? } else { ?>
			<div class="section"><?= _l("You have not chosen any products to compare."); ?></div>
			<div class="buttons">
				<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
			</div>
		<? } ?>

		<?= $area_bottom; ?>
	</div>

<?= $common_footer; ?>
