<?php

class System_Extension_Shipping extends System_Extension_Extension
{
	public function __construct()
	{
		parent::__construct();

		$this->settings += array(
			'geo_zone_id' => 0,
			'min_total'   => 0,
		);

	}

	//This is a required function
	public function getQuotes($address)
	{
		exit(_l("Implement %s for the %s extension", __METHOD__, $this->code));
	}

	public function validate($address)
	{
		//NOTE: Very important! Shipping should only call getSubTotal. calling cart->getTotals() may cause an infinite loop!
		if (!empty($this->settings['min_total']) && (int)$this->settings['min_total'] > $this->cart->getSubTotal()) {
			$this->error['total'] = _l("The total order must be at least %s to use this shipping method.", $this->settings['min_total']);
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			$this->error['geo_zone'] = _l("This shipping method cannot ship to the requested location.");
			return false;
		}

		return true;
	}
}
