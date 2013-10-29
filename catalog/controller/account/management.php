<?php
class Catalog_Controller_Account_Management extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/management');

			$this->url->redirect($this->url->link('account/login'));
		}

		//Page Head
		$this->document->setTitle(_("Account Manager"));

		//Breadcrumbs
		$this->breadcrumb->add(_("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_("Account Manager"), $this->url->link('account/management'));

		//Page Information
		$shipping_address               = $this->customer->getDefaultShippingAddress();
		$shipping_address['display']    = $this->address->format($shipping_address);
		$this->data['shipping_address'] = $shipping_address;

		//Customer Information
		$customer = $this->customer->info() + $this->customer->getMetaData();

		$customer['display_name'] = $customer['firstname'] . ' ' . $customer['lastname'];

		$this->data['newsletter_display'] = $customer['newsletter'] ? _("Send me RealMeal weekly updates!") : _("Do not send me any emails.");

		$this->data['customer'] = $customer;

		//Subscriptions
		$filter = array(
			'statuses' => array(
				Subscription::ACTIVE,
				Subscription::ON_HOLD,
			)
		);

		$subscriptions = $this->subscription->getCustomerSubscriptions(null, $filter);

		$thumb_width = $this->config->get('config_image_category_width');
		$thumb_height = $this->config->get('config_image_category_height');

		foreach ($subscriptions as $key => &$subscription) {
			switch ($subscription['status']) {
				case Subscription::ACTIVE:
					$subscription['edit'] = $this->url->link('account/subscription', 'subscription_id=' . $subscription['customer_subscription_id']);
					break;

				case Subscription::ON_HOLD:
					$subscription['edit'] = $this->url->link('account/subscription', 'subscription_id=' . $subscription['customer_subscription_id']);
					break;

				default:
					unset($subscriptions[$key]);
					break;
			}

			$subscription['thumb'] = $this->image->resize($subscription['product']['image'], $thumb_width, $thumb_height);
		}
		unset($subscription);

		$this->data['data_subscriptions'] = $subscriptions;

		//Action Buttons
		$this->data['edit_account'] = $this->url->link('account/update');
		$this->data['back'] = $this->url->link('common/home');

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