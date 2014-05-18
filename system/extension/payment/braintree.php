<?php

class System_Extension_Payment_Braintree extends System_Extension_Payment
{
	private $cards;
	private $bt_customer;
	private $init = false;

	public function charge($transaction)
	{
		if (!$this->initAPI()) {
			return false;
		}

		if (!$transaction['payment_key']) {
			$this->error = _l("Payment Method was not specified.");
			return false;
		}
		//Charge New Card without saving card to account (save customer details for reference)
		if (strpos($transaction['payment_key'], 'new_card') === 0) {
			$order_id = (int)str_replace('new_card_', '', $transaction['payment_key']);
			$order    = $this->order->get($order_id);

			//Load New Card data
			$new_card = $this->config->load('braintree', $transaction['payment_key']);

			//Remove temporary New Card data
			$this->config->remove('braintree', $transaction['payment_key'], 0);

			if (!$order) {
				$this->error = _l("Failed to load Order information. Transaction failed.");
				return false;
			}

			if (!$new_card) {
				$this->error = _l("Failed to load Payment Card data. Unable to process transaction.");
				return false;
			}

			$sale = array(
				"amount"     => $transaction['amount'],
				"creditCard" => array(
					"number"          => $new_card["number"],
					"cvv"             => $new_card["cvv"],
					"expirationMonth" => $new_card["month"],
					"expirationYear"  => $new_card["year"]
				),
				'customer'   => array(
					'firstName' => $order['firstname'],
					'lastName'  => $order['lastname'],
					'email'     => $order['email'],
					'phone'     => $order['telephone'],
					'fax'       => $order['fax'],
				),
				"options"    => array(
					"submitForSettlement" => true,
					'storeInVault'        => false,
				),
			);
		} else {
			$this->loadCustomer();

			$sale = array(
				'customerId'         => $this->bt_customer->id,
				'amount'             => $transaction['amount'],
				'paymentMethodToken' => $transaction['payment_key'],
				'recurring'          => $transaction['type'] === 'subscription',
				'options'    => array(
					'submitForSettlement' => true,
				),
			);
		}

		try {
			$result = Braintree_Transaction::sale($sale);

			if ($result->success) {
				return true;
			}

			$this->resultError($result);
		} catch (Braintree_Exception $e) {
			$this->error[] = $e->getMessage();
		}

		return false;
	}

	public function confirm($order_id, $new_card = null)
	{
		if (!$this->initAPI()) {
			$url_contact = site_url('page', 'page_id=' . option('config_contact_page_id'));
			$this->error['braintree_api'] = _l("There was a problem while processing your transaction. Please choose a different Payment Method, or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
			$this->error_log->write(__METHOD__ . ": Failed to load Braintree API.");
			return false;
		}

		$order = $this->order->get($order_id);

		if (empty($order)) {
			$url_contact = site_url('page', 'page_id=' . option('config_contact_page_id'));
			$this->error['order_id'] = _l("We were unable to process your order. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
			$this->error_log->write(__METHOD__ . ": Failed to lookup order ID: $order_id. Unable to confirm checkout payment.");
			return false;
		}

		//New Credit Card
		if ($new_card) {
			if (!$this->validateCard($new_card)) {
				$this->error['new_card'] = _l("The Credit Card Information was invalid");
				return false;
			}

			if (!empty($new_card['save_to_account']) && $this->customer->isLogged()) {
				$this->loadCustomer();

				$credit_card = array(
					'customerId'      => $this->bt_customer->id,
					'number'          => $new_card["number"],
					'cvv'             => $new_card["cvv"],
					'expirationMonth' => $new_card["month"],
					'expirationYear'  => $new_card["year"]
				);

				try {
					$result = Braintree_CreditCard::create($credit_card);
				} catch (Braintree_Exception $e) {
					$this->error['braintree_error'] = $e->getMessage();
					return false;
				}

				if ($result->success) {
					//Update the payment key for the transaction to the new card
					if (!empty($result->creditCard->_attributes['token'])) {
						$this->order->setPaymentMethod($order_id, 'braintree', $result->creditCard->_attributes['token']);
					}
				} else {
					$url_contact = site_url('page', 'page_id=' . $this->config->load('config', 'config_contact_page_id'));
					$this->error['braintree_result'] = _l("There was a problem processing your transaction. Please try again or <a href=\"%s\">contact us</a> to complete your order.", $url_contact);
					$this->error_log->write(__METHOD__ . ": Braintree_CreditCard:create() failed for order ID $order_id. Unable to confirm checkout payment method.");
					return false;
				}

			} else {
				//Save the new card information temporarily to be processed after transaction is confirmed (in $this->charge())
				$this->config->save('braintree', 'new_card_' . $order_id, $new_card);
				$this->order->setPaymentMethod($order_id, 'braintree', 'new_card_' . $order_id);
			}
		}

		$complete_status_id = $this->settings['complete_order_status_id'] ? $this->settings['complete_order_status_id'] : option("config_order_complete_status_id");

		if (!$this->order->updateOrder($order_id, $complete_status_id, _l("Order Completed via Braintree Payments"), true)) {
			$this->error = $this->order->getError();
			return false;
		}

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
					'options'         => array(
						'verifyCard' => true,
					),
				);

				if (!empty($card['name'])) {
					$data['cardholderName']  = $card['name'];
				} elseif (!empty($card['lastname'])) {
					$data['cardholderName']  = $card['firstname'] . ' ' . $card['lastname'];
				}

				try {
					$result = Braintree_CreditCard::create($data);

					if (!empty($result->success)) {
						return $result->creditCard->_attributes['token'];
					} elseif (!empty($result->errors)) {
						$this->resultError($result);
					} else {
						$this->error[] = _l("There was a problem creating the credit card");
					}
				} catch (Braintree_Exception $e) {
					$this->error[] = $e->getMessage();
				}
			}
		}

		return empty($this->error);
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

		return empty($this->error);
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

		return empty($this->error);
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

		return empty($this->error);
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
					$this->error[] = _l("Your Customer information was not found. Please try <a href=\"%s\">registering a credit card</a>", site_url('extension/payment/braintree/register_card'));
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
