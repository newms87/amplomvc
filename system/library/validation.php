<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class Validation extends Library
{
	private $encoding;

	const PASSWORD_STRENGTH = 'password_strength';
	const PASSWORD_CONFIRM = 'password_confirm';
	const PHONE_INVALID = 'phone_invalid';
	const EMAIL_INVALID = 'email_invalid';
	const URL_INVALID = 'url_invalid';
	const TEXT_LENGTH_MIN = 'text_min';
	const TEXT_LENGTH_MAX = 'text_max';
	const DATETIME_FORMAT = 'date_format';

	function __construct()
	{
		parent::__construct();

		$this->encoding = 'UTF-8';
	}

	public function getError($with_keys = false)
	{
		if ($this->error && is_array($this->error)) {
			return implode(',', $this->error);
		}

		return $this->error;
	}

	public function isErrorCode($code)
	{
		return $code === key($this->error);
	}

	public function getErrorCode()
	{
		return key($this->error);
	}

	public function set_encoding($encoding)
	{
		$this->encoding = $encoding;
	}

	public function reset()
	{
		$this->error = null;
	}

	public function phone($phone)
	{
		$this->reset();

		$allowed = "()-+. \\d";
		if (preg_match("/[^$allowed]/", $phone) > 0) {
			$this->error[self::PHONE_INVALID] = _l("Invalid Phone Number");
		}

		$p = preg_replace("/[^\\d]/", '', $phone);

		if (!$this->text($p, 7, 32)) {
			$this->error[self::PHONE_INVALID] = _l("Phone Number is Invalid");
		}

		return empty($this->error);
	}

	public function email($email)
	{
		$this->reset();

		if (preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i", $email) == 0) {
			$this->error[self::EMAIL_INVALID] = _l("Email address is invalid!");
		}

		return empty($this->error);
	}

	public function url($url, $protocol_required = false)
	{
		$this->reset();

		if (!is_url($url)) {
			$this->error[self::URL_INVALID] = _l("Url is invalid!");
		}

		return empty($this->error);
	}

	public function text($text, $min = 1, $max = -1)
	{
		$this->reset();

		if ($min >= 0 && strlen($text) < $min) {
			$this->error[self::TEXT_LENGTH_MIN] = _l("Text length must be at least $min characters");
		}

		if ($max >= 0 && strlen($text) > $max) {
			$this->error[self::TEXT_LENGTH_MAX] = _l("Text length must be at most $max characters");
		}

		return empty($this->error);
	}

	public function password($password, $confirm = null)
	{
		$this->reset();

		if (strlen($password) < 8) {
			$this->error[self::PASSWORD_STRENGTH] = _l("Password must be at least 8 characters long.");
		}

		if ($confirm !== null) {
			if ($confirm !== $password) {
				$this->error[self::PASSWORD_CONFIRM] = _l("Your Password and Confirmation do not match.");
			}
		}

		return empty($this->error);
	}

	public function postcode($postcode)
	{
		return $this->text($postcode, 5, 12);
	}

	public function datetime($date, $format = null)
	{
		$this->reset();

		if (!$format) {
			$format = $this->language->info('datetime_format');
		}

		$date_info = date_parse_from_format($format, $date);

		if ($date_info['errors'] || !checkdate($date_info['month'], $date_info['day'], $date_info['year'])) {
			$this->error[self::DATETIME_FORMAT] = _l("invalid datetime format");
		}

		return empty($this->error);
	}

	public function fileUpload($file_upload)
	{
		$this->reset();

		if (!empty($file_upload['error'])) {
			switch ($file_upload['error']) {
				case 1:
					$error = _l("The uploaded file exceeds the upload_max_filesize directive in php.ini!");
					break;
				case 2:
					$error = _l("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form!");
					break;
				case 3:
					$error = _l("The uploaded file was only partially uploaded!");
					break;
				case 4:
					$error = _l("No file was uploaded!");
					break;
				case 6:
					$error = _l("Missing a temporary folder!");
					break;
				case 7:
					$error = _l("Failed to write file to disk!");
					break;
				case 8:
					$error = _l("File upload stopped by extension!");
					break;

				case 999:
				default:
					$error = _l("No error code available!");
					break;
			}

			$this->error[$file_upload['error']] = $error;
		}

		return empty($this->error);
	}
}
