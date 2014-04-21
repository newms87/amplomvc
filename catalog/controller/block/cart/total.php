<?php
class Catalog_Controller_Block_Cart_Total extends Controller
{
	public function index()
	{
		$totals = $this->cart->getTotals();

		foreach ($totals as &$total) {
			if (!isset($total['display_value'])) {
				$total['display_value'] = $this->currency->format($total['value']);
			}
		}

		$data['totals'] = $totals;

		//Render
		$this->response->setOutput($this->render('block/cart/total', $data));
	}
}
