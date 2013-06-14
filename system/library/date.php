<?php
class Date {
	private $registry;
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	/**
	 * Returns the current date and time
	 * 
	 * @param Int $return_type (optional) - Can be AC_DATE_STRING, AC_DATE_OBJECT, or AC_DATE_TIMESTAMP
	 * @param String $format (optional) - The date format compatible with PHP's date_format(). Default uses the language Datetime default format.
	 * 			Only used with $return_type = AC_DATE_STRING
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
		
		switch($return_type) {
			case AC_DATE_OBJECT:
				return $date;
			case AC_DATE_TIMESTAMP:
				return $date->getTimestamp();
			case AC_DATE_STRING:
			default:
				return $date->format($format);
		}
	}
	
	/**
	 * Returns the date added with the specified interval
	 * 
	 * @param DateTime $date (optional) - A DateTime object with the starting date, or null for the current date
	 * @param Mixed $interval (optional) - A DateInterval object or a string in the format parseable by PHP's strtotime(). 
	 * 			See link for more details. If not set, the current date will be returned.
	 * @param Int $return_type (optional) - Can be AC_DATE_STRING, AC_DATE_OBJECT, or AC_DATE_TIMESTAMP. Default is AC_DATE_STRING
	 * @param String $format (optional) - The date format compatible with PHP's date_format().
	 * 			Or 'short', 'long', 'datetime' ('default' is alias) for AmploCart Language specific format. Default uses the language Datetime default format.
	 * 			Only used with $return_type = AC_DATE_STRING
	 * 
	 * @link http://www.php.net/manual/en/datetime.formats.php
	 * 
	 * @return Mixed - string, DateTime object or Unix timestamp as specified in $return_type. Default is String
	 */
	public function add($date = null, $interval = '', $return_type = AC_DATE_STRING, $format = null)
	{
		if (!$date) {
			$date = new DateTime();
		}
		
		if (!empty($interval) && is_string($interval)) {
			try {
				$interval = new DateInterval($interval);
			} catch(Exception $e) { $interval = null;}
		}
		
		if ($interval) {
			$date->add($interval);
		}
		
		switch($return_type) {
			case AC_DATE_OBJECT:
				return $date;
			case AC_DATE_TIMESTAMP:
				return $date->getTimestamp();
			case AC_DATE_STRING:
			default:
				return $this->format($date, $format);
		}
	}
	
	public function format($date = null, $format = '')
	{
		if (!$date) {
			$date = new DateTime();
		} elseif (is_string($date)) {
			try {
				$date = new DateTime($date);
			} catch(Exception $e) {return $date;}
			
		}elseif (is_int($date)) {
			$ts = $date;
			$date = new DateTime();
			$date->setTimestamp($ts);
		}
		
		if (!$format) {
			$format = $this->language->getInfo('datetime_format');
		} else {
			switch ($format) {
				case 'short':
					$format = $this->language->getInfo('date_format_short');
					break;
				case 'long':
					$format = $this->language->getInfo('date_format_long');
					break;
				case 'default':
				case 'datetime':
					$format = $this->language->getInfo('datetime_format');
					break;
			}
		}
		
		return $date->format($format);
	}
}