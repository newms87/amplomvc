<?php
class System_Extension_Payment_Braintree extends PaymentSubscriptionExtension
{
	private $cards;
	private $bt_customer;
	private $init = false;

	public function renderTemplate()
	{
		//Language
		$this->language->system('extension/payment/braintree');

		//Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->customer->getMeta('braintree_cards');

		$this->data['card_select'] = $this->cardSelect($this->customer->getMeta('default_payment_key'));

		//Action Buttons
		$this->data['confirm'] = $this->confirmUrl();

		$this->data['user_logged'] = $this->customer->isLogged();

		//The Template
		$this->template->load('payment/braintree');

		//Render
		$this->render();
	}

	public function charge($transaction)
	{
		if (!$this->initAPI()) {
			return;
		}

		$data = array(
			'amount'             => $transaction['amount'],
			'paymentMethodToken' => $transaction['payment_key'],
			'recurring'          => $transaction['type'] === 'subscription',
		);

		try {
			$result = Braintree_Transaction::sale($data);

			if ($result->success) {
				return true;
			}

			$this->resultError($result);
		} catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return false;
	}

	public function confirm()
	{
		if (!$this->initAPI()) {
			$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->get('config_contact_page_id'));
			$this->message->add('warning', _l("There was a problem while processing your transaction. Please choose a different Payment Method, or <a href=\"%s\">contact us</a> to complete your order.", $url_contact));
			$this->error_log->write(__METHOD__ . ": Failed to load Braintree API.");
			$this->url->redirect('checkout/checkout');
		}

		$order = $this->order->get($_GET['order_id']);

		if (empty($order)) {
			$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->get('config_contact_page_id'));
			$this->message->add('warning', _l("We were unable to process your order. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact));
			$this->error_log->write(__METHOD__ . ": Failed to lookup order ID: $_GET[order_id]. Unable to confirm checkout payment.");
			$this->url->redirect('checkout/checkout');
		}

		//Pay with Existing Credit Card
		if (!empty($_POST['existing_payment_card'])) {
			if (!$this->customer->isLogged()) {
				$this->message->add('warning', _l("You must be logged in to use a card from your account! Please try checking out again"));
				$this->url->redirect('checkout/checkout');
			}

			$this->loadCustomer();

			$sale = array(
				'amount'             => $order['total'],
				'customerId'         => $this->bt_customer->id,
				'paymentMethodToken' => $_POST['payment_key'],
			);

			$this->customer->setMeta('default_payment_key', $_POST['payment_key']);
		}
		//New Credit Card
		else {

			if (!$this->validateCard($_POST)) {
				$this->message->add('warning', $this->error);
				$this->url->redirect('checkout/checkout');
			}

			$sale = array(
				"amount"     => $order['total'],
				"creditCard" => array(
					"number"          => $_POST["number"],
					"cvv"             => $_POST["cvv"],
					"expirationMonth" => $_POST["month"],
					"expirationYear"  => $_POST["year"]
				),
				"options"    => array(
					"submitForSettlement" => true,
				)
			);

			if (!empty($_POST['save_to_account']) && $this->customer->isLogged()) {
				$this->loadCustomer();

				$sale['customerId']              = $this->bt_customer->id;
				$sale['options']['storeInVault'] = true;
			} else {
				$sale['customer'] = array(
					'firstName' => $order['firstname'],
					'lastName'  => $order['lastname'],
					'email'     => $order['email'],
					'phone'     => $order['telephone'],
					'fax'       => $order['fax'],
				);
			}
		}

		$result = Braintree_Transaction::sale($sale);

		if ($result->success) {
			$this->order->updateOrder($_GET['order_id'], $this->settings['complete_order_status_id'], _l("Order Completed via Braintree Payments"), true);

			$this->url->redirect('checkout/success', 'order_id=' . $_GET['order_id']);
		} else {
			$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->load('config', 'config_contact_page_id'));
			$this->message->add('warning', _l("There was a problem processing your transaction. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact));
			$this->error_log->write(__METHOD__ . ": Braintree_Transaction:sale() failed for order ID $_GET[order_id]. Unable to confirm checkout payment.");
			$this->url->redirect('checkout/checkout');
		}
	}

	/********************
	 * Card Transaction *
	 ********************/

	public function hasCard($id)
	{
		$cards = $this->getCards();

		return isset($cards[$id]);
	}

	public function getCard($id)
	{
		$cards = $this->getCards();

		return isset($cards[$id]) ? $cards[$id] : null;
	}

	public function getCards()
	{
		if (!$this->cards) {
			$this->cards = array();

			$this->loadCustomer();

			if ($this->bt_customer && !empty($this->bt_customer->creditCards)) {
				foreach ($this->bt_customer->creditCards as $card) {
					foreach ($this->bt_customer->creditCards as $card2) {
						if ($card2->token !== $card->token && $card2->uniqueNumberIdentifier === $card->uniqueNumberIdentifier) {
							$this->message->add('warning', _l("That card was already added to your account!"));
							$this->remove_card($card2->token);
							continue 2;
						}
					}

					$this->cards[$card->token] = array(
						'id'      => $card->token,
						'type'    => $card->cardType,
						'name'    => $card->cardholderName,
						'month'   => $card->expirationMonth,
						'year'    => $card->expirationYear,
						'masked'  => $card->maskedNumber,
						'last4'   => $card->last4,
						'image'   => $card->imageUrl,
						'default' => $card->isDefault(),
					);
				}
			}
		}

		return $this->cards;
	}

	public function add_card($card = array())
	{
		if (!$this->initAPI()) {
			return;
		}

		$this->language->system('extension/payment/braintree');

		//Handle POST
		if (empty($card) && $this->request->isPost()) {
			$card = $_POST;
		}

		if (empty($card['number'])) {
			$this->error[] = _l("There was a problem while processing your card. Please make sure Javascript is enabled and try again.");
		} else {
			$this->loadCustomer();

			//TODO: Use unique number identifier to clean up duplicates!
			if (!empty($this->bt_customer->id)) {
				$data = array(
					'customerId'      => $this->bt_customer->id,
					'number'          => $card['number'],
					'expirationMonth' => $card['month'],
					'expirationYear'  => $card['year'],
					'cvv'             => $card['cvv'],
					'cardholderName'  => $card['firstname'] . ' ' . $card['lastname'],
					'options'         => array(
						'verifyCard' => true,
					),
				);

				$result = Braintree_CreditCard::create($data);

				if (!empty($result->errors)) {
					$this->resultError($result);
				}
			}
		}

		//Resolve redirect
		$redirect = !empty($_SESSION['redirect']) ? $_SESSION['redirect'] : $this->url->link('account/update');
		unset($_SESSION['redirect']);

		if ($this->error) {
			$this->message->add('error', $this->error);
			$redirect = $this->callbackUrl('register_card');
		} else {
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

	public function update_card($id = null, $data)
	{
		if (!$this->initAPI()) {
			return;
		}

		if (!empty($data['default'])) {
			unset($data['default']);
			$data['options'] = array(
				'makeDefault' => true,
			);
		}

		Braintree_CreditCard::update($id, $data);
	}

	public function remove_card($id = null)
	{
		if (!$this->initAPI()) {
			return;
		}

		if (!$id) {
			$id = $_GET['card_id'];
		}

		Braintree_CreditCard::delete($id);

		$this->message->add('notify', _l("You have successfully removed the card from your account"));

		$this->url->redirect('account/update');
	}

	public function cardSelect($select_id = '', $remove = false)
	{
		//Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->getCards();

		foreach ($this->data['cards'] as &$card) {
			if ($remove) {
				$card['remove'] = $this->callbackUrl('remove_card', 'card_id=' . $card['id']);
			}

			if ($select_id) {
				$card['default'] = $select_id === $card['id'];
			}
		}

		//Action Buttons
		$this->data['register_card'] = $this->callbackUrl('register_card');

		//The Template
		$this->template->load('payment/braintree_card_select');

		//Render
		return $this->render();
	}

	public function register_card()
	{
		//Initialize BrainTree API
		if (!$this->initAPI()) {
			return;
		}

		$payment_address = $this->customer->getDefaultPaymentAddress();

		$defaults = array(
			'firstname' => $this->customer->info('firstname'),
			'lastname'  => $this->customer->info('lastname'),
			'postcode'  => $payment_address ? $payment_address['postcode'] : '',
		);

		$this->data += $_POST + $defaults;

		//Template Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];

		//Action Buttons
		$this->data['submit'] = $this->callbackUrl('add_card');

		//The Template
		$this->template->load('payment/braintree_register_card');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer',
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function getSubscription($id)
	{
		if (!$this->initAPI()) {
			return;
		}

		return Braintree_Subscription::find($id);
	}

	public function getPlans()
	{
		if (!$this->initAPI()) {
			return;
		}

		try {
			$results = Braintree_Plan::all();

			if ($results) {
				$plans = array();
				foreach ($results as $plan) {
					$plans[$plan->_attributes['id']] = $plan->_attributes;
				}

				return $plans;
			}
		} catch(Exception $e) {
			$this->error_log->write($e);
			$error_log = $this->url->admin('tool/logs', 'log=error');
			$this->message->add('warning', _l("There was a problem while communicating with Braintree. See more details in the <a target=\"_blank\" href=\"%s\">Error Log.</a>", $error_log));
		}
	}

	public function validateCard($card)
	{
		if (empty($card['number'])) {
			$this->error['number'] = _l("You must provide a credit card number");
		}

		if (empty($card['cvv'])) {
			$this->error['cvv'] = _l("You must provide the credit card CVV");
		}

		if (empty($card['month'])) {
			$this->error['month'] = _l("You must provide the 2 digit credit card Expiration Month");
		}

		if (empty($card['year'])) {
			$this->error['year'] = _l("You must provide the 4 digit credit card Expiration Year");
		}

		return $this->error ? false : true;
	}

	private function resultError($result)
	{
		if ($result->_attributes && !empty($result->_attributes['message'])) {
			$this->error[] = $result->_attributes['message'];
		} elseif ($result->errors) {
			foreach ($result->errors->deepAll() as $error) {
				$this->error[] = $error->message;
			}
		} else {
			$this->error[] = _l("Transaction failed. Unable to activate subscription.");
		}

		$this->error_log->write('Braintree Result Error: ' . implode('; ', $this->error) . get_caller(0, 2));
	}

	private function initAPI()
	{
		if (!$this->init) {
			if (!empty($this->settings['merchant_id'])) {
				require_once DIR_RESOURCES . '/braintree/lib/Braintree.php';

				Braintree_Configuration::environment($this->settings['mode']);
				Braintree_Configuration::merchantId($this->settings['merchant_id']);
				Braintree_Configuration::publicKey($this->settings['public_key']);
				Braintree_Configuration::privateKey($this->settings['private_key']);

				$this->init = true;
			}
		}

		return $this->init;
	}

	private function loadCustomer()
	{
		if (!$this->customer->isLogged()) {
			return;
		}

		if (!$this->bt_customer) {
			//Load BrainTree API
			if (!$this->initAPI()) {
				return;
			}

			$braintree_id = $this->customer->getMeta('braintree_id');

			if ($braintree_id) {
				try {
					$this->bt_customer = Braintree_Customer::find($braintree_id);
				} catch (Braintree_Exception_NotFound $e) {
					$this->language->system('extension/payment/braintree');
					$this->message->add('warning', _l("Your Customer information was not found. Please try <a href=\"%s\">registering a credit card</a>", $this->callbackUrl('register_card')));
					$this->error_log->write(__METHOD__ . _l("(): The customer with ID %s was not found!", $braintree_id));
					$this->url->redirect($this->callbackUrl('register_card'));
				}
			} else {
				$customer = $this->customer->info();

				$data = array(
					'firstName' => $customer['firstname'],
					'lastName'  => $customer['lastname'],
					'email'     => $customer['email'],
					'phone'     => $customer['telephone'],
					'fax'       => $customer['fax'],
					//TODO: Add website to customers ... 'website' => $customer['website'],
				);

				$result = Braintree_Customer::create($data);

				if ($result->success) {
					//If new customer created, save customer ID for braintree (NOTE: $result->customer->id returned only for new customers)
					if (!empty($result->customer) && !empty($result->customer->id)) {
						$this->customer->setMeta('braintree_id', $result->customer->id);
					}
				} elseif (!empty($result->errors)) {
					$this->resultError($result);
				}
			}
		}
	}
}
