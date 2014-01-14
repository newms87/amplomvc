<?php
class Validation extends Library
{
	private $encoding;

	private $error_code = null;

	const PASSWORD_STRENGTH = 1;
	const PASSWORD_CONFIRM  = 2;
	const PHONE_INVALID     = 3;
	const EMAIL_INVALID     = 4;
	const URL_INVALID       = 5;
	const TEXT_LENGTH_MIN   = 6;
	const TEXT_LENGTH_MAX   = 7;
	const DATETIME_FORMAT   = 8;

	function __construct($registry)
	{
		parent::__construct($registry);

		$this->encoding = 'UTF-8';
	}

	public function isCode($code)
	{
		return $code === $this->error_code;
	}

	public function getCode()
	{
		return $this->error_code;
	}

	public function set_encoding($encoding)
	{
		$this->encoding = $encoding;
	}

	public function reset()
	{
		$this->error      = null;
		$this->error_code = null;
	}

	public function phone($phone)
	{
		$this->reset();

		$allowed = "()-+. \\d";
		if (preg_match("/[^$allowed]/", $phone) > 0) {
			$this->error      = _l("Invalid Phone Number");
			$this->error_code = self::PHONE_INVALID;
		}

		$p = preg_replace("/[^\\d]/", '', $phone);

		if (!$this->text($p, 7, 32)) {
			$this->error      = _l("Phone Number is Invalid");
			$this->error_code = self::PHONE_INVALID;
		}

		return $this->error ? false : true;
	}

	public function email($email)
	{
		$this->reset();

		if (preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i", $email) == 0) {
			$this->error      = _l("Email address is invalid!");
			$this->error_code = self::EMAIL_INVALID;
		}

		return $this->error ? false : true;
	}

	public function url($url, $protocol_required = false)
	{
		$this->reset();

		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->error      = _l("Url is invalid!");
			$this->error_code = self::URL_INVALID;
		}

		return $this->error ? false : true;
	}

	public function text($text, $min = 1, $max = -1)
	{
		$this->reset();

		if ($min >= 0 && strlen($text) < $min) {
			$this->error      = _l("Text length must be at least $min characters");
			$this->error_code = self::TEXT_LENGTH_MIN;
		}

		if ($max >= 0 && strlen($text) > $max) {
			$this->error      = _l("Text length must be at most $max characters");
			$this->error_code = self::TEXT_LENGTH_MAX;
		}

		return $this->error ? false : true;
	}

	public function password($password, $confirm = null)
	{
		$this->reset();

		if (strlen($password) < 8) {
			$this->error      = _l("Password must be at least 8 characters long.");
			$this->error_code = self::PASSWORD_STRENGTH;
		}

		if (!is_null($confirm)) {
			if ($confirm !== $password) {
				$this->error      = _l("Your Password and Confirmation do not match.");
				$this->error_code = self::PASSWORD_CONFIRM;
			}
		}

		return $this->error ? false : true;
	}

	public function postcode($postcode)
	{
		return $this->text($postcode, 5, 12);
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
		$this->reset();

		if (!$format) {
			$format = $this->language->info('datetime_format');
		}

		$date_info = date_parse_from_format($format, $date);

		if ($date_info['errors'] || !checkdate($date_info['month'], $date_info['day'], $date_info['year'])) {
			$this->error      = _l("invalid datetime format");
			$this->error_code = self::DATETIME_FORMAT;
		}

		return $this->error ? false : true;
	}

	public function fileUpload($file_upload)
	{
		$this->reset();

		if (!empty($file_upload['error'])) {
			switch ($file_upload['error']) {
				case 1:
					$this->error = _l("The uploaded file exceeds the upload_max_filesize directive in php.ini!");
					break;
				case 2:
					$this->error = _l("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form!");
					break;
				case 3:
					$this->error = _l("The uploaded file was only partially uploaded!");
					break;
				case 4:
					$this->error = _l("No file was uploaded!");
					break;
				case 6:
					$this->error = _l("Missing a temporary folder!");
					break;
				case 7:
					$this->error = _l("Failed to write file to disk!");
					break;
				case 8:
					$this->error = _l("File upload stopped by extension!");
					break;

				case 999:
				default:
					$this->error = _l("No error code available!");
					break;
			}

			$this->error_code = $file_upload['error'];
		}

		return $this->error ? false : true;
	}
}
