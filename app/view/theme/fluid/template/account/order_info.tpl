<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="order-info-page" class="content order-<?= $order_id; ?>">
	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<h1><?= _l("Order Information"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="order-info row">
		<div class="wrap">
			<div class="order-details col xs-12 sm-6 md-4">
				<h2><?= _l("Order Details"); ?></h2>


				<div class="info-list">
					<? if (!empty($invoice_id)) { ?>
						<div class="info-item invoice-id">
							<span class="label"><?= _l("Invoice #:"); ?></span>
							<span class="info"><?= $invoice_id; ?></span>
						</div>
					<? } ?>
					<div class="info-item order-id">
						<span class="label"><?= _l("Order ID:"); ?></span>
						<span class="info"><?= $order_id; ?></span>
					</div>
					<div class="info-item invoice-id">
						<span class="label"><?= _l("Date:"); ?></span>
						<span class="info"><?= format('date', $date_added, 'short'); ?></span>
					</div>
				</div>
			</div>

			<div class="payment-details col xs-12 sm-6 md-4">
				<h2><?= _l("Payment Details"); ?></h2>

				<div class="info-list">
					<? if (!empty($payment_method)) { ?>
						<div class="info-item payment-method">
							<span class="label"><?= _l("Payment Method"); ?></span>
							<span class="info"><?= $payment_method['title']; ?></span>
						</div>
					<? } ?>
				</div>

				<div class="payment-address">
					<?= format('address', $payment_address); ?>
				</div>
			</div>

			<? if ($shipping_address) { ?>
				<div class="shipping-details col xs-12 sm-6 md-4">
					<h2><?= _l("Shipping Details"); ?></h2>

					<div class="info-list">
						<? if (!empty($shipping_method)) { ?>
							<div class="info-item shipping-method">
								<span class="label"><?= _l("Shipping Method"); ?></span>
								<span class="info"><?= $shipping_method['title']; ?></span>
							</div>
						<? } ?>
					</div>

					<div class="shipping-address">
						<?= format('address', $shipping_address); ?>
					</div>
				</div>
			<? } ?>

			<div class="order-products col xs-12">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= _l("Product"); ?></td>
						<td class="left"><?= _l("Model"); ?></td>
						<td class="right"><?= _l("Quantity"); ?></td>
						<td class="right"><?= _l("Price"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
						<? if (!empty($products)) { ?>
							<td></td>
						<? } ?>
					</tr>
					</thead>
					<tbody>
					<? foreach ($products as $product) { ?>
						<tr class="order-product">
							<td class="left">
								<div class="name"><?= $product['name']; ?></div>
								<div class="options">
									<? foreach ($product['options'] as $option) { ?>
										<div class="option">
											<span class="option-name"><?= charlimit(($option['name'] ? $option['name'] . ': ' : '') . $option['value'], 30); ?></span>
										</div>
									<? } ?>
								</div>
							</td>
							<td class="left"><?= $product['model']; ?></td>
							<td class="right"><?= $product['quantity']; ?></td>
							<td class="right"><?= format('currency', $product['price'], $currency_code, $currency_value); ?></td>
							<td class="right"><?= format('currency', $product['total'], $currency_code, $currency_value); ?></td>
							<td class="right">
								<? if ($product['return_policy']['days'] < 0) { ?>
									<div class="final-sale-small another-one"><span class="final-sale"></span></div>
								<? } elseif (option('config_product_returns')) { ?>
									<a href="<?= $product['return']; ?>">
										<img src="<?= theme_url('image/return.png'); ?>" alt="<?= _l("Return Products"); ?>" title="<?= _l("Return Products"); ?>"/>
									</a>
								<? } ?>
							</td>
						</tr>
					<? } ?>
					<? foreach ($vouchers as $voucher) { ?>
						<tr>
							<td class="left"><?= $voucher['description']; ?></td>
							<td class="left"></td>
							<td class="right">1</td>
							<td class="right"><?= format('currency', $voucher['amount'], $currency_code, $currency_value); ?></td>
							<td class="right"><?= format('currency', $voucher['amount'], $currency_code, $currency_value); ?></td>
							<? if ($products) { ?>
								<td></td>
							<? } ?>
						</tr>
					<? } ?>
					</tbody>
					<tfoot>
					<? foreach ($totals as $total) { ?>
						<tr>
							<td colspan="3"></td>
							<td class="right"><b><?= $total['title']; ?>:</b></td>
							<td class="right"><?= format('currency', $total['value'], $currency_code, $currency_value); ?></td>
							<? if ($products) { ?>
								<td></td>
							<? } ?>
						</tr>
					<? } ?>
					</tfoot>
				</table>
			</div>

			<? if ($comment) { ?>
				<div class="xs-12 col order-comments">
					<h2><?= _l("Order Comments"); ?></h2>

					<div class="comment"><?= nl2br($comment); ?></div>
				</div>
			<? } ?>

			<? if ($histories) { ?>
				<div class="order-history col xs-12">
					<h2><?= _l("Order History"); ?></h2>
					<table class="list">
						<thead>
						<tr>
							<td class="left"><?= _l("Date"); ?></td>
							<td class="left"><?= _l("Status"); ?></td>
							<td class="left"><?= _l("Comments"); ?></td>
						</tr>
						</thead>
						<tbody>
						<? foreach ($histories as $history) { ?>
							<tr>
								<td class="left"><?= format('date', $history['date_added'], 'short'); ?></td>
								<td class="left"><?= $history['order_status']['title']; ?></td>
								<td class="left"><?= nl2br($history['comment']); ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				</div>
			<? } ?>
			<div class="footer-text col xs-12">
				<?= _l("* A Product Marked as Final Sale cannot be returned. Read our <a href=\"%s\" class=\"colorbox\">Return Policy</a> for details.", site_url('page/content', 'page_id=' . option('config_shipping_return_page_id'))); ?>
			</div>
		</div>
	</div>

	<div class="button-row row">
		<div class="wrap">
			<div class="buttons">
				<div class="right"><a href="<?= site_url('account/order'); ?>" class="button"><?= _l("Continue"); ?></a>
				</div>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>

</section>

<?= call('common/footer'); ?>
