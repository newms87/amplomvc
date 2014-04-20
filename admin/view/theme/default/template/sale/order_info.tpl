<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= _l("Orders"); ?></h1>

			<div class="buttons"><a onclick="window.open('<?= $invoice; ?>');" class="button"><?= _l("Invoice"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<div class="vtabs">
				<a href="#tab-order"><?= _l("Order"); ?></a>
				<a href="#tab-product"><?= _("Product"); ?></a>
				<a href="#tab-history"><?= _l("Order History"); ?></a>
			</div>

			<div id="tab-order" class="vtabs-content">
				<table class="form">
					<tr>
						<td><?= _l("Order ID:"); ?></td>
						<td>#<?= $order_id; ?></td>
					</tr>
					<tr>
						<td><?= _l("Invoice No.:"); ?></td>
						<td><?= $invoice_id; ?></td>
					</tr>
					<tr>
						<td><?= _l("Store:"); ?></td>
						<td><a target="_blank" href="<?= $store['url']; ?>"><?= $store['name']; ?></a></td>
					</tr>
					<tr>
						<td><?= _l("Customer:"); ?></td>
						<td>
							<? if (!empty($url_customer)) { ?>
								<a target="_blank" href="<?= $url_customer; ?>"><?= $firstname; ?> <?= $lastname; ?></a>
							<? } else { ?>
								<?= $firstname; ?> <?= $lastname; ?>
							<? } ?>
						</td>
					</tr>
					<? if (!empty($customer_group)) { ?>
						<tr>
							<td><?= _l("Customer Group:"); ?></td>
							<td><?= $customer_group; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("E-Mail:"); ?></td>
						<td><?= $email; ?></td>
					</tr>
					<tr>
						<td><?= _l("Telephone:"); ?></td>
						<td><?= $telephone; ?></td>
					</tr>
					<? if (!empty($fax)) { ?>
						<tr>
							<td><?= _l("Fax:"); ?></td>
							<td><?= $fax; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Payment Details:"); ?></td>
						<td>
							<p><?= $payment_method['title']; ?></p>

							<p><?= $payment_address; ?></p>
						</td>
					</tr>
					<? if (!empty($shipping_address)) { ?>
						<tr>
							<td><?= _l("Shipping Details:"); ?></td>
							<td>
								<p><?= $shipping_method['title']; ?></p>

								<p><?= $shipping_address; ?></p>
							</td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Total:"); ?></td>
						<td><?= $total; ?>
							<? if ($credit && $customer_id) { ?>
								<? if (!$credit_total) { ?>
									<span id="credit"><b>[</b> <a id="credit-add"><?= _l("Add Credit"); ?></a> <b>]</b></span>
								<? } else { ?>
									<span id="credit"><b>[</b> <a id="credit-remove"><?= _l("Remove Credit"); ?></a> <b>]</b></span>
								<? } ?>
							<? } ?>
						</td>
					</tr>
					<? if (!empty($reward) && $customer_id) { ?>
						<tr>
							<td><?= _l("Reward Points:"); ?></td>
							<td><?= $reward; ?>
								<? if (!$reward_total) { ?>
									<span id="reward"><b>[</b> <a id="reward-add"><?= _l("Add Reward Points"); ?></a> <b>]</b></span>
								<? } else { ?>
									<span id="reward"><b>[</b> <a id="reward-remove"><?= _l("Remove Reward Points"); ?></a> <b>]</b></span>
								<? } ?></td>
						</tr>
					<? } ?>
					<? if ($order_status) { ?>
						<tr>
							<td><?= _l("Order Status:"); ?></td>
							<td id="order-status"><?= $order_status['title']; ?></td>
						</tr>
					<? } ?>
					<? if (!empty($comment)) { ?>
						<tr>
							<td><?= _l("Comment:"); ?></td>
							<td><?= $comment; ?></td>
						</tr>
					<? } ?>
					<? if ($ip) { ?>
						<tr>
							<td><?= _l("IP Address:"); ?></td>
							<td><?= $ip; ?></td>
						</tr>
					<? } ?>
					<? if ($forwarded_ip) { ?>
						<tr>
							<td><?= _l("Forwarded IP:"); ?></td>
							<td><?= $forwarded_ip; ?></td>
						</tr>
					<? } ?>
					<? if ($user_agent) { ?>
						<tr>
							<td><?= _l("User Agent:"); ?></td>
							<td><?= $user_agent; ?></td>
						</tr>
					<? } ?>
					<? if ($accept_language) { ?>
						<tr>
							<td><?= _l("Accept Language:"); ?></td>
							<td><?= $accept_language; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Date Added:"); ?></td>
						<td><?= $date_added; ?></td>
					</tr>
					<tr>
						<td><?= _l("Date Modified:"); ?></td>
						<td><?= $date_modified; ?></td>
					</tr>
				</table>
			</div>
			<!-- /tab-order -->

			<div id="tab-product" class="vtabs-content">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= _l("Product"); ?></td>
						<td class="left"><?= _l("Model"); ?></td>
						<td class="right"><?= _l("Quantity"); ?></td>
						<td class="right"><?= _l("Unit Price"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
					</tr>
					</thead>
					<tbody>
					<? foreach ($products as $product) { ?>
						<tr>
							<td class="left">
								<a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
								<? foreach ($product['options'] as $option) { ?>
									<div class="product_option">
										<span class="name"><?= $option['name']; ?>:</span>
										<span class="value"><?= $option['value']; ?></span>
									</div>
								<? } ?>
							</td>
							<td class="left"><?= $product['model']; ?></td>
							<td class="right"><?= $product['quantity']; ?></td>
							<td class="right"><?= $product['price_display']; ?></td>
							<td class="right"><?= $product['total_display']; ?></td>
						</tr>
					<? } ?>
					<? foreach ($vouchers as $voucher) { ?>
						<tr>
							<td class="left"><a href="<?= $voucher['href']; ?>"><?= $voucher['description']; ?></a></td>
							<td class="left"></td>
							<td class="right">1</td>
							<td class="right"><?= $voucher['amount']; ?></td>
							<td class="right"><?= $voucher['amount']; ?></td>
						</tr>
					<? } ?>
					<? foreach ($totals as $totals) { ?>
						<tr class="totals">
							<td colspan="4" class="right"><?= $totals['title']; ?>:</td>
							<td class="right"><?= $totals['value_display']; ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>

				<? if (!empty($downloads)) { ?>
					<h3><?= _l("Order Downloads"); ?></h3>
					<table class="list">
						<thead>
						<tr>
							<td class="left"><b><?= _l("Download Name"); ?></b></td>
							<td class="left"><b><?= _l("Filename"); ?></b></td>
							<td class="right"><b><?= _l("Remaining Downloads"); ?></b></td>
						</tr>
						</thead>
						<tbody>
						<? foreach ($downloads as $download) { ?>
							<tr>
								<td class="left"><?= $download['name']; ?></td>
								<td class="left"><?= $download['filename']; ?></td>
								<td class="right"><?= $download['remaining']; ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				<? } ?>
			</div>
			<!-- /tab-products -->

			<div id="tab-history" class="vtabs-content">
				<div id="history">
					<table class="list">
						<thead>
						<tr>
							<td class="left"><b><?= _l("Date Added"); ?></b></td>
							<td class="left"><b><?= _l("Comment"); ?></b></td>
							<td class="left"><b><?= _l("Status"); ?></b></td>
							<td class="left"><b><?= _l("Customer Notified"); ?></b></td>
						</tr>
						</thead>
						<tbody>
						<? if (!empty($histories)) { ?>
							<? foreach ($histories as $history) { ?>
								<tr>
									<td class="left"><?= $history['date_added']; ?></td>
									<td class="left"><?= $history['comment']; ?></td>
									<td class="left"><?= $history['status']; ?></td>
									<td class="left"><?= $history['notify']; ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="4"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				</div>

				<form id="add_history_form" action="" method="post">
					<table class="form">
						<tr>
							<td><?= _l("Order Status:"); ?></td>
							<td>
								<select name="order_status_id">
									<? foreach ($data_order_statuses as $os_id => $order_status) { ?>
										<option value="<?= $os_id; ?>" <?= $os_id == $order_status_id ? 'selected="selected"' : ''; ?>><?= $order_status['title']; ?></option>
									<? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?= _l("Notify Customer:"); ?></td>
							<td><input type="checkbox" name="notify" value="1"/></td>
						</tr>
						<tr>
							<td><?= _l("Comment:"); ?></td>
							<td>
								<textarea name="comment" cols="40" rows="8"></textarea>

								<div>
									<input type="submit" id="button-history" class="button" value="<?= _l("Add History"); ?>"/>
								</div>
							</td>
						</tr>
					</table>
				</form>

			</div>
			<!-- /tab-history -->
		</div>
	</div>
</div>


<script type="text/javascript">
	$('.vtabs a').tabs();
</script>

<?= $this->call('common/footer'); ?>
