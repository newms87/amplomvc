<?php
class System_Extension_Total_SubTotal extends Extension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$_ = $this->language->system_fetch('extension/total/sub_total');

		$sub_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			$sub_total += $product['total'];
		}

		//TODO: Handle Vouchers properly sitewide!
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}

		$total += $sub_total;

		//Add Total Data (must use code as Index!)
		$total_data['sub_total'] = array(
			'title'      => $_['text_sub_total'],
			'value'      => $sub_total,
		);
	}
}
