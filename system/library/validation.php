<?php
class Validation extends Library
{
	private $encoding;

	function __construct($registry)
	{
		parent::__construct($registry);

		$this->encoding = 'UTF-8';
	}

	public function set_encoding($encoding)
	{
		$this->encoding = $encoding;
	}

	public function phone($phone)
	{
		$this->error = array();

		$allowed = "()-+. \\d";
		if (preg_match("/[^$allowed]/", $phone) > 0) {
			$this->error = _l("Invalid Phone Number");
			return false;
		}

		$p = preg_replace("/[^\\d]/", '', $phone);

		if (!$this->text($p, 7, 32)) {
			$this->error = _l("Phone Number is Invalid");
		}

		return true;
	}

	public function email($email)
	{
		$this->error = array();

		if (preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i", $email) == 0) {
			$this->error = _l("Email address is invalid!");
			return false;
		}

		return true;
	}

	public function url($url, $protocol_required = false)
	{
		$this->error = array();

		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->error = _l("Url is invalid!");
			return false;
		}

		return true;
	}

	public function text($text, $min = 1, $max = -1)
	{
		if ($min >= 0 && strlen($text) < $min) {
			return false;
		}

		if ($max >= 0 && strlen($text) > $max) {
			return false;
		}

		return true;
	}

	public function password($password)
	{
		$this->error = array();

		if (strlen($password) < 8) {
			$this->error = _l("Password must be at least 8 characters long.");

			return false;
		}

		return true;
	}

	public function postcode($postcode)
	{
		return $this->text($postcode, 5,12);
	}

	/*
	 TODO: this is used in Form/address.php validation. Consider removing that functionality and this method...
	 */
	public function not_empty_zero($value)
	{
		return !empty($value);
	}

	public function datetime($date, $format = null)
	{
		$this->error = array();

		if (!$format) {
			$format = $this->language->getInfo('datetime_format');
		}

		$date_info = date_parse_from_format($format, $date);

		if ($date_info['errors'] || !checkdate($date_info['month'], $date_info['day'], $date_info['year'])) {
			$this->error = _l("invalid datetime format");
			return false;
		}

		return true;
	}
}
