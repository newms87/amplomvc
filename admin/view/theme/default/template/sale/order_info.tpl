<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>
<div class="box">
<div class="heading">
	<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= _l("Orders"); ?></h1>

	<div class="buttons"><a onclick="window.open('<?= $invoice; ?>');" class="button"><?= _l("Invoice"); ?></a><a
			href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
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

<? if (!empty($maxmind_id)) { ?>
	<div id="tab-fraud" class="vtabs-content">
		<table class="form">
			<? if ($country_match) { ?>
				<tr>
					<td><?= _l("Country Match:<br /><span class=\"help\">Whether country of IP address matches billing address country (mismatch = higher risk).</span>"); ?></td>
					<td><?= $country_match; ?></td>
				</tr>
			<? } ?>
			<? if ($country_code) { ?>
				<tr>
					<td><?= _l("Country Code:<br /><span class=\"help\">Country Code of the IP address.</span>"); ?></td>
					<td><?= $country_code; ?></td>
				</tr>
			<? } ?>
			<? if ($high_risk_country) { ?>
				<tr>
					<td><?= _l("High Risk Country:<br /><span class=\"help\">Whether IP address or billing address country is in Ghana, Nigeria, or Vietnam.</span>"); ?></td>
					<td><?= $high_risk_country; ?></td>
				</tr>
			<? } ?>
			<? if ($distance) { ?>
				<tr>
					<td><?= _l("Distance:<br /><span class=\"help\">Distance from IP address to Billing Location in kilometers (large distance = higher risk).</span>"); ?></td>
					<td><?= $distance; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_region) { ?>
				<tr>
					<td><?= _l("IP Region:<br /><span class=\"help\">Estimated State/Region of the IP address.</span>"); ?></td>
					<td><?= $ip_region; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_city) { ?>
				<tr>
					<td><?= _l("IP City:<br /><span class=\"help\">Estimated City of the IP address.</span>"); ?></td>
					<td><?= $ip_city; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_latitude) { ?>
				<tr>
					<td><?= _l("IP Latitude:<br /><span class=\"help\">Estimated Latitude of the IP address.</span>"); ?></td>
					<td><?= $ip_latitude; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_longitude) { ?>
				<tr>
					<td><?= _l("IP Longitude:<br /><span class=\"help\">Estimated Longitude of the IP address.</span>"); ?></td>
					<td><?= $ip_longitude; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_isp) { ?>
				<tr>
					<td><?= _l("ISP:<br /><span class=\"help\">ISP of the IP address.</span>"); ?></td>
					<td><?= $ip_isp; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_org) { ?>
				<tr>
					<td><?= _l("IP Organization:<br /><span class=\"help\">Organization of the IP addres.</span>"); ?></td>
					<td><?= $ip_org; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_asnum) { ?>
				<tr>
					<td><?= _l("ASNUM:<br /><span class=\"help\">Estimated Autonomous System Number of the IP address.</span>"); ?></td>
					<td><?= $ip_asnum; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_user_type) { ?>
				<tr>
					<td><?= _l("IP User Type:<br /><span class=\"help\">Estimated user type of the IP address.</span>"); ?></td>
					<td><?= $ip_user_type; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_country_confidence) { ?>
				<tr>
					<td><?= _l("IP Country Confidence:<br /><span class=\"help\">Representing our confidence that the country location is correct.</span>"); ?></td>
					<td><?= $ip_country_confidence; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_region_confidence) { ?>
				<tr>
					<td><?= _l("IP Region Confidence:<br /><span class=\"help\">Representing our confidence that the region location is correct.</span>"); ?></td>
					<td><?= $ip_region_confidence; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_city_confidence) { ?>
				<tr>
					<td><?= _l("IP City Confidence:<br /><span class=\"help\">Representing our confidence that the city location is correct.</span>"); ?></td>
					<td><?= $ip_city_confidence; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_postal_confidence) { ?>
				<tr>
					<td><?= _l("IP Postal Confidence:<br /><span class=\"help\">Representing our confidence that the postal code location is correct.</span>"); ?></td>
					<td><?= $ip_postal_confidence; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_postal_code) { ?>
				<tr>
					<td><?= _l("IP Postal Code:<br /><span class=\"help\">Estimated Postal Code of the IP address.</span>"); ?></td>
					<td><?= $ip_postal_code; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_accuracy_radius) { ?>
				<tr>
					<td><?= _l("IP Accuracy Radius:<br /><span class=\"help\">The average distance between the actual location of the end user using the IP address and the location returned by the GeoIP City database, in miles.</span>"); ?></td>
					<td><?= $ip_accuracy_radius; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_net_speed_cell) { ?>
				<tr>
					<td><?= _l("IP Net Speed Cell:<br /><span class=\"help\">Estimated network type of the IP address.</span>"); ?></td>
					<td><?= $ip_net_speed_cell; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_metro_code) { ?>
				<tr>
					<td><?= _l("IP Metro Code:<br /><span class=\"help\">Estimated Metro Code of the IP address.</span>"); ?></td>
					<td><?= $ip_metro_code; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_area_code) { ?>
				<tr>
					<td><?= _l("IP Area Code:<br /><span class=\"help\">Estimated Area Code of the IP address.</span>"); ?></td>
					<td><?= $ip_area_code; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_time_zone) { ?>
				<tr>
					<td><?= _l("IP Time Zone:<br /><span class=\"help\">Estimated Time Zone of the IP address.</span>"); ?></td>
					<td><?= $ip_time_zone; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_region_name) { ?>
				<tr>
					<td><?= _l("IP Region Name:<br /><span class=\"help\">Estimated Region name of the IP address.</span>"); ?></td>
					<td><?= $ip_region_name; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_domain) { ?>
				<tr>
					<td><?= _l("IP Domain:<br /><span class=\"help\">Estimated domain of the IP address.</span>"); ?></td>
					<td><?= $ip_domain; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_country_name) { ?>
				<tr>
					<td><?= _l("IP Country Name:<br /><span class=\"help\">Estimated Country name of the IP address.</span>"); ?></td>
					<td><?= $ip_country_name; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_continent_code) { ?>
				<tr>
					<td><?= _l("IP Continent Code:<br /><span class=\"help\">Estimated Continent code of the IP address.</span>"); ?></td>
					<td><?= $ip_continent_code; ?></td>
				</tr>
			<? } ?>
			<? if ($ip_corporate_proxy) { ?>
				<tr>
					<td><?= _l("IP Corporate Proxy:<br /><span class=\"help\">Whether the IP is an Corporate Proxy in the database or not.</span>"); ?></td>
					<td><?= $ip_corporate_proxy; ?></td>
				</tr>
			<? } ?>
			<? if ($anonymous_proxy) { ?>
				<tr>
					<td><?= _l("Anonymous Proxy:<br /><span class=\"help\">Whether IP address is Anonymous Proxy (anonymous proxy = very high risk).</span>"); ?></td>
					<td><?= $anonymous_proxy; ?></td>
				</tr>
			<? } ?>
			<? if ($proxy_score) { ?>
				<tr>
					<td><?= _l("Proxy Score:<br /><span class=\"help\">Likelihood of IP Address being an Open Proxy.</span>"); ?></td>
					<td><?= $proxy_score; ?></td>
				</tr>
			<? } ?>
			<? if ($is_trans_proxy) { ?>
				<tr>
					<td><?= _l("Is Transparent Proxy:<br /><span class=\"help\">Whether IP address is in our database of known transparent proxy servers, returned if forwardedIP is passed as an input.</span>"); ?></td>
					<td><?= $is_trans_proxy; ?></td>
				</tr>
			<? } ?>
			<? if ($free_mail) { ?>
				<tr>
					<td><?= _l("Free Mail:<br /><span class=\"help\">Whether e-mail is from free e-mail provider (free e-mail = higher risk).</span>"); ?></td>
					<td><?= $free_mail; ?></td>
				</tr>
			<? } ?>
			<? if ($carder_email) { ?>
				<tr>
					<td><?= _l("Carder Email:<br /><span class=\"help\">Whether e-mail is in database of high risk e-mails.</span>"); ?></td>
					<td><?= $carder_email; ?></td>
				</tr>
			<? } ?>
			<? if ($high_risk_username) { ?>
				<tr>
					<td><?= _l("High Risk Username:<br /><span class=\"help\">Whether usernameMD5 input is in database of high risk usernames. Only returned if usernameMD5 is included in inputs.</span>"); ?></td>
					<td><?= $high_risk_username; ?></td>
				</tr>
			<? } ?>
			<? if ($high_risk_password) { ?>
				<tr>
					<td><?= _l("High Risk Password:<br /><span class=\"help\">Whether passwordMD5 input is in database of high risk passwords. Only returned if passwordMD5 is included in inputs.</span>"); ?></td>
					<td><?= $high_risk_password; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_match) { ?>
				<tr>
					<td><?= _l("Bin Match:<br /><span class=\"help\">Whether country of issuing bank based on BIN number matches billing address country.</span>"); ?></td>
					<td><?= $bin_match; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_country) { ?>
				<tr>
					<td><?= _l("Bin Country:<br /><span class=\"help\">Country Code of the bank which issued the credit card based on BIN number.</span>"); ?></td>
					<td><?= $bin_country; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_name_match) { ?>
				<tr>
					<td><?= _l("Bin Name Match:<br /><span class=\"help\">Whether name of issuing bank matches inputted  BIN name. A return value of Yes provides a positive indication that cardholder is in possession of credit card.</span>"); ?></td>
					<td><?= $bin_name_match; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_name) { ?>
				<tr>
					<td><?= _l("Binary Name"); ?></td>
					<td><?= $bin_name; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_phone_match) { ?>
				<tr>
					<td><?= _l("Bin Phone Match:<br /><span class=\"help\">Whether customer service phone number matches inputed BIN Phone. A return value of Yes provides a positive indication that cardholder is in possession of credit card.</span>"); ?></td>
					<td><?= $bin_phone_match; ?></td>
				</tr>
			<? } ?>
			<? if ($bin_phone) { ?>
				<tr>
					<td><?= _l("Binary Phone"); ?></td>
					<td><?= $bin_phone; ?></td>
				</tr>
			<? } ?>
			<? if ($customer_phone_in_billing_location) { ?>
				<tr>
					<td><?= _l("Customer Phone Number in Billing Location:<br /><span class=\"help\">Whether the customer phone number is in the billing zip code. A return value of Yes provides a positive indication that the phone number listed belongs to the cardholder. A return value of No indicates that the phone number may be in a different area, or may not be listed in our database. NotFound is returned when the phone number prefix cannot be found in our database at all. Currently we only support US Phone numbers.</span>"); ?></td>
					<td><?= $customer_phone_in_billing_location; ?></td>
				</tr>
			<? } ?>
			<? if ($ship_forward) { ?>
				<tr>
					<td><?= _l("Shipping Forward:<br /><span class=\"help\">Whether shipping address is in database of known mail drops.</span>"); ?></td>
					<td><?= $ship_forward; ?></td>
				</tr>
			<? } ?>
			<? if ($city_postal_match) { ?>
				<tr>
					<td><?= _l("City Postal Match:<br /><span class=\"help\">Whether billing city and state match zipcode. Currently available for US addresses only, returns empty string outside the US.</span>"); ?></td>
					<td><?= $city_postal_match; ?></td>
				</tr>
			<? } ?>
			<? if ($ship_city_postal_match) { ?>
				<tr>
					<td><?= _l("Shipping City Postal Match:<br /><span class=\"help\">Whether shipping city and state match zipcode. Currently available for US addresses only, returns empty string outside the US.</span>"); ?></td>
					<td><?= $ship_city_postal_match; ?></td>
				</tr>
			<? } ?>
			<? if ($score) { ?>
				<tr>
					<td><?= _l("Score:<br /><span class=\"help\">Overall fraud score based on outputs listed above. This is the original fraud score, and is based on a simple formula. It has been replaced with risk score (see below), but is kept for backwards compatibility.</span>"); ?></td>
					<td><?= $score; ?></td>
				</tr>
			<? } ?>
			<? if ($explanation) { ?>
				<tr>
					<td><?= _l("Explanation:<br /><span class=\"help\">A brief explanation of the score, detailing what factors contributed to it, according to our formula. Please note this corresponds to the score, not the riskScore.</span>"); ?></td>
					<td><?= $explanation; ?></td>
				</tr>
			<? } ?>
			<? if ($risk_score) { ?>
				<tr>
					<td><?= _l("Risk Score:<br /><span class=\"help\">New fraud score representing the estimated probability that the order is fraud, based off of analysis of past minFraud transactions. Requires an upgrade for clients who signed up before February 2007.</span>"); ?></td>
					<td><?= $risk_score; ?></td>
				</tr>
			<? } ?>
			<? if ($queries_remaining) { ?>
				<tr>
					<td><?= _l("Queries Remaining:<br /><span class=\"help\">Number of queries remaining in your account, can be used to alert you when you may need to add more queries to your account.</span>"); ?></td>
					<td><?= $queries_remaining; ?></td>
				</tr>
			<? } ?>
			<? if ($maxmind_id) { ?>
				<tr>
					<td><?= _l("Maxmind ID:<br /><span class=\"help\">Unique identifier, used to reference transactions when reporting fraudulent activity back to MaxMind. This reporting will help MaxMind improve its service to you and will enable a planned feature to customize the fraud scoring formula based on your chargeback history.</span>"); ?></td>
					<td><?= $maxmind_id; ?></td>
				</tr>
			<? } ?>
			<? if ($error) { ?>
				<tr>
					<td><?= _l("Error:<br /><span class=\"help\">Returns an error string with a warning message or a reason why the request failed.</span>"); ?></td>
					<td><?= $error; ?></td>
				</tr>
			<? } ?>
		</table>
	</div>
<? } ?>
</div>
</div>
</div>


<script type="text/javascript">
	$('.vtabs a').tabs();
</script>

<?= $footer; ?>
