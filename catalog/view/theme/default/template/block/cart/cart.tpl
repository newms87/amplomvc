<div id='the_cart'>
	<? if (isset($no_price_display)) { ?>
		<span id='cart_no_price_display'><?= $no_price_display; ?></span>
	<? } ?>
	<form id='cart_form' action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="cart_form" value="1"/>

		<div class="cart-info">
			<table>
				<thead>
				<tr>
					<td class="image"><?= $column_image; ?></td>
					<td class="name"><?= $column_name; ?></td>
					<td class="model"><?= $column_model; ?></td>
					<td class="quantity"><?= $column_quantity; ?></td>

					<? if (!empty($show_return_policy)) { ?>
						<td class="return_policy"><?= $column_return_policy; ?></td>
					<? } ?>

					<? if (empty($no_price_display)) { ?>
						<td class="price"><?= $column_price; ?></td>
						<td class="total"><?= $column_total; ?></td>
					<? } ?>
				</tr>
				</thead>
				<tbody>
				<? foreach ($products as $product) { ?>
					<tr>
						<td class="image">
							<? if ($product['thumb']) { ?>
								<a href="<?= $product['href']; ?>">
									<img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>"
									     title="<?= $product['name']; ?>"/>
								</a>
							<? } ?>
						</td>
						<td class="name">
							<a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
							<? if (!$product['in_stock']) { ?>
								<span class="stock">***</span>
							<? } ?>

							<? if (!empty($product['selected_options'])) { ?>
								<div class="product_option_description">
									<? foreach ($product['selected_options'] as $selected_option) { ?>
										<div
											class='cart_product_option_value'><?= $text_option_bullet; ?><?= $selected_option['product_option']['display_name']; ?>
											: <?= $selected_option['value']; ?></div>
									<? } ?>
								</div>
							<? } ?>

							<? if ($product['reward']) { ?>
								<span class='cart_product_reward'><?= $product['reward']; ?></span>
							<? } ?>
						</td>
						<td class="model"><?= $product['model']; ?></td>
						<td class="quantity">
							<input type="text" name="quantity[<?= $product['key']; ?>]" value="<?= $product['quantity']; ?>"
							       size="1"/>
							<input class="block_cart_update" onclick="javascript: void(0)" type="image" name='action'
							       value='update' src="<?= HTTP_THEME_IMAGE . 'update.png'; ?>" alt="<?= $button_update; ?>"
							       title="<?= $button_update; ?>"/>
							<label><?= $text_update_cart; ?></label>
							<a class="block_cart_remove" onclick="return false;" href="<?= $product['remove']; ?>">
								<img src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>" alt="<?= $button_remove; ?>"
								     title="<?= $button_remove; ?>"/>
							</a>
						</td>

						<? if (!empty($show_return_policy)) { ?>
							<td class='return_policy'>
								<div><?= $product['return_policy']; ?></div>
							</td>
						<? } ?>

						<? if (!isset($no_price_display)) { ?>
							<td class="price"><?= $product['price']; ?></td>
							<td class="total"><?= $product['total']; ?></td>
						<? } ?>
					</tr>
				<? } ?>

				<? if (!empty($vouchers)) { ?>
					<? foreach ($vouchers as $key => $voucher) { ?>
						<tr>
							<td class="image"></td>
							<td class="name"><?= $voucher['description']; ?></td>
							<td class="model"></td>
							<td class="quantity">
								<input type="text" name="" value="1" size="1" disabled="disabled"/>
								<a href="<?= $voucher['remove']; ?>">
									<img src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>" alt="<?= $text_remove; ?>"
									     title="<?= $button_remove; ?>"/>
								</a>
							</td>
							<? if (!isset($no_price_display)) { ?>
								<td class="price"><?= $voucher['amount']; ?></td>
								<td class="total"><?= $voucher['amount']; ?></td>
							<? } ?>
						</tr>
					<? } ?>
				<? } ?>
				</tbody>
			</table>
		</div>
	</form>
</div>

<? if ($ajax_cart) { ?>
	<script type="text/javascript">//<!--
		$('.block_cart_update').click(function () {
			form = $('form#cart_form');

			data = form.find('input[value=update], select, input:checked, input[type="text"], input[type="hidden"], textarea');

			if (typeof handle_ajax_cart_preload == 'function') {
				handle_ajax_cart_preload('update', data);
			}

			$('#the_cart').load(form.attr('action'), data, function () {
				if (typeof handle_ajax_cart_load == 'function') {
					handle_ajax_cart_load('update', data);
				}
			});

			return false;
		});

		$('.block_cart_remove').click(function () {
			context = $(this);

			if (typeof handle_ajax_cart_preload == 'function') {
				handle_ajax_cart_preload('remove', context);
			}

			$('#the_cart').load(context.attr('href'), {}, function () {
				if (typeof handle_ajax_cart_load == 'function') {
					handle_ajax_cart_load('remove', context);
				}
			});

			return false;
		});
		//--></script>
<? } ?>