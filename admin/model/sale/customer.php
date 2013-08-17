<?php
class Admin_Model_Sale_Customer extends Model
{
	public function addCustomer($data)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "customer SET firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', newsletter = '" . (int)$data['newsletter'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', password = '" . $this->customer->encrypt($data['password']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				$this->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->escape($address['firstname']) . "', lastname = '" . $this->escape($address['lastname']) . "', company = '" . $this->escape($address['company']) . "', address_1 = '" . $this->escape($address['address_1']) . "', address_2 = '" . $this->escape($address['address_2']) . "', city = '" . $this->escape($address['city']) . "', postcode = '" . $this->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();

					$this->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . $address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		}
	}

	public function editCustomer($customer_id, $data)
	{
		$this->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', newsletter = '" . (int)$data['newsletter'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', status = '" . (int)$data['status'] . "' WHERE customer_id = '" . (int)$customer_id . "'");

		if ($data['password']) {
			$this->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->customer->encrypt($data['password']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		$this->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				if ($address['address_id']) {
					$this->query("INSERT INTO " . DB_PREFIX . "address SET address_id = '" . $this->escape($address['address_id']) . "', customer_id = '" . (int)$customer_id . "', firstname = '" . $this->escape($address['firstname']) . "', lastname = '" . $this->escape($address['lastname']) . "', company = '" . $this->escape($address['company']) . "', address_1 = '" . $this->escape($address['address_1']) . "', address_2 = '" . $this->escape($address['address_2']) . "', city = '" . $this->escape($address['city']) . "', postcode = '" . $this->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

					if (isset($address['default'])) {
						$this->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address['address_id'] . "' WHERE customer_id = '" . (int)$customer_id . "'");
					}
				} else {
					$this->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->escape($address['firstname']) . "', lastname = '" . $this->escape($address['lastname']) . "', company = '" . $this->escape($address['company']) . "', address_1 = '" . $this->escape($address['address_1']) . "', address_2 = '" . $this->escape($address['address_2']) . "', city = '" . $this->escape($address['city']) . "', postcode = '" . $this->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "'");

					if (isset($address['default'])) {
						$address_id = $this->db->getLastId();

						$this->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
					}
				}
			}
		}
	}

	public function editToken($customer_id, $token)
	{
		$this->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "coupon_customer WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getCustomer($customer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->escape($email) . "'");

		return $query->row;
	}

	public function getCustomers($data = array())
	{
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cg.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id)";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->escape(strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(c.email) LIKE '" . $this->escape(strtolower($data['filter_email'])) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->query($sql);

		return $query->rows;
	}

	public function approve($customer_id)
	{
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");

			$this->language->load('mail/customer');

			$store_info = $this->Model_Setting_Store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
				$store_url  = $this->url->store($store_info['store_id'], 'account/login');
			} else {
				$store_name = $this->config->get('config_name');
				$store_url  = $this->url->store($this->config->get('config_default_store'), 'account/login');
			}

			$patterns     = array(
				'/%first_name%/',
				'/%last_name%/',
				'/%store_name%/',
				'/%store_url%/'
			);
			$replacements = array(
				$customer_info['firstname'],
				$customer_info['lastname'],
				$store_name,
				$store_url
			);
			$subject      = preg_replace($patterns, $replacements, $this->config->get('mail_registration_subject'));
			$message      = preg_replace($patterns, $replacements, $this->config->get('mail_registration_message'));

			$this->mail->init();

			$this->mail->setTo($customer_info['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($store_name);
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
		}
	}

	public function getAddress($address_id)
	{
		$address_query = $this->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country        = $country_query->row['name'];
				$iso_code_2     = $country_query->row['iso_code_2'];
				$iso_code_3     = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country        = '';
				$iso_code_2     = '';
				$iso_code_3     = '';
				$address_format = '';
			}

			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone      = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone      = '';
				$zone_code = '';
			}

			return array(
				'address_id'     => $address_query->row['address_id'],
				'customer_id'    => $address_query->row['customer_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}
	}

	public function getAddresses($customer_id)
	{
		$address_data = array();

		$query = $this->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		foreach ($query->rows as $result) {
			$address_info = $this->getAddress($result['address_id']);

			if ($address_info) {
				$address_data[] = $address_info;
			}
		}

		return $address_data;
	}

	public function getTotalCustomers($data = array())
	{
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(firstname, ' ', lastname)) LIKE '" . $this->escape(strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(email) LIKE '" . $this->escape(strtolower($data['filter_email'])) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->query($sql);

		return $query->row['total'];
	}

	public function getTotalCustomersAwaitingApproval()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCustomerId($customer_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByCustomerGroupId($customer_group_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0)
	{
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$this->language->load('mail/customer');

			if ($customer_info['store_id']) {
				$store_info = $this->Model_Setting_Store->getStore($customer_info['store_id']);

				if ($store_info) {
					$store_name = $store_info['store_name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message = sprintf($this->_('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->_('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

			$this->mail->init();

			$this->mail->setTo($customer_info['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($store_name);
			$this->mail->setSubject(html_entity_decode(sprintf($this->_('text_transaction_subject'), $this->config->get('config_name')), ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
		}
	}

	public function deleteTransaction($order_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id)
	{
		$query = $this->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0)
	{
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->escape($description) . "', date_added = NOW()");

			$this->language->load('mail/customer');

			if ($order_id) {
				$order_info = $this->order->get($order_id);

				if ($order_info) {
					$store_name = $order_info['store_name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message = sprintf($this->_('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->_('text_reward_total'), $this->getRewardTotal($customer_id));

			$this->mail->init();

			$this->mail->setTo($customer_info['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($store_name);
			$this->mail->setSubject(html_entity_decode(sprintf($this->_('text_reward_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
		}
	}

	public function deleteReward($order_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id)
	{
		$query = $this->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getIpsByCustomerId($customer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function getTotalCustomersByIp($ip)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->escape($ip) . "'");

		return $query->row['total'];
	}

	public function addBlacklist($ip)
	{
		$this->query("INSERT INTO `" . DB_PREFIX . "customer_ip_blacklist` SET `ip` = '" . $this->escape($ip) . "'");
	}

	public function deleteBlacklist($ip)
	{
		$this->query("DELETE FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE `ip` = '" . $this->escape($ip) . "'");
	}

	public function getTotalBlacklistsByIp($ip)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE `ip` = '" . $this->escape($ip) . "'");

		return $query->row['total'];
	}
}
