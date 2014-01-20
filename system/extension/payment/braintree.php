<?php
class System_Extension_Payment_Braintree extends System_Extension_Payment
{
	private $cards;
	private $bt_customer;
	private $init = false;

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
			$this->error = _l("There was a problem while processing your transaction. Please choose a different Payment Method, or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
			$this->error_log->write(__METHOD__ . ": Failed to load Braintree API.");
			return false;
		}

		$order = $this->order->get($_GET['order_id']);

		if (empty($order)) {
			$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->get('config_contact_page_id'));
			$this->error = _l("We were unable to process your order. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
			$this->error_log->write(__METHOD__ . ": Failed to lookup order ID: $_GET[order_id]. Unable to confirm checkout payment.");
			return false;
		}

		//Pay with Existing Credit Card
		if (!empty($_POST['existing_payment_card'])) {
			if (!$this->customer->isLogged()) {
				$this->error = _l("You must be logged in to use a card from your account! Please try checking out again");
				return false;
			}

			$this->loadCustomer();

			$sale = array(
				'amount'             => $order['total'],
				'customerId'         => $this->bt_customer->id,
				'paymentMethodToken' => $_POST['payment_key'],
			);

			$this->customer->setMeta('default_payment_key', $_POST['payment_key']);
		} else {
			//New Credit Card
			if (!$this->validateCard($_POST)) {
				return false;
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

		try {
			$result = Braintree_Transaction::sale($sale);
		} catch (Braintree_Exception $e) {
			$this->error = $e->getMessage();
			return false;
		}

		if (!$result->success) {
			$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->load('config', 'config_contact_page_id'));
			$this->error = _l("There was a problem processing your transaction. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
			$this->error_log->write(__METHOD__ . ": Braintree_Transaction:sale() failed for order ID $_GET[order_id]. Unable to confirm checkout payment.");
			return false;
		}

		$this->order->updateOrder($_GET['order_id'], $this->settings['complete_order_status_id'], _l("Order Completed via Braintree Payments"), true);

		return true;
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
							$this->removeCard($card2->token);
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

	public function addCard($card = array())
	{
		if (!$this->initAPI()) {
			return false;
		}

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

				try {
					$result = Braintree_CreditCard::create($data);

					if (!empty($result->errors)) {
						$this->resultError($result);
					}
				} catch (Braintree_Exception $e) {
					$this->error[] = $e->getMessage();
				}
			}
		}

		return $this->error ? false : true;
	}

	public function updateCard($id = null, $data)
	{
		if (!$this->initAPI()) {
			return false;
		}

		if (!empty($data['default'])) {
			unset($data['default']);
			$data['options'] = array(
				'makeDefault' => true,
			);
		}

		try {
			Braintree_CreditCard::update($id, $data);
		} catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return $this->error ? false : true;
	}

	public function removeCard($card_id)
	{
		if (!$this->initAPI()) {
			return false;
		}

		try {
			Braintree_CreditCard::delete($card_id);
		} catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return $this->error ? false : true;
	}

	public function getSubscription($id)
	{
		if (!$this->initAPI()) {
			return;
		}

		try {
			return Braintree_Subscription::find($id);
		} catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return false;
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
		} catch (Exception $e) {
			$this->error_log->write($e);
			$error_log   = $this->url->admin('tool/logs', 'log=error');
			$this->error = _l("There was a problem while communicating with Braintree. See more details in the <a target=\"_blank\" href=\"%s\">Error Log.</a>", $error_log);
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
			return false;
		}

		if (!$this->bt_customer) {
			//Load BrainTree API
			if (!$this->initAPI()) {
				return false;
			}

			$braintree_id = $this->customer->getMeta('braintree_id');

			if ($braintree_id) {
				try {
					$this->bt_customer = Braintree_Customer::find($braintree_id);
				} catch (Braintree_Exception $e) {
					$this->error[] = _l("Your Customer information was not found. Please try <a href=\"%s\">registering a credit card</a>", $this->url->link('extension/payment/braintree/register_card'));
					$this->error_log->write(__METHOD__ . _l("(): The customer with ID %s was not found!", $braintree_id));
					return false;
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

				try {
					$result = Braintree_Customer::create($data);

					if ($result->success) {
						//If new customer created, save customer ID for braintree (NOTE: $result->customer->id returned only for new customers)
						if (!empty($result->customer) && !empty($result->customer->id)) {
							$this->customer->setMeta('braintree_id', $result->customer->id);
						}

						return true;

					} elseif (!empty($result->errors)) {
						$this->resultError($result);
					}
				} catch (Braintree_Exception $e) {
					$this->error[] = $e->getMessage();
					return false;
				}
			}
		}
	}
}
