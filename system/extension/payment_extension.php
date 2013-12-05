<?php
abstract class PaymentExtension extends System_Extension_Extension
{
	public function getErrors()
	{
		return $this->error;
	}

	public function renderTemplate()
	{
		//Template Data
		$this->data['confirm'] = $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());

		//The Template
		$this->template->load('payment/payment');

		//Render
		$this->render();
	}

	protected function confirmUrl()
	{
		return $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());
	}

	protected function callbackUrl($callback = 'callback', $query = '')
	{
		return $this->url->link('payment/callback', 'code=' . $this->info['code'] . '&callback=' . $callback . ($query ? '&' . $query : ''));
	}

	public function validate($address, $total)
	{
		if ((int)$this->settings['min_total'] > $total) {
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			return false;
		}

		return true;
	}
}

abstract class PaymentCardExtension extends PaymentExtension
{
	public function hasCard($id)
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function getCard($id)
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function getCards()
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function add_card($card = array())
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function update_card($id = null, $data)
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function remove_card($id = null)
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function charge($card_id, $amount, $info = array())
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function cardSelect($select_id = '', $remove = false) { }

	public function register_card() { }
}

abstract class PaymentSubscriptionExtension extends PaymentCardExtension
{
	public function getSubscription($id) { return true; }

	public function addSubscription($subscription) { return true; }

	public function updateSubscription($customer_subscription_id, $data) { return true; }

	public function cancelSubscription($customer_subscription_id) { return true; }

	public function chargeSubscription($customer_subscription)
	{
		echo "Please implement " . __METHOD__ . "(\$customer_subscription) to extend PaymentSubscription!";
		exit;
	}

	public function validateSubscription(&$subscription_data) { return true; }
}
