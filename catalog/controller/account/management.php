<?php
class Catalog_Controller_Account_Management extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/management'));

			$this->url->redirect('account/login');
		}

		//Page Head
		$this->document->setTitle(_l("Account Manager"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account Manager"), $this->url->link('account/management'));

		//Page Information
		$shipping_address               = $this->customer->getDefaultShippingAddress();
		$shipping_address['display']    = $this->address->format($shipping_address);
		$this->data['shipping_address'] = $shipping_address;

		//Customer Information
		$customer = $this->customer->info() + $this->customer->getMeta();

		$customer['display_name'] = $customer['firstname'] . ' ' . $customer['lastname'];

		$this->data['newsletter_display'] = $customer['newsletter'] ? _l("Send me RealMeal weekly updates!") : _l("Do not send me any emails.");

		$this->data['customer'] = $customer;

		//Urls
		$this->data['url_order_history'] = $this->url->link('account/order');
		$this->data['url_returns']       = $this->url->link('account/return');

		//Action Buttons
		$this->data['edit_account'] = $this->url->link('account/update');
		$this->data['back']         = $this->url->link('common/home');

		//The Template
		$this->template->load('account/management');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
