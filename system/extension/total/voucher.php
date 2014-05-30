<?php
class System_Extension_Total_Voucher extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->cart->hasVouchers()) {
			$vouchers = $this->cart->getVouchers();

			if ($vouchers) {
				foreach ($vouchers as $voucher) {
					if ($voucher['remaining'] > $total) {
						$amount = $total;
					} else {
						$amount = $voucher['amount'];
					}
				}


				$total_data[$voucher['code']] = array(
					'method_id' => $vouchers['voucher_id'],
					'title'     => _l("Voucher (%s)", $this->session->get('voucher')),
					'amount'    => -$amount,
				);

				$total -= $amount;
			}
		}
	}

	public function confirm($order_info, $order_total)
	{
		if (empty($order_total['method_id'])) {
			trigger_error("Voucher Confirmation Error: The voucher ID was not found for order_id $order_info[order_id].");
			return;
		}

		$voucher_info = $this->Model_Sale_Voucher->getVoucher($order_total['method_id']);

		if ($voucher_info) {
			$this->System_Model_Sale_Voucher->redeem($voucher_info['voucher_id'], $order_info['order_id'], $order_total['amount']);
		}
	}
}
