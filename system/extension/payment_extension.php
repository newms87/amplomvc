<?php
abstract class PaymentExtension extends System_Extension_Extension
{
	//TODO: This is a hack to easily allow generating the template here. Consider moving this to a controller.
	protected $template;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->template = new Template($registry);
	}

	public function renderTemplate()
	{
		//Template Data
		$this->data['confirm'] = $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());

		//The Template
		$this->template->load('payment/payment');

		//Render
		$this->template->render();
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

//TODO: Consider changing Card to paymentKey. This is more generic, and allows for future deployment like bitcoins etc...
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

	public function charge($transaction)
	{
		echo "Please implement " . __METHOD__ . " to extend PaymentCard!";
		exit;
	}

	public function cardSelect($select_id = '', $remove = false) { }

	public function register_card() { }
}

//TODO: Do we really need these? Maybe need hooks somehow for these actions.
abstract class PaymentSubscriptionExtension extends PaymentCardExtension
{
	public function addSubscription($subscription) { return true; }

	public function updateSubscription($customer_subscription_id, $data) { return true; }

	public function updateSubscriptionStatus($customer_subscription_id, $status) { return true; }
}
