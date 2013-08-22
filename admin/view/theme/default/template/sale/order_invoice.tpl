<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?= $direction; ?>" lang="<?= $language; ?>"
      xml:lang="<?= $language; ?>">
<head>
	<title><?= $title; ?></title>
	<base href="<?= $base; ?>"/>
	<link rel="stylesheet" type="text/css" href="view/stylesheet/invoice.css"/>
</head>
<body>
<? foreach ($orders as $order) { ?>
	<div style="page-break-after: always;">
		<h1><?= $text_invoice; ?></h1>
		<table class="store">
			<tr>
				<td><?= $order['store_name']; ?><br/>
					<?= $order['store_address']; ?><br/>
					<?= $text_telephone; ?> <?= $order['store_telephone']; ?><br/>
					<? if ($order['store_fax']) { ?>
						<?= $text_fax; ?> <?= $order['store_fax']; ?><br/>
					<? } ?>
					<?= $order['store_email']; ?><br/>
					<?= $order['store_url']; ?></td>
				<td align="right" valign="top">
					<table>
						<tr>
							<td><b><?= $text_date_added; ?></b></td>
							<td><?= $order['date_added']; ?></td>
						</tr>
						<? if ($order['invoice_no']) { ?>
							<tr>
								<td><b><?= $text_invoice_no; ?></b></td>
								<td><?= $order['invoice_no']; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><b><?= $text_order_id; ?></b></td>
							<td><?= $order['order_id']; ?></td>
						</tr>
						<tr>
							<td><b><?= $text_payment_method; ?></b></td>
							<td><?= $order['payment_method']; ?></td>
						</tr>
						<? if ($order['shipping_method']) { ?>
							<tr>
								<td><b><?= $text_shipping_method; ?></b></td>
								<td><?= $order['shipping_method']; ?></td>
							</tr>
						<? } ?>
					</table>
				</td>
			</tr>
		</table>
		<table class="address">
			<tr class="heading">
				<td width="50%"><b><?= $text_to; ?></b></td>
				<td width="50%"><b><?= $text_ship_to; ?></b></td>
			</tr>
			<tr>
				<td><?= $order['payment_address']; ?><br/>
					<?= $order['email']; ?><br/>
					<?= $order['telephone']; ?></td>
				<td><?= $order['shipping_address']; ?></td>
			</tr>
		</table>
		<table class="product">
			<tr class="heading">
				<td><b><?= $column_product; ?></b></td>
				<td><b><?= $column_model; ?></b></td>
				<td align="right"><b><?= $column_quantity; ?></b></td>
				<td align="right"><b><?= $column_price; ?></b></td>
				<td align="right"><b><?= $column_total; ?></b></td>
			</tr>
			<? foreach ($order['product'] as $product) { ?>
				<tr>
					<td><?= $product['name']; ?>
						<? foreach ($product['option'] as $option) { ?>
							<br/>
							&nbsp;
							<small> - <?= $option['name']; ?>: <?= $option['value']; ?></small>
						<? } ?></td>
					<td><?= $product['model']; ?></td>
					<td align="right"><?= $product['quantity']; ?></td>
					<td align="right"><?= $product['price']; ?></td>
					<td align="right"><?= $product['total']; ?></td>
				</tr>
			<? } ?>
			<? foreach ($order['voucher'] as $voucher) { ?>
				<tr>
					<td align="left"><?= $voucher['description']; ?></td>
					<td align="left"></td>
					<td align="right">1</td>
					<td align="right"><?= $voucher['amount']; ?></td>
					<td align="right"><?= $voucher['amount']; ?></td>
				</tr>
			<? } ?>
			<? foreach ($order['total'] as $total) { ?>
				<tr>
					<td align="right" colspan="4"><b><?= $total['title']; ?>:</b></td>
					<td align="right"><?= $total['text']; ?></td>
				</tr>
			<? } ?>
		</table>
		<? if ($order['comment']) { ?>
			<table class="comment">
				<tr class="heading">
					<td><b><?= $column_comment; ?></b></td>
				</tr>
				<tr>
					<td><?= $order['comment']; ?></td>
				</tr>
			</table>
		<? } ?>
	</div>
<? } ?>
</body>
</html>