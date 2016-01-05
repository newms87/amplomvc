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

class Encryption extends Library
{
	private $key;

	function __construct()
	{
		parent::__construct();

		$this->key = option('config_encryption');
	}

	function encrypt($value)
	{
		if (!$this->key) {
			return $value;
		}

		$output = '';

		for ($i = 0; $i < strlen($value); $i++) {
			$char    = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char    = chr(ord($char) + ord($keychar));

			$output .= $char;
		}

		return base64_encode($output);
	}

	function decrypt($value)
	{
		if (!$this->key) {
			return $value;
		}

		$output = '';

		$value = base64_decode($value);

		for ($i = 0; $i < strlen($value); $i++) {
			$char    = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char    = chr(ord($char) - ord($keychar));

			$output .= $char;
		}

		return $output;
	}
}
