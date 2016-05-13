<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Account extends Controller
{
	public function __construct()
	{
		parent::__construct();

		switch ($this->router->getAction()->getMethod()) {
			case 'index':
			case 'details':
			case 'save':
				if (!is_logged()) {
					$this->request->setRedirect($this->url->here());
					redirect('customer/login');
				}
				break;
		}
	}

	public function index($content = '')
	{
		if (!$content) {
			return $this->details();
		}

		$data['path']    = $this->router->getPath();
		$data['content'] = $content;

		//Render
		output($this->render('account/account', $data));
	}

	public function details()
	{
		//Page Head
		set_page_info('title', _l("My Details"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("My Account"), site_url('account'));
		breadcrumb(_l("My Details"), site_url('account/details'));

		$data['path'] = $this->router->getPath();

		//Customer Information
		$customer['customer'] = customer_info();
		$customer['meta']     = $this->customer->meta();

		$options = array(
			'index' => 'address_id',
		);

		$customer['addresses'] = $this->Model_Customer->getAddresses(customer_info('customer_id'), null, null, $options);

		$filter = array(
			'country_id' => option('config_country_id', 223),
		);

		$customer['data_zones'] = $this->Model_Localisation_Zone->getRecords(null, $filter, array('cache' => true));

		//Render
		output($this->render('account/details', $customer));
	}

	public function save()
	{
		if ($this->Model_Customer->save(customer_info('customer_id'), $_POST)) {
			message('success', _l("Your account information has been updated successfully!"));
		} else {
			message('error', $this->Model_Customer->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account');
		}
	}

	public function confirm_email()
	{
		$email = _request('email');
		$customer_email = customer_info('email');

		if ($email) {
			if ($customer_email) {
				if ($customer_email === $email) {
					$this->customer->saveMeta('confirmed_email', $email);
					message('confirm-email', _l("Thank you! Your email has been confirmed!"), 'confirm');
				} else {
					message('error', _l("Your email did not match your registered email address %s. Please log in to your account using username %s.", $customer_email, $email));
				}
			} else {
				message('notify', _l("Please log into your account to confirm your email."));
				redirect('customer/login', array('redirect' => $this->url->here()));
			}
		} else {
			message('error', _l("Unable to confirm your email address. Please try again."));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('');
		}
	}

	public function resend_confirmation()
	{
		if (!is_logged()) {
			message('error', _l("Please log into your account first before confirming your email"));
			redirect('customer/login');
		}

		if ($this->customer->sendConfirmationEmail()) {
			message('confirm-email', _l("Confirmation Email has been resent to %s", customer_info('email')), 'confirm');
		} else {
			message('error', $this->customer->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect();
		}
	}
}
