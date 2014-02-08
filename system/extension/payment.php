<?php
class System_Extension_Payment extends System_Extension_Extension
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/payment_interfaces.php");
	}

	public function renderTemplate()
	{
		$action = new Action($this->registry, 'extension/payment/' . $this->code);

		if ($action->isValid() && $action->execute()) {
			return $action->getOutput();
		}
	}

	public function validate($address)
	{
		if ((int)$this->settings['min_total'] > $this->cart->getTotal()) {
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			return false;
		}

		return true;
	}
}
