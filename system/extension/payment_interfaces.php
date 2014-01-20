<?php

//TODO: Consider changing Card to paymentKey. This is more generic, and allows for future deployment like bitcoins etc...
interface PaymentCardInterface
{
	public function hasCard($id);

	public function getCard($id);

	public function getCards();

	public function addCard($card = array());

	public function updateCard($id = null, $data);

	public function removeCard($card_id);

	public function charge($transaction);
}

//TODO: Do we really need these? Maybe need hooks somehow for these actions.
interface PaymentSubscriptionInterface
{
	public function addSubscription($subscription);

	public function updateSubscription($customer_subscription_id, $data);

	public function updateSubscriptionStatus($customer_subscription_id, $status);
}
