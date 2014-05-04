<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div id="order_info" class="content">
	<?= _area('top'); ?>
	<?= _breadcrumbs(); ?>

	<h1><?= _l("Order Information"); ?></h1>

	<table class="list">
		<thead>
			<tr>
				<td class="left" colspan="2"><?= _l("Order Details"); ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left half">
					<? if (!empty($invoice_id)) { ?>
						<b><?= _l("Invoice #:"); ?></b> <?= $invoice_id; ?><br/>
					<? } ?>
					<b><?= _l("Order ID:"); ?></b> #<?= $order_id; ?><br/>
					<b><?= _l("Date:"); ?></b> <?= $date_added; ?>
				</td>
				<td class="left half">
					<? if (!empty($payment_method)) { ?>
						<b><?= _l("Payment Method"); ?></b> <?= $payment_method['title']; ?><br/>
					<? } ?>
					<? if (!empty($shipping_method)) { ?>
						<b><?= _l("Shipping Method"); ?></b> <?= $shipping_method['title']; ?>
					<? } ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?= _l("Billing Address"); ?></td>
				<? if (!empty($shipping_address)) { ?>
					<td class="left"><?= _l("Delivery Address"); ?></td>
				<? } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left"><?= $payment_address; ?></td>
				<? if (!empty($shipping_address)) { ?>
					<td class="left"><?= $shipping_address; ?></td>
				<? } ?>
			</tr>
		</tbody>
	</table>
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
				<tr class="order_product">
					<td class="left">
						<div class="name"><?= $product['name']; ?></div>
						<div class="options">
							<? foreach ($product['options'] as $option) { ?>
								<div class="option">
									<span class="option_name"><?= $option['display_value']; ?></span>
								</div>
							<? } ?>
						</div>
					</td>
					<td class="left"><?= $product['model']; ?></td>
					<td class="right"><?= $product['quantity']; ?></td>
					<td class="right"><?= $product['price']; ?></td>
					<td class="right"><?= $product['total']; ?></td>
					<td class="right">
						<? if ($product['return_policy']['days'] < 0) { ?>
							<div class="final_sale_small"><span class="final_sale"></span></div>
						<? } else { ?>
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
					<td class="right"><?= $voucher['amount']; ?></td>
					<td class="right"><?= $voucher['amount']; ?></td>
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
					<td class="right"><?= $total['text']; ?></td>
					<? if ($products) { ?>
						<td></td>
					<? } ?>
				</tr>
			<? } ?>
		</tfoot>
	</table>
	<? if ($comment) { ?>
		<table class="list">
			<thead>
				<tr>
					<td class="left"><?= _l("Order Comments"); ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="left"><?= $comment; ?></td>
				</tr>
			</tbody>
		</table>
	<? } ?>
	<? if ($histories) { ?>
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
						<td class="left"><?= $history['date_added']; ?></td>
						<td class="left"><?= $history['order_status']['title']; ?></td>
						<td class="left"><?= $history['comment']; ?></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	<? } ?>
	<div class="footer_text">
		<?= _l("* A Product Marked as"); ?>
		<span class="final_sale"></span>
		<?= _l("cannot be returned. Read our"); ?>
		<a href="<?= $policies; ?>" class="colorbox"><?= _l("Return Policy"); ?></a>
		<?= _l("for details."); ?>
	</div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
