<?php
class Catalog_Controller_Extension_Payment_Braintree extends Controller
{
	private $settings;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->settings = $this->System_Extension_Payment_Braintree->settings();
	}

	public function index()
	{
		//Entry Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->customer->getMeta('braintree_cards');

		$this->data['card_select'] = $this->select_card($this->customer->getMeta('default_payment_key'));

		//Action Buttons
		$this->data['confirm'] = $this->url->link('extension/payment/braintree/confirm', 'order_id=' . $this->order->getId());

		$this->data['user_logged'] = $this->customer->isLogged();

		//The Template
		$this->template->load('extension/payment/braintree');

		//Render
		$this->render();
	}

	public function select_card($select_id = '', $remove = false)
	{
		//Entry Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->System_Extension_Payment_Braintree->getCards();

		foreach ($this->data['cards'] as &$card) {
			if ($remove) {
				$card['remove'] = $this->url->link('extension/payment/braintree/remove_card', 'card_id=' . $card['id']);
			}

			if ($select_id) {
				$card['default'] = $select_id === $card['id'];
			}
		}

		//Action Buttons
		$this->data['register_card'] = $this->url->link('extension/payment/braintree/register_card');

		//The Template
		$this->template->load('extension/payment/braintree_card_select');

		//Render
		return $this->render();
	}

	public function register_card()
	{
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

		$this->data += $card_info + $defaults;

		//Template Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];

		//Action Buttons
		$this->data['submit'] = $this->url->link('extension/payment/braintree/add_card');

		//The Template
		$this->template->load('extension/payment/braintree_register_card');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer',
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function add_card($card = array())
	{
		//Handle POST
		if ($this->request->isPost() && !empty($_POST)) {
			if (!$this->System_Extension_Payment_Braintree->addCard($_POST)) {
				$this->error = $this->System_Extension_Payment_Braintree->getError();
			}
		}

		//Resolve redirect
		if ($this->error) {
			$this->message->add('error', $this->error);
			$redirect = $this->url->link('extension/payment/braintree/register_card');
		} else {
			$redirect = $this->request->fetchRedirect();
			$this->message->add('success', _l("You have successfully registered your card with us!"));
		}

		if ($this->request->isAjax()) {
			$json = $this->message->fetch();

			if (!$this->error) {
				$json['redirect'] = $redirect;
			}

			$this->response->setOutput(json_encode($json));
		} else {
			$this->url->redirect($redirect);
		}
	}

	public function remove_card()
	{
		if (!empty($_GET['card_id'])) {
			if ($this->System_Extension_Payment_Braintree->removeCard($_GET['card_id'])) {
				$this->message->add('notify', _l("You have successfully removed the card from your account"));
			}
			else {
				$this->message->add('warning', $this->System_Extension_Payment_Braintree->getError());
			}
		} else {
			$this->message->add('warning', _l("There was no card selected to be removed."));
		}

		$this->url->redirect('account/update');
	}

	public function confirm()
	{
		$order_id = !empty($_GET['order_id']) ? $_GET['order_id'] : 0;

		if (!$order_id) {
			$this->message->add('error', _l("Order was not processed. Please try submitting your order again."));
			$this->url->redirect('checkout/checkout');
		}

		//Pay with Existing Credit Card
		if (!empty($_POST['existing_payment_card']) && !empty($_POST['payment_key'])) {
			if (!$this->customer->isLogged()) {
				$this->message->add('error', _l("You must be logged in to use a card from your account! Please try checking out again"));
				return false;
			}

			$this->order->setPaymentMethod($order_id, 'braintree', $_POST['payment_key']);

			$result = $this->System_Extension_Payment_Braintree->confirm($order_id);
		}
		//Pay with New Card
		else {
			$result = $this->System_Extension_Payment_Braintree->confirm($order_id, $_POST);
		}

		if (!$result) {
			$this->message->add('error', $this->System_Extension_Payment_Braintree->getError());
			$this->url->redirect('checkout/checkout');
		}

		//Clear Cart
		$this->cart->clear();

		$this->url->redirect('checkout/success', 'order_id=' .$order_id);
	}
}
