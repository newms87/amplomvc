<?php
class Date extends Library
{
	/**
	 * Returns the current date and time
	 *
	 * @param Int $return_type (optional) - Can be AC_DATE_STRING, AC_DATE_OBJECT, or AC_DATE_TIMESTAMP
	 * @param String $format (optional) - The date format compatible with PHP's date_format(). Default uses the language Datetime default format.
	 *         Only used with $return_type = AC_DATE_STRING
	 *
	 * @link http://www.php.net/manual/en/datetime.formats.php
	 *
	 * @return Mixed - string, DateTime object or Unix timestamp as specified in $return_type. Default is String
	 */

	public function now($return_type = AC_DATE_STRING, $format = '')
	{
		$date = new DateTime();

		if (!$format) {
			$format = $this->language->getInfo('datetime_format');
		}

		switch ($return_type) {
			case AC_DATE_OBJECT:
				return $date;
			case AC_DATE_TIMESTAMP:
				return $date->getTimestamp();
			case AC_DATE_STRING:
			default:
				return $date->format($format);
		}
	}

	public function isInPast($date, $datetimezero_true = true)
	{
		if ($date === DATETIME_ZERO) {
			return $datetimezero_true;
		}

		$this->getObject($date);

		$diff = $date->diff(new DateTime());

		return $diff->invert === 0;
	}

	public function isInFuture($date, $datetimezero_true = false)
	{
		if ($date === DATETIME_ZERO) {
			return $datetimezero_true;
		}

		$this->getObject($date);

		$diff = $date->diff(new DateTime());

		return $diff->invert === 1;
	}

	/**
	 * Returns the date added with the specified interval
	 *
	 * @param DateTime $date (optional) - A DateTime object with the starting date, or null for the current date
	 * @param Mixed $interval (optional) - A DateInterval object or a string in the format parseable by PHP's strtotime().
	 *         (see first link on relative formats) If not set, the current date will be returned.
	 * @param Int $return_type (optional) - Can be AC_DATE_STRING, AC_DATE_OBJECT, or AC_DATE_TIMESTAMP. Default is AC_DATE_STRING
	 * @param String $format (optional) - The date format compatible with PHP's date_format().
	 *         Or 'short', 'long', 'datetime' ('default' is alias) for AmploCart Language specific format. Default uses the language Datetime default format.
	 *         Only used with $return_type = AC_DATE_STRING
	 *
	 * @link http://www.php.net/manual/en/datetime.formats.relative.php
	 * @link http://www.php.net/manual/en/datetime.formats.php
	 *
	 * @return Mixed - string, DateTime object or Unix timestamp as specified in $return_type. Default is String
	 */
	public function add($date = null, $interval = '', $return_type = AC_DATE_STRING, $format = null)
	{
		$this->getObject($date);

		if (!empty($interval) && is_string($interval)) {
			$interval = date_interval_create_from_date_string($interval);
		}

		if ($interval) {
			$date->add($interval);
		}

		switch ($return_type) {
			case AC_DATE_OBJECT:
				return $date;
			case AC_DATE_TIMESTAMP:
				return $date->getTimestamp();
			case AC_DATE_STRING:
			default:
				return $this->format($date, $format);
		}
	}

	public function getDayOfWeek($date = null)
	{
		return $this->format($date, 'w');
	}

	public function getDayOfMonth($date = null)
	{
		return $this->format($date, 'd');
	}

	public function getDayOfYear($date = null)
	{
		return $this->format($date, 'z');
	}

	public function diff($d1, $d2 = null)
	{
		$this->getObject($d1);
		$this->getObject($d2);

		return $d1->diff($d2);
	}

	public function isBefore($d1, $d2)
	{
		return $this->diff($d1,$d2)->invert == 1;
	}

	public function isAfter($d1, $d2)
	{
		return $this->diff($d1,$d2)->invert == 0;
	}

	public function format($date = null, $format = '')
	{
		$this->getObject($date);

		if (!$format) {
			$format = $this->language->getInfo('datetime_format');
		} else {
			switch ($format) {
				case 'date_format_short':
				case 'short':
					$format = $this->language->getInfo('date_format_short');
					break;
				case 'date_format_long':
				case 'long':
					$format = $this->language->getInfo('date_format_long');
					break;
				case 'time_format';
				case'time':
					$format = $this->language->getInfo('time_format');
					break;
				case 'datetime_format_long':
				case 'datetime_long':
					$format = $this->language->getInfo('datetime_format_long');
					break;
				case 'datetime_format':
				case 'default':
				case 'datetime':
					$format = $this->language->getInfo('datetime_format');
					break;
			}
		}

		return $date->format($format);
	}

	public function getObject(&$date)
	{
		if (!$date) {
			$date = new DateTime();
		} elseif (is_string($date)) {
			try {
				$date = new DateTime($date);
			} catch (Exception $e) {
				return $date;
			}

		} elseif (is_int($date)) {
			$ts   = $date;
			$date = new DateTime();
			$date->setTimestamp($ts);
		}

		return $date;
	}
}
