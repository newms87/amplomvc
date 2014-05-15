<?php

class Catalog_Controller_Extension_Payment_Braintree extends Controller
{
	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->settings = $this->System_Extension_Payment_Braintree->settings();
	}

	public function index()
	{
		//Entry Data
		$data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$data['cards']          = $this->customer->getMeta('braintree_cards');

		//Action Buttons
		$data['confirm'] = site_url('extension/payment/braintree/confirm', 'order_id=' . $this->order->getId());

		$data['user_logged'] = $this->customer->isLogged();

		//Render
		$this->render('extension/payment/braintree/braintree', $data);
	}

	public function register_card($settings = array())
	{
		//Default Settings
		$settings += array(
			'template' => 'extension/payment/braintree/register_card',
		);

		//Entry Data
		$card_info = array();

		if ($this->request->isPost()) {
			$card_info = $_POST;
		}

		$payment_address = $this->customer->getDefaultPaymentAddress();

		$defaults = array(
			'firstname' => $this->customer->info('firstname'),
			'lastname'  => $this->customer->info('lastname'),
			'postcode'  => $payment_address ? $payment_address['postcode'] : '',
		);

		$settings += $card_info + $defaults;

		//Template Data
		$settings['encryption_key'] = $this->settings['client_side_encryption_key'];

		//Action Buttons
		$settings['submit'] = site_url('extension/payment/braintree/add_card');

		//Render
		$this->response->setOutput($this->render($settings['template'], $settings));
	}

	public function select_card($settings = array())
	{
		//Default Settings
		$settings += array(
			'new_card'    => false,
			'remove_card' => false,
		);

		//Data
		$cards = $this->System_Extension_Payment_Braintree->getCards();

		if (!empty($settings['new_card'])) {
			$template = 'extension/payment/braintree/' . (is_string($settings['new_card']) ? $settings['new_card'] : 'register_fields');

			$cards['new'] = array(
				'id' => 'new',
			   'settings' => array(
				   'template' => $template,
			   ),
			);
		}

		$settings['cards'] = $cards;

		$settings['payment_key'] = $this->customer->getDefaultPaymentMethod('braintree');

		//Render
		$this->response->setOutput($this->render('extension/payment/braintree/select_card', $settings));
	}

	public function add_card()
	{
		//Handle POST
		if (!$this->System_Extension_Payment_Braintree->addCard($_POST)) {
			$this->message->add('error', $this->System_Extension_Payment_Braintree->getError());
		} else {
			$this->message->add('success', _l("You have successfully registered your card with us!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			}

			redirect('account');
		}
	}

	public function remove_card()
	{
		if (!empty($_GET['card_id'])) {
			if ($this->System_Extension_Payment_Braintree->removeCard($_GET['card_id'])) {
				$this->message->add('notify', _l("You have successfully removed the card from your account"));
			} else {
				$this->message->add('warning', $this->System_Extension_Payment_Braintree->getError());
			}
		} else {
			$this->message->add('warning', _l("There was no card selected to be removed."));
		}

		redirect('account/update');
	}

	public function confirm()
	{
		$order_id = !empty($_GET['order_id']) ? $_GET['order_id'] : 0;

		if (!$order_id) {
			$this->message->add('error', _l("Order was not processed. Please try submitting your order again."));
			redirect('checkout/checkout');
		}

		//Pay with Existing Credit Card
		if (!empty($_POST['existing_payment_card']) && !empty($_POST['payment_key'])) {
			if (!$this->customer->isLogged()) {
				$this->message->add('error', _l("You must be logged in to use a card from your account! Please try checking out again"));
				return false;
			}

			$this->order->setPaymentMethod($order_id, 'braintree', $_POST['payment_key']);

			$result = $this->System_Extension_Payment_Braintree->confirm($order_id);
		} //Pay with New Card
		else {
			$result = $this->System_Extension_Payment_Braintree->confirm($order_id, $_POST);
		}

		if (!$result) {
			$this->message->add('error', $this->System_Extension_Payment_Braintree->getError());
			redirect('checkout/checkout');
		}

		//Clear Cart
		$this->cart->clear();

		redirect('checkout/success', 'order_id=' . $order_id);
	}
}
