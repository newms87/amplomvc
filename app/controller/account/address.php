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

class App_Controller_Account_Address extends Controller
{
	public function index()
	{
		//TODO: THis is not yet implemented...
		redirect('account/details');

		if (!is_logged()) {
			$this->request->setRedirect('account/address/form');

			redirect('customer/login');
		}

		//Page Head
		set_page_info('title', _l("Address Book"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Address Book"), site_url('account/address'));

		//Load Addresses
		$addresses = $this->Model_Customer->getAddresses(customer_info('customer_id'));

		foreach ($addresses as &$address) {
			$address['address'] = format('address', $address);
			$address['update']  = site_url('account/address/save', 'address_id=' . $address['address_id']);
			$address['delete']  = site_url('account/address/remove', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$data['addresses'] = $addresses;

		//Action Buttons
		$data['insert'] = site_url('account/address/form');
		$data['back']   = site_url('account');

		//Render
		output($this->render('account/address_list', $data));
	}

	public function form()
	{
		//TODO: THis is not yet implemented...
		redirect('account/details');

		//Page Head
		set_page_info('title', _l("Address Form"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Address Book"), site_url('account/address'));
		breadcrumb(_l("Form"), site_url('account/address/form'));

		//Insert or Update
		$address_id = !empty($_GET['address_id']) ? (int)$_GET['address_id'] : 0;

		//Load Information
		$defaults = array(
			'name'       => customer_info('first_name') . ' ' . customer_info('last_name'),
			'company'    => '',
			'address'    => '',
			'address_2'  => '',
			'postcode'   => '',
			'city'       => '',
			'country_id' => option('config_country_id'),
			'zone_id'    => '',
			'default'    => false,
		);

		$address_info = $_POST;

		if (!IS_POST && $address_id) {
			$address_info            = $this->Model_Address->getRecord(_get('address_id'));
			$address_info['default'] = (int)$this->customer->getDefaultShippingAddressId() === $address_id;
		}

		$data = $address_info + $defaults;

		$data['address_id'] = $address_id;

		//Template Data
		$data['data_countries'] = $this->Model_Localisation_Country->getActiveCountries();

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$data['save'] = site_url('account/address/update', 'address_id=' . $address_id);

		//Render
		$template = $this->is_ajax ? 'account/address_form_ajax' : 'account/address_form';
		output($this->render($template, $data));
	}

	public function save()
	{
		$address_id = $this->Model_Customer->saveAddress(customer_info('customer_id'), _get('address_id'), $_POST);

		if ($address_id) {
			message('success', _l("You have successfully added an address to your account!"));
		} else {
			message('error', $this->Model_Customer->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('account/address/form', 'address_id=' . _get('address_id'));
		} else {
			redirect('account/address');
		}
	}

	public function remove()
	{
		if ($this->Model_Customer->removeAddress(customer_info('customer_id'), _request('address_id'))) {
			message('success', _l("Your address has been successfully deleted"));
		} else {
			message('error', $this->Model_Customer->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account/address');
		}
	}
}
