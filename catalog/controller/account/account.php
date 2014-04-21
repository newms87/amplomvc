<?php
class Catalog_Controller_Account_Account extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/account'));

			$this->url->redirect('account/login');
		}

		//Page Head
		$this->document->setTitle(_l("Account Manager"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account Manager"), $this->url->link('account/account'));

		//Page Information
		$shipping_address               = $this->customer->getDefaultShippingAddress();
		$shipping_address['display']    = $this->address->format($shipping_address);
		$data['shipping_address'] = $shipping_address;

		//Customer Information
		$customer = $this->customer->info() + $this->customer->getMeta();

		$customer['display_name'] = $customer['firstname'] . ' ' . $customer['lastname'];

		$data['newsletter_display'] = $customer['newsletter'] ? _l("Send me RealMeal weekly updates!") : _l("Do not send me any emails.");

		$data['customer'] = $customer;

		//Urls
		$data['url_order_history'] = $this->url->link('account/order');
		$data['url_returns']       = $this->url->link('account/return');

		//Action Buttons
		$data['edit_account'] = $this->url->link('account/update');
		$data['back']         = $this->url->link('common/home');

		//Render
		$this->response->setOutput($this->render('account/account', $data));
	}
}
