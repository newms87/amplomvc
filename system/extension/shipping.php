<?php
class System_Extension_Shipping extends System_Extension_Extension
{
	//This is a required function
	public function getQuote($address)
	{
		exit(_l("Implement %s for the %s extension", __METHOD__, $this->code));
	}

	public function validate($address, $total)
	{
		if ((int)$this->settings['min_total'] > $total) {
			$this->error['total'] = _l("The total order must be at least " . (int)$this->settings['min_total'] . " to use this shipping method.");
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			$this->error['geo_zone'] = _l("This shipping method cannot ship to the requested location.");
			return false;
		}

		return true;
	}
}
