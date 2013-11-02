<?= $header; ?>
	<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= $head_title; ?></h1>

		<div class="buttons"><a onclick="window.open('<?= $invoice; ?>');" class="button"><?= $button_invoice; ?></a><a
				href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="section">
	<div class="vtabs">
		<a href="#tab-order"><?= $tab_order; ?></a>
		<a href="#tab-product"><?= $tab_product; ?></a>
		<a href="#tab-history"><?= $tab_order_history; ?></a>
		<? if (!empty($maxmind_id)) { ?>
			<a href="#tab-fraud"><?= $tab_fraud; ?></a>
		<? } ?>
	</div>

	<div id="tab-order" class="vtabs-content">
		<table class="form">
			<tr>
				<td><?= $text_order_id; ?></td>
				<td>#<?= $order_id; ?></td>
			</tr>
			<tr>
				<td><?= $text_invoice_no; ?></td>
				<td><?= $invoice_id; ?></td>
			</tr>
			<tr>
				<td><?= $text_store; ?></td>
				<td><a target="_blank" href="<?= $store['url']; ?>"><?= $store['name']; ?></a></td>
			</tr>
			<tr>
				<td><?= $text_customer; ?></td>
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
					<td><?= $text_customer_group; ?></td>
					<td><?= $customer_group; ?></td>
				</tr>
			<? } ?>
			<tr>
				<td><?= $text_email; ?></td>
				<td><?= $email; ?></td>
			</tr>
			<tr>
				<td><?= $text_telephone; ?></td>
				<td><?= $telephone; ?></td>
			</tr>
			<? if (!empty($fax)) { ?>
				<tr>
					<td><?= $text_fax; ?></td>
					<td><?= $fax; ?></td>
				</tr>
			<? } ?>
			<tr>
				<td><?= $text_payment_info; ?></td>
				<td>
					<p><?= $payment_method['title']; ?></p>
					<p><?= $payment_address; ?></p>
				</td>
			</tr>
			<? if (!empty($shipping_address)) { ?>
				<tr>
					<td><?= $text_shipping_info; ?></td>
					<td>
						<p><?= $shipping_method['title']; ?></p>
						<p><?= $shipping_address; ?></p>
					</td>
				</tr>
			<? } ?>
			<tr>
				<td><?= $text_total; ?></td>
				<td><?= $total; ?>
					<? if ($credit && $customer_id) { ?>
						<? if (!$credit_total) { ?>
							<span id="credit"><b>[</b> <a id="credit-add"><?= $text_credit_add; ?></a> <b>]</b></span>
						<? } else { ?>
							<span id="credit"><b>[</b> <a id="credit-remove"><?= $text_credit_remove; ?></a> <b>]</b></span>
						<? } ?>
					<? } ?>
				</td>
			</tr>
			<? if (!empty($reward) && $customer_id) { ?>
				<tr>
					<td><?= $text_reward; ?></td>
					<td><?= $reward; ?>
						<? if (!$reward_total) { ?>
							<span id="reward"><b>[</b> <a id="reward-add"><?= $text_reward_add; ?></a> <b>]</b></span>
						<? } else { ?>
							<span id="reward"><b>[</b> <a id="reward-remove"><?= $text_reward_remove; ?></a> <b>]</b></span>
						<? } ?></td>
				</tr>
			<? } ?>
			<? if ($order_status) { ?>
				<tr>
					<td><?= $text_order_status; ?></td>
					<td id="order-status"><?= $order_status['title']; ?></td>
				</tr>
			<? } ?>
			<? if (!empty($comment)) { ?>
				<tr>
					<td><?= $text_comment; ?></td>
					<td><?= $comment; ?></td>
				</tr>
			<? } ?>
			<? if (!empty($affiliate)) { ?>
				<tr>
					<td><?= $text_affiliate; ?></td>
					<td><a href="<?= $affiliate; ?>"><?= $affiliate_firstname; ?> <?= $affiliate_lastname; ?></a></td>
				</tr>
				<tr>
					<td><?= $text_commission; ?></td>
					<td><?= $commission; ?>
						<? if (!$commission_total) { ?>
							<span id="commission"><b>[</b> <a
									id="commission-add"><?= $text_commission_add; ?></a> <b>]</b></span>
						<? } else { ?>
							<span id="commission"><b>[</b> <a
									id="commission-remove"><?= $text_commission_remove; ?></a> <b>]</b></span>
						<? } ?></td>
				</tr>
			<? } ?>
			<? if ($ip) { ?>
				<tr>
					<td><?= $text_ip; ?></td>
					<td><?= $ip; ?></td>
				</tr>
			<? } ?>
			<? if ($forwarded_ip) { ?>
				<tr>
					<td><?= $text_forwarded_ip; ?></td>
					<td><?= $forwarded_ip; ?></td>
				</tr>
			<? } ?>
			<? if ($user_agent) { ?>
				<tr>
					<td><?= $text_user_agent; ?></td>
					<td><?= $user_agent; ?></td>
				</tr>
			<? } ?>
			<? if ($accept_language) { ?>
				<tr>
					<td><?= $text_accept_language; ?></td>
					<td><?= $accept_language; ?></td>
				</tr>
			<? } ?>
			<tr>
				<td><?= $text_date_added; ?></td>
				<td><?= $date_added; ?></td>
			</tr>
			<tr>
				<td><?= $text_date_modified; ?></td>
				<td><?= $date_modified; ?></td>
			</tr>
		</table>
	</div> <!-- /tab-order -->

	<div id="tab-product" class="vtabs-content">
		<table class="list">
			<thead>
			<tr>
				<td class="left"><?= $column_product; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
				<td class="right"><?= $column_price; ?></td>
				<td class="right"><?= $column_total; ?></td>
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
			<h3><?= $text_download; ?></h3>
			<table class="list">
				<thead>
				<tr>
					<td class="left"><b><?= $column_download; ?></b></td>
					<td class="left"><b><?= $column_filename; ?></b></td>
					<td class="right"><b><?= $column_remaining; ?></b></td>
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
	</div> <!-- /tab-products -->

	<div id="tab-history" class="vtabs-content">
		<div id="history">
			<table class="list">
				<thead>
				<tr>
					<td class="left"><b><?= $column_date_added; ?></b></td>
					<td class="left"><b><?= $column_comment; ?></b></td>
					<td class="left"><b><?= $column_status; ?></b></td>
					<td class="left"><b><?= $column_notify; ?></b></td>
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
						<td class="center" colspan="4"><?= $text_no_results; ?></td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		</div>

		<form id="add_history_form" action="" method="post">
			<table class="form">
				<tr>
					<td><?= $entry_order_status; ?></td>
					<td>
						<select name="order_status_id">
							<? foreach ($data_order_statuses as $os_id => $order_status) { ?>
								<option value="<?= $os_id; ?>" <?= $os_id == $order_status_id ? 'selected="selected"' : ''; ?>><?= $order_status['title']; ?></option>
							<? } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><?= $entry_notify; ?></td>
					<td><input type="checkbox" name="notify" value="1"/></td>
				</tr>
				<tr>
					<td><?= $entry_comment; ?></td>
					<td>
						<textarea name="comment" cols="40" rows="8"></textarea>

						<div>
							<input type="submit" id="button-history" class="button" value="<?= $button_add_history; ?>" />
						</div>
					</td>
				</tr>
			</table>
		</form>

	</div><!-- /tab-history -->

	<? if (!empty($maxmind_id)) { ?>
		<div id="tab-fraud" class="vtabs-content">
		<table class="form">
		<? if ($country_match) { ?>
			<tr>
				<td><?= $text_country_match; ?></td>
				<td><?= $country_match; ?></td>
			</tr>
		<? } ?>
		<? if ($country_code) { ?>
			<tr>
				<td><?= $text_country_code; ?></td>
				<td><?= $country_code; ?></td>
			</tr>
		<? } ?>
		<? if ($high_risk_country) { ?>
			<tr>
				<td><?= $text_high_risk_country; ?></td>
				<td><?= $high_risk_country; ?></td>
			</tr>
		<? } ?>
		<? if ($distance) { ?>
			<tr>
				<td><?= $text_distance; ?></td>
				<td><?= $distance; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_region) { ?>
			<tr>
				<td><?= $text_ip_region; ?></td>
				<td><?= $ip_region; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_city) { ?>
			<tr>
				<td><?= $text_ip_city; ?></td>
				<td><?= $ip_city; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_latitude) { ?>
			<tr>
				<td><?= $text_ip_latitude; ?></td>
				<td><?= $ip_latitude; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_longitude) { ?>
			<tr>
				<td><?= $text_ip_longitude; ?></td>
				<td><?= $ip_longitude; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_isp) { ?>
			<tr>
				<td><?= $text_ip_isp; ?></td>
				<td><?= $ip_isp; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_org) { ?>
			<tr>
				<td><?= $text_ip_org; ?></td>
				<td><?= $ip_org; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_asnum) { ?>
			<tr>
				<td><?= $text_ip_asnum; ?></td>
				<td><?= $ip_asnum; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_user_type) { ?>
			<tr>
				<td><?= $text_ip_user_type; ?></td>
				<td><?= $ip_user_type; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_country_confidence) { ?>
			<tr>
				<td><?= $text_ip_country_confidence; ?></td>
				<td><?= $ip_country_confidence; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_region_confidence) { ?>
			<tr>
				<td><?= $text_ip_region_confidence; ?></td>
				<td><?= $ip_region_confidence; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_city_confidence) { ?>
			<tr>
				<td><?= $text_ip_city_confidence; ?></td>
				<td><?= $ip_city_confidence; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_postal_confidence) { ?>
			<tr>
				<td><?= $text_ip_postal_confidence; ?></td>
				<td><?= $ip_postal_confidence; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_postal_code) { ?>
			<tr>
				<td><?= $text_ip_postal_code; ?></td>
				<td><?= $ip_postal_code; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_accuracy_radius) { ?>
			<tr>
				<td><?= $text_ip_accuracy_radius; ?></td>
				<td><?= $ip_accuracy_radius; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_net_speed_cell) { ?>
			<tr>
				<td><?= $text_ip_net_speed_cell; ?></td>
				<td><?= $ip_net_speed_cell; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_metro_code) { ?>
			<tr>
				<td><?= $text_ip_metro_code; ?></td>
				<td><?= $ip_metro_code; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_area_code) { ?>
			<tr>
				<td><?= $text_ip_area_code; ?></td>
				<td><?= $ip_area_code; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_time_zone) { ?>
			<tr>
				<td><?= $text_ip_time_zone; ?></td>
				<td><?= $ip_time_zone; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_region_name) { ?>
			<tr>
				<td><?= $text_ip_region_name; ?></td>
				<td><?= $ip_region_name; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_domain) { ?>
			<tr>
				<td><?= $text_ip_domain; ?></td>
				<td><?= $ip_domain; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_country_name) { ?>
			<tr>
				<td><?= $text_ip_country_name; ?></td>
				<td><?= $ip_country_name; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_continent_code) { ?>
			<tr>
				<td><?= $text_ip_continent_code; ?></td>
				<td><?= $ip_continent_code; ?></td>
			</tr>
		<? } ?>
		<? if ($ip_corporate_proxy) { ?>
			<tr>
				<td><?= $text_ip_corporate_proxy; ?></td>
				<td><?= $ip_corporate_proxy; ?></td>
			</tr>
		<? } ?>
		<? if ($anonymous_proxy) { ?>
			<tr>
				<td><?= $text_anonymous_proxy; ?></td>
				<td><?= $anonymous_proxy; ?></td>
			</tr>
		<? } ?>
		<? if ($proxy_score) { ?>
			<tr>
				<td><?= $text_proxy_score; ?></td>
				<td><?= $proxy_score; ?></td>
			</tr>
		<? } ?>
		<? if ($is_trans_proxy) { ?>
			<tr>
				<td><?= $text_is_trans_proxy; ?></td>
				<td><?= $is_trans_proxy; ?></td>
			</tr>
		<? } ?>
		<? if ($free_mail) { ?>
			<tr>
				<td><?= $text_free_mail; ?></td>
				<td><?= $free_mail; ?></td>
			</tr>
		<? } ?>
		<? if ($carder_email) { ?>
			<tr>
				<td><?= $text_carder_email; ?></td>
				<td><?= $carder_email; ?></td>
			</tr>
		<? } ?>
		<? if ($high_risk_username) { ?>
			<tr>
				<td><?= $text_high_risk_username; ?></td>
				<td><?= $high_risk_username; ?></td>
			</tr>
		<? } ?>
		<? if ($high_risk_password) { ?>
			<tr>
				<td><?= $text_high_risk_password; ?></td>
				<td><?= $high_risk_password; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_match) { ?>
			<tr>
				<td><?= $text_bin_match; ?></td>
				<td><?= $bin_match; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_country) { ?>
			<tr>
				<td><?= $text_bin_country; ?></td>
				<td><?= $bin_country; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_name_match) { ?>
			<tr>
				<td><?= $text_bin_name_match; ?></td>
				<td><?= $bin_name_match; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_name) { ?>
			<tr>
				<td><?= $text_bin_name; ?></td>
				<td><?= $bin_name; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_phone_match) { ?>
			<tr>
				<td><?= $text_bin_phone_match; ?></td>
				<td><?= $bin_phone_match; ?></td>
			</tr>
		<? } ?>
		<? if ($bin_phone) { ?>
			<tr>
				<td><?= $text_bin_phone; ?></td>
				<td><?= $bin_phone; ?></td>
			</tr>
		<? } ?>
		<? if ($customer_phone_in_billing_location) { ?>
			<tr>
				<td><?= $text_customer_phone_in_billing_location; ?></td>
				<td><?= $customer_phone_in_billing_location; ?></td>
			</tr>
		<? } ?>
		<? if ($ship_forward) { ?>
			<tr>
				<td><?= $text_ship_forward; ?></td>
				<td><?= $ship_forward; ?></td>
			</tr>
		<? } ?>
		<? if ($city_postal_match) { ?>
			<tr>
				<td><?= $text_city_postal_match; ?></td>
				<td><?= $city_postal_match; ?></td>
			</tr>
		<? } ?>
		<? if ($ship_city_postal_match) { ?>
			<tr>
				<td><?= $text_ship_city_postal_match; ?></td>
				<td><?= $ship_city_postal_match; ?></td>
			</tr>
		<? } ?>
		<? if ($score) { ?>
			<tr>
				<td><?= $text_score; ?></td>
				<td><?= $score; ?></td>
			</tr>
		<? } ?>
		<? if ($explanation) { ?>
			<tr>
				<td><?= $text_explanation; ?></td>
				<td><?= $explanation; ?></td>
			</tr>
		<? } ?>
		<? if ($risk_score) { ?>
			<tr>
				<td><?= $text_risk_score; ?></td>
				<td><?= $risk_score; ?></td>
			</tr>
		<? } ?>
		<? if ($queries_remaining) { ?>
			<tr>
				<td><?= $text_queries_remaining; ?></td>
				<td><?= $queries_remaining; ?></td>
			</tr>
		<? } ?>
		<? if ($maxmind_id) { ?>
			<tr>
				<td><?= $text_maxmind_id; ?></td>
				<td><?= $maxmind_id; ?></td>
			</tr>
		<? } ?>
		<? if ($error) { ?>
			<tr>
				<td><?= $text_error; ?></td>
				<td><?= $error; ?></td>
			</tr>
		<? } ?>
		</table>
		</div>
	<? } ?>
	</div>
	</div>
</div>


<script type="text/javascript"><!--
$('.vtabs a').tabs();
</script>

<?= $footer; ?>
