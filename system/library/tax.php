<?php
final class Tax extends Library
{
	private $store_address;
	private $show_price_with_tax;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->store_address = array(
			'country_id' => $this->config->get('config_country_id'),
			'zone_id'    => $this->config->get('config_zone_id'),
			'postcode'   => $this->config->get('config_postcode')
		);

		$this->show_price_with_tax = $this->config->get('config_show_price_with_tax');
	}

	public function apply(&$taxes, $value, $tax_class_id)
	{
		$tax_rates = $this->tax->getRates($value, $tax_class_id);

		foreach ($tax_rates as $tax_rate) {
			if (!isset($taxes[$tax_rate['tax_rate_id']])) {
				$taxes[$tax_rate['tax_rate_id']] = 0;
			}

			if ($tax_rate['type'] == 'P') {
				$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
			} elseif ($tax_rate['type'] == 'F') {
				$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
			}
		}
	}

	public function calculate($value, $tax_class_id)
	{
		if (!$this->show_price_with_tax || !$tax_class_id) {
			return $value;
		}

		return $value + $this->getTax($value, $tax_class_id);
	}

	public function getTax($value, $tax_class_id)
	{
		$amount = 0;

		$tax_rates = $this->getRates($value, $tax_class_id);

		foreach ($tax_rates as $tax_rate) {
			$amount += $tax_rate['amount'];
		}

		return $amount;
	}

	public function getRateInfo($tax_rate_id)
	{
		return $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
	}

	public function getRates($value, $tax_class_id)
	{
		$tax_rates = array();

		$customer_group_id = $this->customer->getCustomerGroupId();

		if ($this->cart->hasShippingAddress()) {
			$this->get_tax_rates($tax_rates, $tax_class_id, 'shipping', $this->cart->getShippingAddress(), $customer_group_id);
		}

		if ($this->cart->hasPaymentAddress()) {
			$this->get_tax_rates($tax_rates, $tax_class_id, 'payment', $this->cart->getPaymentAddress(), $customer_group_id);
		}

		if ($this->store_address) {
			$this->get_tax_rates($tax_rates, $tax_class_id, 'store', $this->store_address, $customer_group_id);
		}


		$tax_rate_data = array();

		foreach ($tax_rates as $tax_rate) {
			if (isset($tax_rate_data[$tax_rate['tax_rate_id']])) {
				$amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];
			} else {
				$amount = 0;
			}

			if ($tax_rate['type'] == 'F') {
				$amount += $tax_rate['rate'];
			} elseif ($tax_rate['type'] == 'P') {
				$amount += ($value / 100 * $tax_rate['rate']);
			}

			$tax_rate_data[$tax_rate['tax_rate_id']] = array(
				'tax_rate_id' => $tax_rate['tax_rate_id'],
				'name'        => $tax_rate['name'],
				'rate'        => $tax_rate['rate'],
				'type'        => $tax_rate['type'],
				'amount'      => $amount
			);
		}

		return $tax_rate_data;
	}

	private function get_tax_rates(&$tax_rates, $tax_class_id, $type, $address, $customer_group_id)
	{
		$query =
			"SELECT tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority, gz.geo_zone_id FROM " . DB_PREFIX . "tax_rule tr1" .
			" LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id)" .
			" INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id)" .
			" LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id)" .
			" WHERE tr1.tax_class_id = '" . (int)$tax_class_id . "' AND tr1.based = '$type' AND tr2cg.customer_group_id = '" . (int)$customer_group_id . "'" .
			" ORDER BY tr1.priority ASC";

		$result = $this->db->query($query);

		//TODO HACK TO APPLY ZONE CODES - SHOULD MOVE THIS TO A NEW TAX TOTAL LINE ITEM!
		$county_tax = array(
			94022,
			94024,
			94035,
			94040,
			94041,
			94043,
			94085,
			94086,
			94087,
			94089,
			94301,
			94303,
			94304,
			94305,
			94306,
			94550,
			95002,
			95008,
			95013,
			95014,
			95020,
			95023,
			95030,
			95032,
			95033,
			95035,
			95037,
			95046,
			95050,
			95051,
			95053,
			95054,
			95070,
			95076,
			95110,
			95111,
			95112,
			95113,
			95116,
			95117,
			95118,
			95119,
			95120,
			95121,
			95122,
			95123,
			95124,
			95125,
			95126,
			95127,
			95128,
			95129,
			95130,
			95131,
			95132,
			95133,
			95134,
			95135,
			95136,
			95138,
			95139,
			95140,
			95141,
			95148
		);

		foreach ($result->rows as $row) {
			if (!$this->address->inGeoZone($address, $row['geo_zone_id'])) {
				continue;
			}

			if (in_array($address['postcode'], $county_tax)) {
				$row['rate'] += 1.125;
			}

			$tax_rates[$row['tax_rate_id']] = $row;
		}
	}

	public function has($tax_class_id)
	{
		return isset($this->taxes[$tax_class_id]);
	}
}
