<?php

class App_Model_Localisation_Currency extends Model
{
	public function addCurrency($data)
	{
		$this->query("INSERT INTO {$this->t['currency']} SET title = '" . $this->escape($data['title']) . "', code = '" . $this->escape($data['code']) . "', symbol_left = '" . $this->escape($data['symbol_left']) . "', symbol_right = '" . $this->escape($data['symbol_right']) . "', decimal_place = '" . $this->escape($data['decimal_place']) . "', value = '" . $this->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW()");

		if (option('config_currency_auto')) {
			$this->updateCurrencies(true);
		}

		clear_cache('currency');
	}

	public function editCurrency($currency_id, $data)
	{
		$this->query("UPDATE {$this->t['currency']} SET title = '" . $this->escape($data['title']) . "', code = '" . $this->escape($data['code']) . "', symbol_left = '" . $this->escape($data['symbol_left']) . "', symbol_right = '" . $this->escape($data['symbol_right']) . "', decimal_place = '" . $this->escape($data['decimal_place']) . "', value = '" . $this->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE currency_id = '" . (int)$currency_id . "'");

		clear_cache('currency');
	}

	public function deleteCurrency($currency_id)
	{
		$this->query("DELETE FROM {$this->t['currency']} WHERE currency_id = '" . (int)$currency_id . "'");

		clear_cache('currency');
	}

	public function getCurrency($currency_id)
	{
		$query = $this->query("SELECT * FROM {$this->t['currency']} WHERE currency_id = '" . (int)$currency_id . "'");

		return $query->row;
	}

	public function getCurrencyByCode($currency)
	{
		return $this->queryRow("SELECT * FROM {$this->t['currency']} WHERE code = '" . $this->escape($currency) . "'");
	}

	public function getActiveCurrencies()
	{
		$currencies = cache('currency.active');

		if (!$currencies) {
			$currencies = $this->queryRows("SELECT * FROM {$this->t['currency']} WHERE status = 1 ORDER BY title ASC");

			cache('currency.active', $currencies);
		}

		return $currencies;
	}

	public function getCurrencies($data = array())
	{
		if ($data) {
			$sql = "SELECT * FROM {$this->t['currency']}";

			$sort_data = array(
				'title',
				'code',
				'value',
				'date_modified'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
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
		} else {
			$currency_data = cache('currency');

			if (!$currency_data) {
				$currency_data = array();

				$query = $this->query("SELECT * FROM {$this->t['currency']} ORDER BY title ASC");

				foreach ($query->rows as $result) {
					$currency_data[$result['code']] = array(
						'currency_id'   => $result['currency_id'],
						'title'         => $result['title'],
						'code'          => $result['code'],
						'symbol_left'   => $result['symbol_left'],
						'symbol_right'  => $result['symbol_right'],
						'decimal_place' => $result['decimal_place'],
						'value'         => $result['value'],
						'status'        => $result['status'],
						'date_modified' => $result['date_modified']
					);
				}

				cache('currency', $currency_data);
			}

			return $currency_data;
		}
	}

	public function updateCurrencies($force = false)
	{
		if (extension_loaded('curl')) {
			$data = array();

			if ($force) {
				$query = $this->query("SELECT * FROM {$this->t['currency']} WHERE code != '" . $this->escape(option('config_currency')) . "'");
			} else {
				$query = $this->query("SELECT * FROM {$this->t['currency']} WHERE code != '" . $this->escape(option('config_currency')) . "' AND date_modified < '" . $this->escape(date('Y-m-d H:i:s', strtotime('-1 day'))) . "'");
			}

			foreach ($query->rows as $result) {
				$data[] = option('config_currency') . $result['code'] . '=X';
			}

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$content = curl_exec($curl);

			curl_close($curl);

			$lines = explode("\n", trim($content));

			foreach ($lines as $line) {
				$currency = substr($line, 4, 3);
				$value    = substr($line, 11, 6);

				if ((float)$value) {
					$this->query("UPDATE {$this->t['currency']} SET value = '" . (float)$value . "', date_modified = '" . $this->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->escape($currency) . "'");
				}
			}

			$this->query("UPDATE {$this->t['currency']} SET value = '1.00000', date_modified = '" . $this->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->escape(option('config_currency')) . "'");

			clear_cache('currency');
		}
	}

	public function getTotalCurrencies()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM {$this->t['currency']}");

		return $query->row['total'];
	}
}
