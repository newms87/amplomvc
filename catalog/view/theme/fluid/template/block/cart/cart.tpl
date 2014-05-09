<div id="the-cart">
	<? if (!$show_price) { ?>
		<div class="no-price"><?= _l("Please <a href=\"%s\">Login</a> or <a href=\"%s\">Register</a> to see Prices.", site_url('customer/login'), site_url('customer/registration')); ?></div>
	<? } ?>

	<form id="cart-form" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="block_cart_form" value="1"/>

		<? if (!empty($cart_products) || !empty($cart_vouchers)) { ?>
			<div class="cart-info">
				<table>
					<thead>
					<tr>
						<td class="image"><?= _l("Image"); ?></td>
						<td class="name"><?= _l("Product Name"); ?></td>
						<td class="model"><?= _l("Model"); ?></td>
						<td class="quantity"><?= _l("Quantity"); ?></td>

						<? if (!empty($show_return_policy)) { ?>
							<td class="return-policy"><?= _l("Return Period"); ?></td>
						<? } ?>

						<? if ($show_price) { ?>
							<td class="price"><?= _l("Unit Price"); ?></td>
							<td class="total"><?= _l("Total"); ?></td>
						<? } ?>

						<td class="center remove"><?= _l("Remove"); ?></td>
					</tr>
					</thead>

					<tbody>
					<? if (!empty($cart_products)) { ?>
						<? foreach ($cart_products as $cart_product) { ?>
							<? $product = $cart_product['product']; ?>
							<tr class="product">
								<td class="image">
									<? if ($cart_product['image']) { ?>
										<a href="<?= site_url('product/product', 'product_id=' . $product['product_id']); ?>">
											<img src="<?= image($cart_product['image'], option('config_image_cart_width'), option('config_image_cart_height')); ?>" alt="<?= $product['name']; ?>" title="<?= $product['name']; ?>"/>
										</a>
									<? } ?>
								</td>
								<td class="name">
									<a href="<?= site_url('product/product', 'product_id=' . $product['product_id']); ?>"><?= $product['name']; ?></a>
									<? if (!$cart_product['in_stock']) { ?>
										<span class="out-of-stock"></span>
									<? } ?>

									<? if (!empty($cart_product['options'])) { ?>
										<div class="product-option-description">
											<? foreach ($cart_product['options'] as $product_option_id => $product_option_values) { ?>
												<? foreach ($product_option_values as $product_option_value) { ?>
													<? if ($product_option_value['display_value']) { ?>
														<div class="cart-product-option-value">
															<?= $product_option_value['display_value']; ?>
														</div>
													<? } else { ?>
														<div class="cart-product-option-value">
															<span class="name"><?= $product_option_value['name']; ?></span>
															<span class="value"><?= $product_option_value['value']; ?></span>
														</div>
													<? } ?>
												<? } ?>
											<? } ?>
										</div>
									<? } ?>

									<? if ($product['reward']) { ?>
										<span class="cart-product-reward"><?= _l("Total Points: %s", $product['reward']); ?></span>
									<? } ?>
								</td>
								<td class="model"><?= $product['model']; ?></td>
								<td class="quantity">
									<input type="text" name="quantity[<?= $cart_product['key']; ?>]" value="<?= $cart_product['quantity']; ?>" size="1"/>
									<input class="update" type="image" name="cart_update" value="1" onclick="return cart_update($(this));" src="<?= theme_url('image/update.png'); ?>" alt="<?= _l("Update"); ?>" title="<?= _l("Update your Cart"); ?>"/>
									<label><?= _l("Update"); ?></label>
								</td>

								<? if (!empty($show_return_policy)) { ?>
									<td class="return_policy">
										<div><?= $product['return_policy']; ?></div>
									</td>
								<? } ?>

								<? if (!isset($no_price_display)) { ?>
									<td class="price"><?= format('currency', $cart_product['price']); ?></td>
									<td class="total"><?= format('currency', $cart_product['total']); ?></td>
								<? } ?>

								<td class="center">
									<a href="<?= site_url("cart/cart/remove", 'cart_key=' . $cart_product['key']); ?>" class="button remove"></a>
								</td>
							</tr>
						<? } ?>
					<? } ?>

					<? if (!empty($vouchers)) { ?>
						<? foreach ($vouchers as $key => $voucher) { ?>
							<tr class="voucher">
								<td class="image"></td>
								<td class="name"><?= $voucher['description']; ?></td>
								<td class="model"></td>
								<td class="quantity"><input type="text" name="" value="1" size="1" disabled="disabled"/></td>

								<? if ($show_price) { ?>
									<td class="price"><?= format('currency', $voucher['amount']); ?></td>
									<td class="total"><?= format('currency', $voucher['amount']); ?></td>
								<? } ?>

								<td class="remove-voucher center">
									<a href="<?= site_url('cart/cart/remove', 'cart_key=' . $voucher['key']); ?>" class="button remove"><?= _l("Remove"); ?></a>
								</td>
							</tr>
						<? } ?>
					<? } ?>

					<?= $cart_inline; ?>

					</tbody>
				</table>
			</div>
		<? } elseif ($cart_empty) { ?>
			<div class="center">
				<h2><?= _l("Your shopping cart is empty! Please check back here after you have added something to your cart!"); ?></h2>
			</div>
		<? } ?>

		<? if ($is_ajax) { ?>
			<?= $this->message->render(); ?>
		<? } ?>
	</form>

	<?= $cart_extend; ?>
</div>

<script type="text/javascript">
	var the_cart = $('#the_cart');

	function cart_update(context) {
		var data = context.closest('form').serializeArray();

		data.push({name: context.attr('name'), value: context.attr('value')});

		$.post("<?= $url_block_cart; ?>", data, function (html) {
			//Cart is empty (or something went wrong)
			if (!html) {
				location = "<?= $url_cart; ?>";
			}

			var new_cart = $('<div />').append(html).find('#the_cart');

			the_cart.html(new_cart.length ? new_cart.html() : html);

			the_cart.trigger('cart_loaded');
		}, 'html');

		return false;
	}
</script>
