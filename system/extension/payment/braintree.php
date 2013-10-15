<?php
class System_Extension_Payment_Braintree extends Extension
{
	private $cards;
	private $bt_customer;

	public function renderTemplate()
	{
		//Language
		$this->language->system('extension/payment/braintree');


		//Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->customer->getMeta('braintree_cards');

		//Action Buttons
		$this->data['confirm']       = $this->confirmUrl();
		$this->data['register_card'] = $this->callbackUrl('register_card');

		//The Template
		$this->template->load('payment/braintree');

		//Render
		$this->render();
	}

	public function callback()
	{

	}

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
							$this->message->add('warning', $this->_('error_duplicate_card'));
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

	public function card_select($remove = false)
	{
		//Language
		$this->language->system('extension/payment/braintree');

		//Data
		$this->data['encryption_key'] = $this->settings['client_side_encryption_key'];
		$this->data['cards']          = $this->getCards();

		if ($remove) {
			foreach ($this->data['cards'] as &$card) {
				$card['remove'] = $this->callbackUrl('remove_card', 'card_id=' . $card['id']);
			}
		}

		//Action Buttons
		$this->data['register_card'] = $this->callbackUrl('register_card');

		//The Template
		$this->template->load('payment/braintree_card_select');

		//Render
		return $this->render();
	}

	public function update_card($id = null, $data)
	{
		$this->initAPI();

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
		$this->language->system('extension/payment/braintree');

		if (!$id) {
			$id = $_GET['card_id'];
		}

		$this->initAPI();

		Braintree_CreditCard::delete($id);

		$this->message->add('notify', $this->_('text_removed_card'));

		$this->url->redirect($this->url->link('account/update'));
	}

	public function register_card()
	{
		//Initialize BrainTree API
		$this->initAPI();

		//Language
		$this->language->system('extension/payment/braintree');

		$payment_address = $this->customer->getDefaultPaymentAddress();

		$defaults = array(
			'firstname' => $this->customer->info('firstname'),
			'lastname'  => $this->customer->info('lastname'),
			'postcode'  => $payment_address ? $payment_address['postcode'] : '',
		);

		$this->data += $_POST + $defaults;

		//Additional Data
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

	public function add_card($card = array())
	{
		$this->language->system('extension/payment/braintree');

		//Handle POST
		if (empty($card) && $this->request->isPost()) {
			$card = $_POST;
		}

		if (empty($card['number'])) {
			$this->error[] = $this->_('error_no_card');
		} else {
			$this->loadCustomer();

			//TODO: USe unique number identifier to clean up duplicates!
			if (!empty($this->bt_customer->id)) {
				$data = array(
					'customerId'      => $this->bt_customer->id,
					'number'          => $card['number'],
					'expirationMonth' => $card['month'],
					'expirationYear'  => $card['year'],
					'cvv'             => $card['cvv'],
					'cardholderName'  => $card['firstname'] . ' ' . $card['lastname'],
				);

				$result = Braintree_CreditCard::create($data);
			} else {
				$data = array(
					"firstName"  => $card["firstname"],
					"lastName"   => $card["lastname"],
					"creditCard" => array(
						"number"          => $card["number"],
						"expirationMonth" => $card["month"],
						"expirationYear"  => $card["year"],
						"cvv"             => $card["cvv"],
						"billingAddress"  => array(
							"postalCode" => $card["postcode"]
						)
					)
				);

				$result = Braintree_Customer::create($data);
			}

			if ($result->success) {
				//If new customer created, save customer ID for braintree (NOTE: $result->customer->id returned only for new customers)
				if (!empty($result->customer) && !empty($result->customer->id)) {
					$this->customer->setMeta('braintree_id', $result->customer->id);
				}
			} elseif (!empty($result->errors)) {
				foreach ($result->errors->deepAll() as $error) {
					$this->error[] = $error->message;
				}
			}
		}

		//Resolve redirect
		$redirect = !empty($_SESSION['redirect']) ? $_SESSION['redirect'] : $this->url->link('account/update');
		unset($_SESSION['redirect']);

		if ($this->request->isAjax()) {
			if ($this->error) {
				$json = array(
					'error' => $this->error,
				);
			} else {
				$json = array(
					'success'  => $this->_('text_success_register_card'),
					'redirect' => $redirect,
				);
			}

			$this->response->setOutput(json_encode($json));
		} else {
			if ($this->error) {
				$this->message->add('error', $this->error);
				$this->url->redirect($this->callbackUrl('register_card'));
			} else {
				$this->message->add('success', $this->_('text_success_register_card'));
				$this->url->redirect($redirect);
			}
		}
	}

	public function getPlans()
	{
		$this->initAPI();

		$results = Braintree_Plan::all();

		if ($results) {
			$plans = array();
			foreach ($results as $plan) {
				$plans[$plan->_attributes['id']] = $plan->_attributes;
			}

			return $plans;
		}
	}

	public function confirmSubscription($subscription_data)
	{
		$this->initAPI();

		$card = $this->getCard($subscription_data['payment_key']);

		$data = array(
			'paymentMethodToken' => $card['id'],
			'planId'             => $this->settings['plan_id'],
		);

		$results = Braintree_Subscription::create($data);

		$this->subscription->setMeta($subscription_data['customer_subscription_id'], 'braintree_subscription_id', $results->subscription->_attributes['id']);
	}

	public function validateSubscription(&$subscription_data)
	{
		if (empty($_POST['credit_card']) || !$this->hasCard($_POST['credit_card'])) {
			$this->error['credit_card'] = $this->_('error_credit_card_id');
		} else {
			$subscription_data['payment_key'] = $_POST['credit_card'];
		}

		return $this->error ? false : true;
	}

	public function validate($address, $total)
	{
		if (!parent::validate($address, $total)) {
			return false;
		}

		return true;
	}

	private function initAPI()
	{
		require_once DIR_RESOURCES . '/braintree/lib/Braintree.php';

		Braintree_Configuration::environment($this->settings['mode']);
		Braintree_Configuration::merchantId($this->settings['merchant_id']);
		Braintree_Configuration::publicKey($this->settings['public_key']);
		Braintree_Configuration::privateKey($this->settings['private_key']);
	}

	private function loadCustomer()
	{
		if (!$this->bt_customer) {
			$braintree_id = $this->customer->getMeta('braintree_id');

			if ($braintree_id) {
				//Load BrainTree API
				$this->initAPI();

				try {
					$this->bt_customer = Braintree_Customer::find($braintree_id);
				} catch (Braintree_Exception_NotFound $e) {
					$this->language->system('extension/payment/braintree');
					$this->message->add('warning', $this->_('error_braintree_customer', $this->callbackUrl('register_card')));
					$this->error_log->write("System_Extension_Payment_Braintree::loadCustomer(): The customer with ID $braintree_id was not found!");
					$this->url->redirect($this->callbackUrl('register_card'));
				}
			}
		}
	}
}
