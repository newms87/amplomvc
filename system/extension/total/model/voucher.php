<?php
class System_Extension_Total_Model_Voucher extends Model 
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->cart->hasVoucher()) {
			$this->language->load('total/voucher');
			
			$vouchers = $this->cart->getVouchers();
			
			if ($voucher_info) {
				if ($voucher_info['amount'] > $total) {
					$amount = $total;
				} else {
					$amount = $voucher_info['amount'];
				}
				
				$total_data[] = array(
					'code'		=> 'voucher',
					'method_id' => $voucher_info['voucher_id'],
					'title'		=> sprintf($this->_('text_voucher'), $this->session->data['voucher']),
					'value'		=> -$amount,
					'sort_order' => $this->config->get('voucher_sort_order')
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
		
		$voucher_info = $this->System_Model_Voucher->getVoucher($order_total['method_id']);
		
		if ($voucher_info) {
			$this->System_Model_Voucher->redeem($voucher_info['voucher_id'], $order_info['order_id'], $order_total['value']);
		}
	}
}