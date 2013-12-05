<?php
class System_Extension_Total_Credit extends TotalExtension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->config->get('credit_status')) {
			$this->language->system('extension/total/credit');

			$balance = $this->customer->getBalance();

			if ((float)$balance) {
				if ($balance > $total) {
					$credit = $total;
				} else {
					$credit = $balance;
				}

				if ($credit > 0) {
					$total_data['credit'] = array(
						'title' => $this->_('text_credit'),
						'value' => -$credit,
					);

					$total -= $credit;
				}
			}
		}
	}

	public function confirm($order_info, $order_total)
	{
		$this->language->system('extension/total/credit');

		if ($order_info['customer_id']) {
			$customer_transaction = array(
				'customer_id' => $order_info['customer_id'],
				'order_id'    => $order_info['order_id'],
				'amount'      => $order_total['value'],
				'description' => $this->_('text_order_id', (int)$order_info['order_id']),
				'date_added'  => $this->date->now(),
			);

			$this->insert('customer_transaction', $customer_transaction);
		}
	}
}
