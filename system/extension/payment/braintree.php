<?php
class System_Extension_Payment_Braintree extends PaymentSubscription
{
	private $cards;
	private $bt_customer;
	private $init = false;

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

		$this->language->system('extension/payment/braintree');

		if (!$id) {
			$id = $_GET['card_id'];
		}

		Braintree_CreditCard::delete($id);

		$this->message->add('notify', $this->_('text_removed_card'));

		$this->url->redirect($this->url->link('account/update'));
	}

	public function charge($card_id, $amount, $info = array())
	{
		if (!$this->initAPI()) {
			return;
		}

		$data = array(
			'amount'             => $amount,
			'paymentMethodToken' => $card_id,
			'recurring'          => !empty($info['recurring']),
		);

		try {
			$result = Braintree_Transaction::sale($data);

			if ($result->success) return true;

			$this->resultError($result);
		}
		catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return false;
	}

	public function chargeSubscription($customer_subscription)
	{
		return $this->charge($customer_subscription['payment_key'], $customer_subscription['total'], array('recurring' => true));
	}

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

	public function cardSelect($select_id = '', $remove = false)
	{
		//Language
		$this->language->system('extension/payment/braintree');

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

	public function getSubscription($id)
	{
		if (!$this->initAPI()) return;

		return Braintree_Subscription::find($id);
	}

	public function getPlans()
	{
		if (!$this->initAPI()) return;

		$results = Braintree_Plan::all();

		if ($results) {
			$plans = array();
			foreach ($results as $plan) {
				$plans[$plan->_attributes['id']] = $plan->_attributes;
			}

			return $plans;
		}
	}

	/*
	public function addSubscription($subscription)
	{
		if (!$this->initAPI()) return;

		$this->language->system('extension/payment/braintree');

		$braintree_subscription_id = $this->subscription->getMeta($subscription['customer_subscription_id'], 'braintree_subscription_id');

		//Check if this subscription already is associated with a BrainTree Subscription
		if ($braintree_subscription_id) {
			$bt_subscription = $this->getSubscription($braintree_subscription_id);

			$failed_statuses = array(
				Braintree_Subscription::CANCELED,
				Braintree_Subscription::EXPIRED,
			);

			if ($bt_subscription && !in_array($bt_subscription->status, $failed_statuses)
			) {
				$this->message->add('warning', $this->_('error_subscription_active'));
				return false;
			}
		}

		$card = $this->getCard($subscription['payment_key']);

		$data = array(
			'paymentMethodToken' => $card['id'],
			'planId'             => $this->settings['plan_id'],
			'price'              => $subscription['total'],
		);

		if ($this->date->isInFuture($subscription['subscription']['start_date'])) {
			$data['firstBillingDate'] = new DateTime($subscription['subscription']['start_date']);
		} elseif (!empty($subscription['day_of_month'])) {
			$data['billingDayOfMonth'] = $subscription['subscription']['day_of_month'];
		} else {
			$data['options'] = array('startImmediately' => true);
		}

		$result = Braintree_Subscription::create($data);

		if ($result->success) {
			$this->subscription->setMeta($subscription['customer_subscription_id'], 'braintree_subscription_id', $result->subscription->_attributes['id']);

			return true;
		}

		$this->resultError($result, true);

		$this->error_log->write('System_Extension_Payment_Braintree::addSubscription(): ' . implode('; ', $this->error));

		return false;
	}

	public function updateSubscription($customer_subscription_id, $data)
	{
		if (!$this->initAPI()) return;

		$this->language->system('extension/payment/braintree');

		$id = $this->subscription->getMeta($customer_subscription_id, 'braintree_subscription_id');

		if ($id) {
			$braintree_data = array(
				'price'              => $data['total'],
				'paymentMethodToken' => $data['payment_key'],
				'options'            => array(
					'prorateCharges'                       => $data['prorate'],
					'revertSubscriptionOnProrationFailure' => true,
				),
			);

			try {
				$result = Braintree_Subscription::update($id, $braintree_data);

				if ($result->success) {
					$bt_subscription = $this->getSubscription($id);

					if ($bt_subscription->_attributes['price'] != $braintree_data['price']) {
						echo 'here'; exit;
						$result = Braintree_Subscription::update($id, $braintree_data);
					}

					if ($result->success) {
						return true;
					}
				}

				$this->resultError($result);
			}
			catch (Braintree_Exception $e) {
				$this->error[] = $e->getMessage();
			}
		}

		$this->error_log->write('System_Extension_Payment_Braintree::updateSubscription(): ' . implode('; ', $this->error));

		return false;
	}

	public function cancelSubscription($customer_subscription_id)
	{
		if (!$this->initAPI()) return;

		$id = $this->subscription->getMeta($customer_subscription_id, 'braintree_subscription_id');

		if ($id) {
			$result = Braintree_Subscription::cancel($id);

			if ($result->success) {
				return true;
			}


			$this->resultError($result);
		}

		return false;
	}

	*/

	public function validateSubscription(&$subscription_data)
	{
		$this->language->system('extension/payment/braintree');

		if (empty($_POST['payment_key']) || !$this->hasCard($_POST['payment_key'])) {
			$this->error['payment_key'] = $this->_('error_payment_key');
		} else {
			$subscription_data['payment_key'] = $_POST['payment_key'];
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
			$this->error[] = $this->_('error_transaction_failed');
		}

		$this->error_log->write('Braintree Result Error: ' . implode('; ', $this->error) . get_caller(0,2));
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
		if (!$this->bt_customer) {
			$braintree_id = $this->customer->getMeta('braintree_id');

			if ($braintree_id) {
				//Load BrainTree API
				if (!$this->initAPI()) {
					return;
				}

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
