<?php
class System_Extension_Payment extends System_Extension_Extension
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/payment_interfaces.php");
	}

	public function validate($address, $total)
	{
		if ((int)$this->settings['min_total'] > $total) {
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			return false;
		}

		return true;
	}
}
