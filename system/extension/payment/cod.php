<?php
class System_Extension_Payment_Cod extends PaymentExtension
{
	public function confirm()
	{
		if (isset($_GET['order_id'])) {
			$this->order->update($_GET['order_id'], $this->settings['complete_order_status_id']);
		}
	}
}
