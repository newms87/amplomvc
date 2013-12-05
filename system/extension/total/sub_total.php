<?php
class System_Extension_Total_SubTotal extends TotalExtension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
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

		$data = array(
			'value' => $sub_total,
		);

		return $data;
	}
}
