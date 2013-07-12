<?php
class Catalog_Model_Total_Voucher extends Model 
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
		$code = '';
		
		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');
		
		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}
		
		$voucher_info = $this->Model_Cart_Voucher->getVoucher($code);
		
		if ($voucher_info) {
			$this->Model_Cart_Voucher->redeem($voucher_info['voucher_id'], $order_info['order_id'], $order_total['value']);
		}
	}
}