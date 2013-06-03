<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="window.open('<?= $invoice; ?>');" class="button"><?= $button_invoice; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div class="vtabs"><a href="#tab-order"><?= $tab_order; ?></a><a href="#tab-payment"><?= $tab_payment; ?></a>
				<? if ($shipping_method) { ?>
				<a href="#tab-shipping"><?= $tab_shipping; ?></a>
				<? } ?>
				<a href="#tab-product"><?= $tab_product; ?></a><a href="#tab-history"><?= $tab_order_history; ?></a>
				<? if ($maxmind_id) { ?>
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
						<td><? if ($invoice_no) { ?>
							<?= $invoice_no; ?>
							<? } else { ?>
							<span id="invoice"><b>[</b> <a id="invoice-generate"><?= $text_generate; ?></a> <b>]</b></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $text_store_name; ?></td>
						<td><?= $store_name; ?></td>
					</tr>
					<tr>
						<td><?= $text_store_url; ?></td>
						<td><a onclick="window.open('<?= $store_url; ?>');"><u><?= $store_url; ?></u></a></td>
					</tr>
					<? if ($customer) { ?>
					<tr>
						<td><?= $text_customer; ?></td>
						<td><a href="<?= $customer; ?>"><?= $firstname; ?> <?= $lastname; ?></a></td>
					</tr>
					<? } else { ?>
					<tr>
						<td><?= $text_customer; ?></td>
						<td><?= $firstname; ?> <?= $lastname; ?></td>
					</tr>
					<? } ?>
					<? if ($customer_group) { ?>
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
					<? if ($fax) { ?>
					<tr>
						<td><?= $text_fax; ?></td>
						<td><?= $fax; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_total; ?></td>
						<td><?= $total; ?>
							<? if ($credit && $customer) { ?>
							<? if (!$credit_total) { ?>
							<span id="credit"><b>[</b> <a id="credit-add"><?= $text_credit_add; ?></a> <b>]</b></span>
							<? } else { ?>
							<span id="credit"><b>[</b> <a id="credit-remove"><?= $text_credit_remove; ?></a> <b>]</b></span>
							<? } ?>
							<? } ?></td>
					</tr>
					<? if ($reward && $customer) { ?>
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
						<td id="order-status"><?= $order_status; ?></td>
					</tr>
					<? } ?>
					<? if ($comment) { ?>
					<tr>
						<td><?= $text_comment; ?></td>
						<td><?= $comment; ?></td>
					</tr>
					<? } ?>
					<? if ($affiliate) { ?>
					<tr>
						<td><?= $text_affiliate; ?></td>
						<td><a href="<?= $affiliate; ?>"><?= $affiliate_firstname; ?> <?= $affiliate_lastname; ?></a></td>
					</tr>
					<tr>
						<td><?= $text_commission; ?></td>
						<td><?= $commission; ?>
							<? if (!$commission_total) { ?>
							<span id="commission"><b>[</b> <a id="commission-add"><?= $text_commission_add; ?></a> <b>]</b></span>
							<? } else { ?>
							<span id="commission"><b>[</b> <a id="commission-remove"><?= $text_commission_remove; ?></a> <b>]</b></span>
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
			</div>
			<div id="tab-payment" class="vtabs-content">
				<table class="form">
					<tr>
						<td><?= $text_firstname; ?></td>
						<td><?= $payment_firstname; ?></td>
					</tr>
					<tr>
						<td><?= $text_lastname; ?></td>
						<td><?= $payment_lastname; ?></td>
					</tr>
					<? if ($payment_company) { ?>
					<tr>
						<td><?= $text_company; ?></td>
						<td><?= $payment_company; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_address_1; ?></td>
						<td><?= $payment_address_1; ?></td>
					</tr>
					<? if ($payment_address_2) { ?>
					<tr>
						<td><?= $text_address_2; ?></td>
						<td><?= $payment_address_2; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_city; ?></td>
						<td><?= $payment_city; ?></td>
					</tr>
					<? if ($payment_postcode) { ?>
					<tr>
						<td><?= $text_postcode; ?></td>
						<td><?= $payment_postcode; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_zone; ?></td>
						<td><?= $payment_zone; ?></td>
					</tr>
					<? if ($payment_zone_code) { ?>
					<tr>
						<td><?= $text_zone_code; ?></td>
						<td><?= $payment_zone_code; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_country; ?></td>
						<td><?= $payment_country; ?></td>
					</tr>
					<tr>
						<td><?= $text_payment_method; ?></td>
						<td><?= $payment_method; ?></td>
					</tr>
				</table>
			</div>
			<? if ($shipping_method) { ?>
			<div id="tab-shipping" class="vtabs-content">
				<table class="form">
					<tr>
						<td><?= $text_firstname; ?></td>
						<td><?= $shipping_firstname; ?></td>
					</tr>
					<tr>
						<td><?= $text_lastname; ?></td>
						<td><?= $shipping_lastname; ?></td>
					</tr>
					<? if ($shipping_company) { ?>
					<tr>
						<td><?= $text_company; ?></td>
						<td><?= $shipping_company; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_address_1; ?></td>
						<td><?= $shipping_address_1; ?></td>
					</tr>
					<? if ($shipping_address_2) { ?>
					<tr>
						<td><?= $text_address_2; ?></td>
						<td><?= $shipping_address_2; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_city; ?></td>
						<td><?= $shipping_city; ?></td>
					</tr>
					<? if ($shipping_postcode) { ?>
					<tr>
						<td><?= $text_postcode; ?></td>
						<td><?= $shipping_postcode; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_zone; ?></td>
						<td><?= $shipping_zone; ?></td>
					</tr>
					<? if ($shipping_zone_code) { ?>
					<tr>
						<td><?= $text_zone_code; ?></td>
						<td><?= $shipping_zone_code; ?></td>
					</tr>
					<? } ?>
					<tr>
						<td><?= $text_country; ?></td>
						<td><?= $shipping_country; ?></td>
					</tr>
					<? if ($shipping_method) { ?>
					<tr>
						<td><?= $text_shipping_method; ?></td>
						<td><?= $shipping_method; ?></td>
					</tr>
					<? } ?>
				</table>
			</div>
			<? } ?>
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
							<td class="left"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
								<? foreach ($product['option'] as $option) { ?>
								<br />
								<? if ($option['type'] != 'file') { ?>
								&nbsp;<small> - <?= $option['name']; ?>: <?= $option['value']; ?></small>
								<? } else { ?>
								&nbsp;<small> - <?= $option['name']; ?>: <a href="<?= $option['href']; ?>"><?= $option['value']; ?></a></small>
								<? } ?>
								<? } ?></td>
							<td class="left"><?= $product['model']; ?></td>
							<td class="right"><?= $product['quantity']; ?></td>
							<td class="right"><?= $product['price']; ?></td>
							<td class="right"><?= $product['total']; ?></td>
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
					</tbody>
					<? foreach ($totals as $totals) { ?>
					<tbody id="totals">
						<tr>
							<td colspan="4" class="right"><?= $totals['title']; ?>:</td>
							<td class="right"><?= $totals['text']; ?></td>
						</tr>
					</tbody>
					<? } ?>
				</table>
				<? if ($downloads) { ?>
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
			</div>
			<div id="tab-history" class="vtabs-content">
				<div id="history"></div>
				<table class="form">
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td><select name="order_status_id">
								<? foreach ($order_statuses as $order_statuses) { ?>
								<? if ($order_statuses['order_status_id'] == $order_status_id) { ?>
								<option value="<?= $order_statuses['order_status_id']; ?>" selected="selected"><?= $order_statuses['name']; ?></option>
								<? } else { ?>
								<option value="<?= $order_statuses['order_status_id']; ?>"><?= $order_statuses['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_notify; ?></td>
						<td><input type="checkbox" name="notify" value="1" /></td>
					</tr>
					<tr>
						<td><?= $entry_comment; ?></td>
						<td><textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
							<div style="margin-top: 10px; text-align: right;"><a id="button-history" class="button"><?= $button_add_history; ?></a></div></td>
					</tr>
				</table>
			</div>
			<? if ($maxmind_id) { ?>
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
$('#invoice-generate').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/createinvoiceno"; ?>" + '&order_id=<?= $order_id; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#invoice').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('#tab-order').prepend('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json.invoice_no) {
				$('#invoice').fadeOut('slow', function() {
					$('#invoice').html(json['invoice_no']);
					
					$('#invoice').fadeIn('slow');
				});
			}
		}
	});
});

$('#credit-add').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/addcredit"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#credit').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
			
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#credit').html('<b>[</b> <a id="credit-remove"><?= $text_credit_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#credit-remove').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/removecredit"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#credit').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#credit').html('<b>[</b> <a id="credit-add"><?= $text_credit_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#reward-add').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/addreward"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#reward').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');

				$('#reward').html('<b>[</b> <a id="reward-remove"><?= $text_reward_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#reward-remove').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/removereward"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#reward').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#reward').html('<b>[</b> <a id="reward-add"><?= $text_reward_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#commission-add').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/addcommission"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#commission').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
								
				$('#commission').html('<b>[</b> <a id="commission-remove"><?= $text_commission_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#commission-remove').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/removecommission"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#commission').after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
								$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#commission').html('<b>[</b> <a id="commission-add"><?= $text_commission_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});

$('#history').load("<?= HTTP_ADMIN . "index.php?route=sale/order/history"; ?>" + '&order_id=<?= $order_id; ?>');

$('#button-history').live('click', function() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/order/history"; ?>" + '&order_id=<?= $order_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'order_status_id=' + encodeURIComponent($('select[name=\'order_status_id\']').val()) + '&notify=' + encodeURIComponent($('input[name=\'notify\']').attr('checked') ? 1 : 0) + '&append=' + encodeURIComponent($('input[name=\'append\']').attr('checked') ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name=\'comment\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-history').attr('disabled', true);
			$('#history').prepend('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-history').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#history').html(html);
			
			$('textarea[name=\'comment\']').val('');
			
			$('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>
<?= $footer; ?>