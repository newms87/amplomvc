<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Block_Account_Address extends App_Controller_Block_Block
{
	public function select($settings)
	{
		$defaults = array(
			'address_id'  => '',
			'customer_id' => customer_info('customer_id'),
			'type'        => 'all',
			'add_address' => true,
		);

		$settings += $defaults;

		switch ($settings['type']) {
			case 'shipping':
				$addresses = $this->customer->getShippingAddresses();
				break;

			case 'all':
			default:
				$addresses = $this->Model_Customer->getAddresses(customer_info('customer_id'));
				break;
		}

		$settings['addresses'] = $addresses;

		$this->render('block/account/address_select', $settings);
	}
}
