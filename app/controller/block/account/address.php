<?php
class App_Controller_Block_Account_Address extends App_Controller_Block_Block
{
	public function select($settings)
	{
		$defaults = array(
			'address_id'  => '',
			'customer_id' => $this->customer->getId(),
			'type'        => 'all',
			'add_address' => true,
		);

		$settings += $defaults;

		$filter = array(
			'customer_ids' => array($settings['customer_id']),
		);

		switch ($settings['type']) {
			case 'shipping':
				$addresses = $this->customer->getShippingAddresses($filter);
				break;

			case 'all':
			default:
				$addresses = $this->Model_Customer->getAddresses($this->customer->getId(), $filter);
				break;
		}

		$settings['addresses'] = $addresses;

		$this->render('block/account/address_select', $settings);
	}
}
