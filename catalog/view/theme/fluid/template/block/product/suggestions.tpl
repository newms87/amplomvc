<div id="suggested_products">
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
				</div>

				<? if (!empty($product['price'])) { ?>
					<div class="price">
						<? if (empty($product['special'])) { ?>
							<?= $product['price']; ?>
						<? } else { ?>
							<span class="retail"><?= $product['price']; ?></span>
							<span class="special"><?= $product['special']; ?></span>
						<? } ?>

						<? if ($show_price_tax) { ?>
							<br/>
							<span class="price-tax"><?= _l("Tax"); ?> <?= $product['tax']; ?></span>
						<? } ?>
					</div>
				<? } ?>
			</a>
		<? } ?>
	</div>
</div>
