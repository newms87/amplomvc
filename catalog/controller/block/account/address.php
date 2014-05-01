<?php
class Catalog_Controller_Block_Account_Address extends Controller
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

			case 'payment':
				$addresses = $this->customer->getPaymentAddresses($filter);
				break;

			case 'all':
			default:
				$addresses = $this->customer->getAddresses($filter);
				break;
		}

		foreach ($addresses as &$address) {
			$address['format'] = $this->address->format($address);
		}
		unset($address);


		if ($settings['add_address']) {
			$addresses[] = array(
				'address_id' => 'new',
				'format'     => '<div class="add-address">' . _l("New Address") . '</div>',
			);
		}

		$settings['addresses'] = $addresses;

		$this->render('block/account/address_select', $settings);
	}
}
