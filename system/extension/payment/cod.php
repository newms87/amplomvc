<?php
class System_Extension_Payment_Cod extends Payment
{
	public function confirm($order_id)
	{
		$this->order->update($order_id, $this->settings['order_status_id']);
	}
}
