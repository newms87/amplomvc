<?php
class Catalog_Controller_Block_Checkout_Confirm extends Controller
{
	public function build()
	{

	}

	public function check_order_status()
	{
		$json = array();

		if (isset($_GET['order_id'])) {
			$order = $this->order->get($_GET['order_id']);

			if ($order['confirmed']) {
				$json = array(
					'status'   => $this->order->getOrderStatus($order['order_status_id']),
					'redirect' => site_url('checkout/success'),
				);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
