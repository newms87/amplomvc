<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=320, target-densitydpi=device-dpi">
	</head>
	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">
			<a href="<?= $store['url']; ?>" title="<?= $store['name']; ?>">
				<img src="<?= $logo; ?>" alt="<?= $store['name']; ?>" style="margin-bottom: 20px; border: none;"/>
			</a>

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<?= _l("Thank you for your purchase from %s! Your order has been received and will be processed once payment has been confirmed.", $store['name']); ?>
			</p>

			<? if ($customer_id) { ?>
				<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("To view your order click on the link below:"); ?></p>
				<p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?= $link; ?>"><?= $link; ?></a></p>
			<? } ?>
			<? if (!empty($downloads_url)) { ?>
				<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Once your payment has been confirmed you can click on the link below to access your downloadable products:"); ?></p>
				<p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?= $downloads_url; ?>"><?= $downloads_url; ?></a></p>
			<? } ?>
			<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
				<thead>
					<tr>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2">
							<?= _l("Order Details:"); ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
							<b><?= _l("Order ID:"); ?></b> <?= $order_id; ?><br/>
							<b><?= _l("Date Added:"); ?></b> <?= $date_added; ?><br/>
							<b><?= _l("Payment Method:"); ?></b> <?= $payment_method['title']; ?><br/>
							<? if ($shipping_method) { ?>
								<b><?= _l("Shipping Method:"); ?></b> <?= $shipping_method['title']; ?>
							<? } ?>
						</td>

						<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
							<b><?= _l("Email:"); ?></b> <?= $email; ?><br/>
							<b><?= _l("Phone:"); ?></b> <?= $telephone; ?><br/>
							<b><?= _l("Customer IP Address:"); ?></b> <?= $ip; ?><br/>
						</td>
					</tr>
				</tbody>
			</table>
			<? if ($comment) { ?>
				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
					<thead>
						<tr>
							<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
								<?= _l("Order Comments:"); ?>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $comment; ?>
							</td>
						</tr>
					</tbody>
				</table>
			<? } ?>
			<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
				<thead>
					<tr>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
							<?= _l("Billing Address"); ?>
						</td>
						<? if ($shipping_address_html) { ?>
							<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
								<?= _l("Delivery Address"); ?>
							</td>
						<? } ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
							<?= $payment_address_html; ?>
						</td>
						<? if ($shipping_address_html) { ?>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $shipping_address_html; ?>
							</td>
						<? } ?>
					</tr>
				</tbody>
			</table>
			<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
				<thead>
					<tr>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
							<?= _l("Product"); ?>
						</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
							<?= _l("Model #"); ?>
						</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">
							<?= _l("Quantity"); ?>
						</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">
							<?= _l("Price"); ?>
						</td>
						<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">
							<?= _l("Total"); ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<? foreach ($order_products as $product) { ?>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $product['name']; ?>

								<? foreach ($product['options'] as $option) { ?>
									<br/>
									&nbsp;
									<small> - <?= $option['name']; ?>: <?= $option['display_value'] ? $option['display_value'] : $option['value']; ?></small>
								<? } ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $product['model']; ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $product['quantity']; ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $product['price']; ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $product['total']; ?>
							</td>
						</tr>
					<? } ?>
					<? foreach ($order_vouchers as $voucher) { ?>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $voucher['description']; ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"></td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								1
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $voucher['amount']; ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $voucher['amount']; ?>
							</td>
						</tr>
					<? } ?>
				</tbody>
				<tfoot>
					<? foreach ($order_totals as $total) { ?>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="4">
								<b><?= $total['title']; ?>:</b>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
								<?= $total['text']; ?>
							</td>
						</tr>
					<? } ?>
				</tfoot>
			</table>
			<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Please reply to this email if you have any questions."); ?></p>
		</div>
	</body>
</html>
