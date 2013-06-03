<?php
class Validation 
{
	private $error = array();
	
	private $registry;
	private $encoding;
	
	function __construct(&$registry)
	{
		$this->encoding = 'UTF-8';
		$this->registry = &$registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function set_encoding($encoding)
	{
		$this->encoding = $encoding;
	}
	
	public function get_errors()
	{
		return $this->error;
	}
	
	public function fetch_error()
	{
		return array_pop($this->error);
	}
	
	public function not_empty($value, $allow_zero = true, $allow_false = true)
	{
		if ($value === '' || is_null($value) || (is_array($value) && empty($value))) {
			$this->error['empty'] = true;
			return false;
		}
		elseif (!$allow_zero && (is_integer($value) || is_string($value) && !$value)) {
			$this->error['empty'] = true;
			return false;
		}
		elseif (!$allow_false && is_bool($value) && !$value) {
			$this->error['empty'] = true;
			return false;
		}
		
		return true;
	}
	
	public function not_empty_zero($value)
	{
		if ($value === '' || is_null($value) || (is_array($value) && empty($value)) || $value === '0' || $value === 0) {
			$this->error['empty'] = true;
			return false;
		}
		
		return true;
	}
	
	public function phone($phone)
	{
		$allowed = "()-+. \d";
		if (preg_match("/[^$allowed]/", $phone) > 0) {
			$this->error['phone']['invalid_characters'] = true;
			return false;
		}
		
		$p = preg_replace("/[^\d]/", '', $phone);
		
		if (strlen($p) < 7) {
			$this->error['phone']['minimum'] = true;
			return false;
		}
		
		if (strlen($p) > 32) {
			$this->error['phone']['maximum'] = true;
			return false;
		}
		
		return true;
	}
	
	public function postcode($postcode)
	{
		$p = preg_replace("/[^\dA-Z]/i", '', $postcode);
		
		if (strlen($p) < 3) {
			$this->error['postcode']['minimum'] = true;
			return false;
		}
		
		if (strlen($p) > 10) {
			$this->error['postcode']['maximum'] = true;
			return false;
		}
		
		return true;
	}
	
	public function email($email)
	{
		if(preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $email) == 0){
			$this->error['email']['format'] = true;
			return false;
		}
		
		return true;
	}
	
	public function url($url, $protocol_required = false)
	{
		
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->error['url']['format'] = true;
			return false;
		}
		
		return true;
	}
	
	public function text($text, $min = 1, $max = -1)
	{
		if ($min >= 0 && strlen($text) < $min) {
			$this->error['text']['minimum'][] = $text . " : " . strlen($text) . ' / ' . $min;
			return false;
		}
		
		if ($max >= 0 && strlen($text) > $max) {
			$this->error['text']['maximum'][] = $text . " : " . strlen($text) . ' / ' . $max;
			return false;
		}
		
		return true;
	}

	public function password($password)
	{
		if (strlen($password) < 4 || strlen($password) > 20) {
			$this->error['password'] = true;
			return false;
		}
		
		return true;
	}
	
	
	public function datetime($date, $format = null)
	{
		if (!$format) {
			$format = $this->language->getInfo('datetime_format');
		}
		
		$date_info = date_parse_from_format($format, $date);
		
		if ($date_info['errors'] || !checkdate($date_info['month'], $date_info['day'], $date_info['year'])) {
			$this->error['datetime'] = "invalid datetime format";
			return false;
		}
		
		return true;
	}
}