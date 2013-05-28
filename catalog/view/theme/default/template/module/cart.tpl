<div id="cart">
	<div class="heading">
		<h4><?= $heading_title; ?></h4>
		<a><span id="cart-total"><?= $text_items; ?></span></a></div>
	<div class="content">
		<? if ($products || $vouchers) { ?>
		<div class="mini-cart-info">
			<table>
				<? foreach ($products as $product) { ?>
				<tr>
					<td class="image"><? if ($product['thumb']) { ?>
						<a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" title="<?= $product['name']; ?>" /></a>
						<? } ?></td>
					<td class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
						<div>
							<? foreach ($product['option'] as $option) { ?>
							- <small><?= $option['name']; ?> <?= $option['value']; ?></small><br />
							<? } ?>
						</div></td>
					<td class="quantity">x&nbsp;<?= $product['quantity']; ?></td>
					<td class="total"><?= $product['total']; ?></td>
					<td class="remove"><img src="<?= HTTP_THEME_IMAGE . 'remove-small.png'; ?>" alt="<?= $button_remove; ?>" title="<?= $button_remove; ?>" onclick="$('#cart').load("<?= HTTP_CATALOG . "index.php?route=module/cart"; ?>" + '&remove=<?= $product['key']; ?> #cart > *');" /></td>
				</tr>
				<? } ?>
				<? foreach ($vouchers as $voucher) { ?>
				<tr>
					<td class="image"></td>
					<td class="name"><?= $voucher['description']; ?></td>
					<td class="quantity">x&nbsp;1</td>
					<td class="total"><?= $voucher['amount']; ?></td>
					<td class="remove"><img src="<?= HTTP_THEME_IMAGE . 'remove-small.png'; ?>" alt="<?= $button_remove; ?>" title="<?= $button_remove; ?>" onclick="$('#cart').load("<?= HTTP_CATALOG . "index.php?route=module/cart"; ?>" + '&remove=<?= $voucher['key']; ?> #cart > *');" /></td>
				</tr>
				<? } ?>
			</table>
		</div>
		<div class="mini-cart-total">
			<table>
				<? foreach ($totals as $total) { ?>
				<tr>
					<td align="right"><b><?= $total['title']; ?>:</b></td>
					<td align="right"><?= $total['text']; ?></td>
				</tr>
				<? } ?>
			</table>
		</div>
		<div class="checkout"><a href="<?= $cart; ?>"><?= $text_cart; ?></a> | <a href="<?= $checkout; ?>"><?= $text_checkout; ?></a></div>
		<? } else { ?>
		<div class="empty"><?= $text_empty; ?></div>
		<? } ?>
	</div>
</div>
