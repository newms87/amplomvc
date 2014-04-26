<?php
class Currency extends Library
{
	private $code;
	private $currencies = array();

	public function __construct()
	{
		parent::__construct();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "currency");

		foreach ($query->rows as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value']
			);
		}

		if (isset($_GET['currency']) && (array_key_exists($_GET['currency'], $this->currencies))) {
			$this->set($_GET['currency']);
		} elseif (($this->session->has('currency')) && (array_key_exists($this->session->get('currency'), $this->currencies))) {
			$this->set($this->session->get('currency'));
		} elseif ((isset($_COOKIE['currency'])) && (array_key_exists($_COOKIE['currency'], $this->currencies))) {
			$this->set($_COOKIE['currency']);
		} else {
			$this->set($this->config->get('config_currency'));
		}
	}

	public function set($currency)
	{
		$this->code = $currency;

		if (!$this->session->has('currency') || ($this->session->get('currency') != $currency)) {
			$this->session->set('currency', $currency);
		}

		if (!isset($_COOKIE['currency']) || ($_COOKIE['currency'] != $currency)) {
			$this->session->setCookie('currency', $currency);
		}

		$vars = array(
			'symbol_left'   => $this->currencies[$currency]['symbol_left'],
			'symbol_right'  => $this->currencies[$currency]['symbol_right'],
			'decimals'      => (int)$this->currencies[$currency]['decimal_place'],
			'decimal_point' => $this->language->info('decimal_point'),
			'thousand_sep'  => $this->language->info('thousand_point'),
		);

		$this->document->localizeVar('currency', $vars);
	}

	public function format($number, $currency = '', $value = '', $format = true, $decimals = null)
	{
		if (!$currency || !$this->has($currency)) {
			$currency = $this->code;
		}

		if ($format) {
			$symbol_left    = $this->currencies[$currency]['symbol_left'];
			$symbol_right   = $this->currencies[$currency]['symbol_right'];
			$decimal_point  = $this->language->info('decimal_point');
			$thousand_point = $this->language->info('thousand_point');
		} else {
			$symbol_left    = '';
			$symbol_right   = '';
			$decimal_point  = '.';
			$thousand_point = '';
		}

		if ($decimals === null) {
			$decimals = (int)$this->currencies[$currency]['decimal_place'];
		}

		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		if ($value) {
			$number = (float)$number * $value;
		}

		$string = number_format($number, $decimals, $decimal_point, $thousand_point);

		return $symbol_left . $string . $symbol_right;
	}

	public function convert($value, $from, $to)
	{
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 0;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 0;
		}

		return $value * ($to / $from);
	}

	public function getId($currency = '')
	{
		if (!$currency) {
			return $this->currencies[$this->code]['currency_id'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency = '')
	{
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_left'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency = '')
	{
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_right'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency = '')
	{
		if (!$currency) {
			return $this->currencies[$this->code]['decimal_place'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getValue($currency = '')
	{
		if (!$currency) {
			return $this->currencies[$this->code]['value'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency)
	{
		return isset($this->currencies[$currency]);
	}
}
