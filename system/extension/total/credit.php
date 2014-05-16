<?php
class System_Extension_Total_Credit extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if (option('credit_status')) {
			$balance = $this->customer->getBalance();

			if ((float)$balance) {
				if ($balance > $total) {
					$credit = $total;
				} else {
					$credit = $balance;
				}

				if ($credit > 0) {
					$total_data['credit'] = array(
						'title' => _l("Credit"),
						'amount' => -$credit,
					);

					$total -= $credit;
				}
			}
		}
	}

	public function confirm($order_info, $order_total)
	{
		if ($order_info['customer_id']) {
			$customer_transaction = array(
				'customer_id' => $order_info['customer_id'],
				'order_id'    => $order_info['order_id'],
				'amount'      => $order_total['amount'],
				'description' => _l("Order %s", (int)$order_info['order_id']),
				'date_added'  => $this->date->now(),
			);

			$this->insert('customer_transaction', $customer_transaction);
		}
	}
}
