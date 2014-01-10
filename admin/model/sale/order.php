<?php
class Admin_Model_Sale_Order extends Model
{
	public function addOrder($data)
	{
		$store_info = $this->Model_Setting_Store->getStore($data['store_id']);

		if ($store_info) {
			$store_name = $store_info['name'];
			$store_url  = $store_info['url'];
		} else {
			$store_name = $this->config->get('config_name');
			$store_url  = HTTP_CATALOG;
		}

		$setting_info = $this->config->loadGroup('setting', $data['store_id']);

		$country_info = $this->Model_Localisation_Country->getCountry($data['shipping_country_id']);

		if ($country_info) {
			$shipping_country        = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country        = '';
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$zone_info = $this->Model_Localisation_Zone->getZone($data['shipping_zone_id']);

		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';
		}

		$country_info = $this->Model_Localisation_Country->getCountry($data['payment_country_id']);

		if ($country_info) {
			$payment_country        = $country_info['name'];
			$payment_address_format = $country_info['address_format'];
		} else {
			$payment_country        = '';
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$zone_info = $this->Model_Localisation_Zone->getZone($data['payment_zone_id']);

		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';
		}

		$currency_info = $this->Model_Localisation_Currency->getCurrencyByCode($this->config->get('config_currency'));

		if ($currency_info) {
			$currency_id    = $currency_info['currency_id'];
			$currency_code  = $currency_info['code'];
			$currency_value = $currency_info['value'];
		} else {
			$currency_id    = 0;
			$currency_code  = $this->config->get('config_currency');
			$currency_value = 1.00000;
		}

		$this->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->escape($invoice_prefix) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->escape($store_name) . "',store_url = '" . $this->escape($store_url) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', shipping_firstname = '" . $this->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->escape($shipping_address_format) . "', shipping_method = '" . $this->escape($data['shipping_method']) . "', shipping_code = '" . $this->escape($data['shipping_code']) . "', payment_firstname = '" . $this->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->escape($data['payment_lastname']) . "', payment_company = '" . $this->escape($data['payment_company']) . "', payment_address_1 = '" . $this->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->escape($data['payment_address_2']) . "', payment_city = '" . $this->escape($data['payment_city']) . "', payment_postcode = '" . $this->escape($data['payment_postcode']) . "', payment_country = '" . $this->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->escape($payment_address_format) . "', payment_method = '" . $this->escape($data['payment_method']) . "', payment_code = '" . $this->escape($data['payment_code']) . "', comment = '" . $this->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', currency_id = '" . (int)$currency_id . "', currency_code = '" . $this->escape($currency_code) . "', currency_value = '" . (float)$currency_value . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		if (isset($data['order_product'])) {
			foreach ($data['order_product'] as $order_product) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->escape($order_product['name']) . "', model = '" . $this->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				if (isset($order_product['order_option'])) {
					foreach ($order_product['order_option'] as $order_option) {
						$this->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->escape($order_option['name']) . "', `value` = '" . $this->escape($order_option['value']) . "', `type` = '" . $this->escape($order_option['type']) . "'");
					}
				}

				if (isset($order_product['order_download'])) {
					foreach ($order_product['order_download'] as $order_download) {
						$this->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->escape($order_download['name']) . "', filename = '" . $this->escape($order_download['filename']) . "', mask = '" . $this->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
					}
				}
			}
		}

		if (isset($data['order_voucher'])) {
			foreach ($data['order_voucher'] as $order_voucher) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->escape($order_voucher['description']) . "', code = '" . $this->escape($order_voucher['code']) . "', from_name = '" . $this->escape($order_voucher['from_name']) . "', from_email = '" . $this->escape($order_voucher['from_email']) . "', to_name = '" . $this->escape($order_voucher['to_name']) . "', to_email = '" . $this->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");

				$this->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
			}
		}

		// Get the total
		$total = 0;

		if (isset($data['order_total'])) {
			foreach ($data['order_total'] as $order_total) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->escape($order_total['code']) . "', title = '" . $this->escape($order_total['title']) . "', text = '" . $this->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}

			$total += $order_total['value'];
		}

		$this->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function editOrder($order_id, $data)
	{
		$country_info = $this->Model_Localisation_Country->getCountry($data['shipping_country_id']);

		if ($country_info) {
			$shipping_country        = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country        = '';
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$zone_info = $this->Model_Localisation_Zone->getZone($data['shipping_zone_id']);

		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';
		}

		$country_info = $this->Model_Localisation_Country->getCountry($data['payment_country_id']);

		if ($country_info) {
			$payment_country        = $country_info['name'];
			$payment_address_format = $country_info['address_format'];
		} else {
			$payment_country        = '';
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$zone_info = $this->Model_Localisation_Zone->getZone($data['payment_zone_id']);

		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';
		}

		$this->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', shipping_firstname = '" . $this->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->escape($shipping_address_format) . "', shipping_method = '" . $this->escape($data['shipping_method']) . "', shipping_code = '" . $this->escape($data['shipping_code']) . "', payment_firstname = '" . $this->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->escape($data['payment_lastname']) . "', payment_company = '" . $this->escape($data['payment_company']) . "', payment_address_1 = '" . $this->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->escape($data['payment_address_2']) . "', payment_city = '" . $this->escape($data['payment_city']) . "', payment_postcode = '" . $this->escape($data['payment_postcode']) . "', payment_country = '" . $this->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->escape($payment_address_format) . "', payment_method = '" . $this->escape($data['payment_method']) . "', payment_code = '" . $this->escape($data['payment_code']) . "', comment = '" . $this->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['order_product'])) {
			foreach ($data['order_product'] as $order_product) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_product SET order_product_id = '" . (int)$order_product['order_product_id'] . "', order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->escape($order_product['name']) . "', model = '" . $this->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				if (isset($order_product['order_option'])) {
					foreach ($order_product['order_option'] as $order_option) {
						$this->query("INSERT INTO " . DB_PREFIX . "order_option SET order_option_id = '" . (int)$order_option['order_option_id'] . "', order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->escape($order_option['name']) . "', `value` = '" . $this->escape($order_option['value']) . "', `type` = '" . $this->escape($order_option['type']) . "'");
					}
				}

				if (isset($order_product['order_download'])) {
					foreach ($order_product['order_download'] as $order_download) {
						$this->query("INSERT INTO " . DB_PREFIX . "order_download SET order_download_id = '" . (int)$order_download['order_download_id'] . "', order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->escape($order_download['name']) . "', filename = '" . $this->escape($order_download['filename']) . "', mask = '" . $this->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
					}
				}
			}
		}

		$this->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['order_voucher'])) {
			foreach ($data['order_voucher'] as $order_voucher) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_voucher_id = '" . (int)$order_voucher['order_voucher_id'] . "', order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->escape($order_voucher['description']) . "', code = '" . $this->escape($order_voucher['code']) . "', from_name = '" . $this->escape($order_voucher['from_name']) . "', from_email = '" . $this->escape($order_voucher['from_email']) . "', to_name = '" . $this->escape($order_voucher['to_name']) . "', to_email = '" . $this->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");

				$this->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
			}
		}

		// Get the total
		$total = 0;

		$this->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['order_total'])) {
			foreach ($data['order_total'] as $order_total) {
				$this->query("INSERT INTO " . DB_PREFIX . "order_total SET order_total_id = '" . (int)$order_total['order_total_id'] . "', order_id = '" . (int)$order_id . "', code = '" . $this->escape($order_total['code']) . "', title = '" . $this->escape($order_total['title']) . "', text = '" . $this->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}

			$total += $order_total['value'];
		}

		$this->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function deleteOrder($order_id)
	{
		$order_query = $this->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$product_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($product_query->rows as $product) {
				$this->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

				$option_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($option_query->rows as $option) {
					$this->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
				}
			}
		}

		$this->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
	}



	public function getTotalSales()
	{
		$query = $this->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSalesByYear($year)
	{
		$query = $this->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int)$year . "'");

		return $query->row['total'];
	}

	public function createInvoiceNo($order_id)
	{
		$order_info = $this->getOrder($_GET['order_id']);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}

	public function getOrderHistories($order_id, $start = 0, $limit = 10)
	{
		$query = $this->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalOrderHistories($order_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderHistoriesByOrderStatusId($order_status_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}

	public function getEmailsByProductsOrdered($products, $start, $end)
	{
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . $product_id . "'";
		}

		$query = $this->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

		return $query->rows;
	}

	public function getTotalEmailsByProductsOrdered($products)
	{
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . $product_id . "'";
		}

		$query = $this->query("SELECT COUNT(DISTINCT email) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

		return $query->row['total'];
	}
}
