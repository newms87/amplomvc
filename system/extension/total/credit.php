<?php
class System_Extension_Total_Credit extends Extension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->config->get('credit_status')) {
			$_ = $this->language->system_fetch('extension/total/credit');

			$balance = $this->customer->getBalance();

			if ((float)$balance) {
				if ($balance > $total) {
					$credit = $total;
				} else {
					$credit = $balance;
				}

				if ($credit > 0) {
					$total_data[] = array(
						'code'		=> 'credit',
						'title'		=> $_['text_credit'],
						'value'		=> -$credit,
						'sort_order' => $this->config->get('credit_sort_order')
					);

					$total -= $credit;
				}
			}
		}
	}

	public function confirm($order_info, $order_total)
	{
		$_ = $this->language->system_fetch('extension/total/credit');

		if ($order_info['customer_id']) {
			$customer_transaction = array(
				'customer_id' => $order_info['customer_id'],
				'order_id' => $order_info['order_id'],
				'amount' => $order_total['value'],
				'description' => sprintf($_['text_order_id'], (int)$order_info['order_id']),
				'date_added' => $this->date->now(),
			);

			$this->insert('customer_transaction', $customer_transaction);
		}
	}
}
