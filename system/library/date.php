<?php
class Date extends Library
{
	private $timezone;

	public function __construct()
	{
		parent::__construct();
		$this->timezone = new DateTimeZone(DEFAULT_TIMEZONE);
	}

	public function datetime(&$date = null, $format = null)
	{
		if (!$date) {
			$date = new DateTime("@" . _time());
		} elseif (is_string($date)) {
			try {
				if ($format) {
					$date = date_create_from_format($format, $date);
				} else {
					$date = new DateTime($date);
				}
			} catch (Exception $e) {
				$this->error['date_string'] = $e;
				return false;
			}
		} elseif (is_int($date)) {
			$date = new DateTime("@" . $date);
		}

		if (!$date) {
			$this->error['format'] = _l("Invalid Date Format");
			return false;
		}

		return $date->setTimezone($this->timezone);
	}

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

	public function now($format = '', $return_type = AC_DATE_STRING)
	{
		if (!$this->datetime($date)) {
			return false;
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

	public function isInPast($date, $datetimezero_true = true)
	{
		if ($date === DATETIME_ZERO) {
			return $datetimezero_true;
		}

		if (!$this->datetime($date)) {
			return false;
		}

		$diff = $date->diff($this->datetime());

		return $diff->invert === 0;
	}

	public function isInFuture($date, $datetimezero_true = false)
	{
		if ($date === DATETIME_ZERO) {
			return $datetimezero_true;
		}

		if (!$this->datetime($date)) {
			return false;
		}

		$diff = $date->diff($this->datetime());

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
	 *         Or 'short', 'long', 'datetime' ('default' is alias) for Amplo MVC Language specific format. Default uses the language Datetime default format.
	 *         Only used with $return_type = AC_DATE_STRING
	 *
	 * @link http://www.php.net/manual/en/datetime.formats.relative.php
	 * @link http://www.php.net/manual/en/datetime.formats.php
	 *
	 * @return Mixed - string, DateTime object or Unix timestamp as specified in $return_type. Default is String
	 */
	public function add($date = null, $interval = '', $return_type = AC_DATE_STRING, $format = null)
	{
		if (!$this->datetime($date)) {
			return false;
		}

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

	public function getCronUnits($date = null)
	{
		if (!$this->datetime($date)) {
			return false;
		}

		return array(
			'i' => (int)$this->format($date, 'i'),
			'h' => (int)$this->format($date, 'H'),
			'd' => (int)$this->format($date, 'd'),
			'm' => (int)$this->format($date, 'm'),
			'w' => ($w = (int)$this->format($date, 'w')) === 7 ? 0 : $w,
			'y' => (int)$this->format($date, 'Y'),
			't' => (int)$this->format($date, 't'),
		);
	}

	public function getDayOfWeek($date = null)
	{
		if (!$this->datetime($date)) {
			return false;
		}

		return $date->format('w');
	}

	public function getDayOfMonth($date = null)
	{
		if (!$this->datetime($date)) {
			return false;
		}

		return $date->format('d');
	}

	public function getDayOfYear($date = null)
	{
		if (!$this->datetime($date)) {
			return false;
		}

		return $date->format('z');
	}

	public function isEqual($d1, $d2)
	{
		$diff = $this->diff($d1, $d2);

		return $diff->days === 0 && $diff->h === 0 && $diff->i === 0 && $diff->s === 0;
	}

	public function isSameDay($d1, $d2)
	{
		$this->datetime($d1);
		$this->datetime($d2);
		$diff = $d1->diff($d2);

		return $diff->days === 0 && $d1->format('z') === $d2->format('z');
	}

	public function isToday($date)
	{
		return $this->isSameDay($date, null);
	}

	public function diff($d1, $d2 = null)
	{
		if (!$this->datetime($d1)) {
			return false;
		}

		if (!$this->datetime($d2)) {
			return false;
		}

		return $d1->diff($d2);
	}

	public function isBefore($d1, $d2)
	{
		return $this->diff($d1, $d2)->invert === 0;
	}

	public function isAfter($d1, $d2)
	{
		return $this->diff($d1, $d2)->invert === 1;
	}

	public function format($date = null, $format = '', $from_format = null)
	{
		if (!$this->datetime($date, $from_format)) {
			return false;
		}

		if (!$format) {
			$format = $this->language->info('datetime_format');
		} else {
			switch ($format) {
				case 'date_format_short':
				case 'short':
					$format = $this->language->info('date_format_short');
					break;
				case 'date_format_medium':
				case 'medium':
					$format = $this->language->info('date_format_medium');
					break;
				case 'date_format_long':
				case 'long':
					$format = $this->language->info('date_format_long');
					break;
				case 'time_format':
				case 'time_format_short':
				case'time':
					$format = $this->language->info('time_format');
					break;
				case 'datetime_format_short':
				case 'datetime_format_medium':
				case 'datetime_medium':
					$format = $this->language->info('datetime_format_medium');
					break;
				case 'datetime_format_long':
				case 'datetime_long':
					$format = $this->language->info('datetime_format_long');
					break;
				case 'datetime_format':
				case 'default':
				case 'datetime':
					$format = $this->language->info('datetime_format');
					break;
				case 'datetime_format_full':
				case 'full':
					$format = $this->language->info('datetime_format_full');
					break;
			}
		}

		return $date->format($format);
	}
}
