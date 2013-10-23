<?php
abstract class Payment extends Extension
{
	public function renderTemplate()
	{
		$this->template->load('payment/payment');

		$this->data['confirm'] = $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());

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

abstract class PaymentCard extends Payment
{
	public function hasCard($id)
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function getCard($id)
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function getCards()
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function add_card($card = array())
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function update_card($id = null, $data)
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function remove_card($id = null)
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function charge($card_id, $amount, $info = array())
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . " to extend PaymentCard!";
		exit;
	}

	public function cardSelect($select_id = '', $remove = false) { }

	public function register_card() { }
}

abstract class PaymentSubscription extends PaymentCard
{
	public function isDue($customer_subscription)
	{
		$subscription = $customer_subscription['subscription'];

		//Limit Number of Charges from date_added
		if ($subscription['cycles']) {
			$diff = $this->date->diff($customer_subscription['date_activated']);
			$total_period = $subscription['cycles'] * $subscription['time'];

			switch ($subscription['time_unit']) {
				//Charge every x weeks until end of period
				case 'W':
					if (($diff['d'] > $total_period) || ($diff['d'] % $subscription['time'] !== 0)) {
						return false;
					}
					break;

				//Charge every x months until end of period
				case 'M':
					if ($diff['m'] > $total_period || ($diff['m'] % $subscription['time'] !== 0)) {
						return false;
					}
					break;

				//Charge every x years until end of period
				case 'Y':
					if ($diff['y'] > $total_period || ($diff['y'] % $subscription['time'] !== 0)) {
						return false;
					}
					break;
			}
		}

		//Charge Only on a specific day
		if ($subscription['day']) {
			switch ($subscription['time_unit']) {
				//Charge on day of week
				case 'W':
					if ($this->date->getDayOfWeek() !== $subscription['day']) {
						return false;
					}
					break;

				//Charge on day of month
				case 'M':
					if ($this->date->getDayOfMonth() !== $subscription['day']) {
						return false;
					}
					break;

				//Charge on day of year
				case 'Y':
					if ($this->date->getDayOfYear() !== $subscription['day']) {
						return false;
					}
					break;
			}
		}

		return true;
	}

	public function getSubscription($id) { return true; }

	public function addSubscription($subscription) { return true; }

	public function updateSubscription($customer_subscription_id, $data) { return true; }

	public function cancelSubscription($customer_subscription_id) { return true; }

	public function chargeSubscription($customer_subscription)
	{
		echo "Please implement " . get_called_class() . '::' . __FUNCTION__ . "(\$customer_subscription) to extend PaymentSubscription!";
		exit;
	}

	public function validateSubscription(&$subscription_data) { return true; }
}
