<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div id="content">
		<?= $content_top; ?>
		<?= $this->breadcrumb->render(); ?>

		<h1><?= $head_title; ?></h1>
		<table class="list">
			<thead>
			<tr>
				<td class="left" colspan="2"><?= $text_order_detail; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left half">
					<? if (!empty($invoice_id)) { ?>
						<b><?= $text_invoice_id; ?></b> <?= $invoice_id; ?><br/>
					<? } ?>
					<b><?= $text_order_id; ?></b> #<?= $order_id; ?><br/>
					<b><?= $text_date_added; ?></b> <?= $date_added; ?>
				</td>
				<td class="left half">
					<? if (!empty($payment_method)) { ?>
						<b><?= $text_payment_method; ?></b> <?= $payment_method['title']; ?><br/>
					<? } ?>
					<? if (!empty($shipping_method)) { ?>
						<b><?= $text_shipping_method; ?></b> <?= $shipping_method['title']; ?>
					<? } ?>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="list">
			<thead>
			<tr>
				<td class="left"><?= $text_payment_address; ?></td>
				<? if (!empty($shipping_address)) { ?>
					<td class="left"><?= $text_shipping_address; ?></td>
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
				<td class="left"><?= $column_name; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
				<td class="right"><?= $column_price; ?></td>
				<td class="right"><?= $column_total; ?></td>
				<? if (!empty($products)) { ?>
					<td style="width: 1px;"></td>
				<? } ?>
			</tr>
			</thead>
			<tbody>
			<? foreach ($products as $product) { ?>
				<tr>
					<td class="left"><?= $product['name']; ?>
						<? foreach ($product['option'] as $option) { ?>
							<br/>
							&nbsp;
							<small> - <?= $option['name']; ?>: <?= $option['value']; ?></small>
						<? } ?></td>
					<td class="left"><?= $product['model']; ?></td>
					<td class="right"><?= $product['quantity']; ?></td>
					<td class="right"><?= $product['price']; ?></td>
					<td class="right"><?= $product['total']; ?></td>
					<td class="right">
						<? if ($product['return_policy']['days'] < 0) { ?>
							<div class='final_sale_small'><span class='final_sale'></span></div>
						<? } else { ?>
							<a href="<?= $product['return']; ?>">
								<img src="<?= HTTP_THEME_IMAGE . 'return.png'; ?>" alt="<?= $button_return; ?>"
								     title="<?= $button_return; ?>"/>
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
					<td class="left"><?= $text_comment; ?></td>
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
			<h2><?= $text_history; ?></h2>
			<table class="list">
				<thead>
				<tr>
					<td class="left"><?= $column_date_added; ?></td>
					<td class="left"><?= $column_status; ?></td>
					<td class="left"><?= $column_comment; ?></td>
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
		<div class='footer_text'>
			* <?= $final_sale_explanation; ?>
		</div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>