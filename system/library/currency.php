<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class Currency extends Library
{
	private $code;
	private $currencies = array();

	public function __construct()
	{
		parent::__construct();

		$this->currencies = cache('currencies');

		if (!$this->currencies) {
			$this->currencies = $this->queryRows("SELECT * FROM {$this->t['currency']} WHERE status = 1", 'code');

			cache('currencies', $this->currencies);
		}

		$this->set(_get('currency', _session('currency', _cookie('currency', option('config_currency')))));
	}

	public function has($code)
	{
		return isset($this->currencies[$code]);
	}

	public function set($code)
	{
		if (!isset($this->currencies[$code])) {
			if (empty($this->currencies)) {
				$this->error['currency'] = _l("There are no currencies available");
				trigger_error($this->error['currency']);
				return false;
			} else {
				$code = key($this->currencies);
			}
		}

		$this->code = $code;

		$currency = $this->currencies[$code];

		$currency += array(
			'decimal_point' => $this->language->info('decimal_point'),
			'thousand_sep'  => $this->language->info('thousand_point'),
		);

		js_var('currency', $currency);

		return true;
	}

	public function format($number, $code = '', $value = '', $format = true, $decimals = null)
	{
		if (!$code || !$this->has($code)) {
			$code = $this->code;
		}

		if ($format) {
			$symbol_left    = $this->currencies[$code]['symbol_left'];
			$symbol_right   = $this->currencies[$code]['symbol_right'];
			$decimal_point  = $this->language->info('decimal_point');
			$thousand_point = $this->language->info('thousand_point');
		} else {
			$symbol_left    = '';
			$symbol_right   = '';
			$decimal_point  = '.';
			$thousand_point = '';
		}

		if ($decimals === null) {
			$decimals = (int)$this->currencies[$code]['decimal_place'];
		}

		if (!$value) {
			$value = $this->currencies[$code]['value'];
		}

		if ($value) {
			$number = (float)$number * $value;
		}

		$string = number_format(abs($number), $decimals, $decimal_point, $thousand_point);

		return ($number < 0 ? '-' : '') . $symbol_left . $string . $symbol_right;
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
}
