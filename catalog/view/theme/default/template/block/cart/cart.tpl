<div id="the_cart">
	<? if (isset($no_price_display)) { ?>
		<span id="cart_no_price_display"><?= $no_price_display; ?></span>
	<? } ?>

	<form id="cart_form" action="" method="post" enctype="multipart/form-data">
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
								<td class="return_policy"><?= _l("Return Period"); ?></td>
							<? } ?>

							<? if (empty($no_price_display)) { ?>
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
										<? if ($cart_product['thumb']) { ?>
											<a href="<?= $cart_product['href']; ?>">
												<img src="<?= $cart_product['thumb']; ?>" alt="<?= $product['name']; ?>" title="<?= $product['name']; ?>"/>
											</a>
										<? } ?>
									</td>
									<td class="name">
										<a href="<?= $cart_product['href']; ?>"><?= $product['name']; ?></a>
										<? if (!$cart_product['in_stock']) { ?>
											<span class="out_of_stock"></span>
										<? } ?>

										<? if (!empty($cart_product['options'])) { ?>
											<div class="product_option_description">
												<? foreach ($cart_product['options'] as $product_option_id => $product_option_values) { ?>
													<? foreach ($product_option_values as $product_option_value) { ?>
														<? if ($product_option_value['display_value']) { ?>
														<div class="cart_product_option_value">
															<?= $product_option_value['display_value']; ?>
														</div>
														<? } else { ?>
															<div class="cart_product_option_value">
																<span class="name"><?= $product_option_value['name']; ?></span>
																<span class="value"><?= $product_option_value['value']; ?></span>
															</div>
														<? } ?>
													<? } ?>
												<? } ?>
											</div>
										<? } ?>

										<? if ($product['reward']) { ?>
											<span class="cart_product_reward"><?= $product['reward']; ?></span>
										<? } ?>
									</td>
									<td class="model"><?= $product['model']; ?></td>
									<td class="quantity">
										<input type="text" name="quantity[<?= $cart_product['key']; ?>]" value="<?= $cart_product['quantity']; ?>" size="1"/>
										<input class="update" type="image" name="cart_update" value="1" onclick="return cart_update($(this));" src="<?= URL_THEME_IMAGE . 'update.png'; ?>" alt="<?= _l("Update"); ?>" title="<?= _l("Update your Cart"); ?>"/>
										<label><?= _l("Update"); ?></label>
									</td>

									<? if (!empty($show_return_policy)) { ?>
										<td class="return_policy">
											<div><?= $product['return_policy']; ?></div>
										</td>
									<? } ?>

									<? if (!isset($no_price_display)) { ?>
										<td class="price"><?= $cart_product['price_display']; ?></td>
										<td class="total"><?= $cart_product['total_display']; ?></td>
									<? } ?>

									<td class="center">
										<a href="<?= $cart_product['remove']; ?>" class="button remove"></a>
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

									<? if (!isset($no_price_display)) { ?>
										<td class="price"><?= $voucher['amount']; ?></td>
										<td class="total"><?= $voucher['amount']; ?></td>
									<? } ?>

									<td class="remove_voucher center"><input type="submit" class="button remove" name="cart_remove_voucher" value="<?= $cart_voucher['key']; ?>" onclick="return cart_update($(this));"></td>
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
	</form>

	<?= $cart_extend; ?>

	<? //Handle Ajax messages
	if (!empty($messages)) {
		?>
		<script type="text/javascript">
			<? foreach ($messages as $type => $msgs) { ?>
			<? foreach ($msgs as $message) { ?>
			show_msg("<?= addslashes($type); ?>", "<?= addslashes($message); ?>");
			<? } ?>
			<? } ?>
		</script>
	<? } ?>

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
