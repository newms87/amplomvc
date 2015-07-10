<?php

class Phone extends Library
{
	public function format($phone, $format = null)
	{
		if (!is_array($phone)) {
			$phone = preg_replace("/[^\\d]/", '', (string)$phone);
			$phone = str_split($phone);
		}

		if ($format) {
			if ($format === 'tel') {
				return implode('', $phone);
			}

			return vsprintf($format, $phone);
		}

		$digits = count($phone);

		if ($digits === 11) {
			return vsprintf("+%d (%d%d%d) %d%d%d-%d%d%d%d", $phone);
		} elseif ($digits === 10) {
			return vsprintf("(%d%d%d) %d%d%d-%d%d%d%d", $phone);
		}

		return implode('', $phone);
	}
}
