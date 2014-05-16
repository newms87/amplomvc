<?php
class System_Extension_Total_SubTotal extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$sub_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			$sub_total += $product['total'];
		}

		//TODO: Handle Vouchers properly sitewide!
		if ($this->session->has('vouchers') && $this->session->get('vouchers')) {
			foreach ($this->session->get('vouchers') as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}

		$total += $sub_total;

		$data = array(
			'amount' => $sub_total,
		);

		return $data;
	}
}
