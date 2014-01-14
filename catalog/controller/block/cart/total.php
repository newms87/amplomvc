<?php
class Catalog_Controller_Block_Cart_Total extends Controller
{
	public function index()
	{
		$this->template->load('block/cart/total');

		$totals = $this->cart->getTotals();

		foreach ($totals as &$total) {
			if (!isset($total['text'])) {
				$total['text'] = $this->currency->format($total['value']);
			}
		}

		$this->data['totals'] = $totals;

		$this->response->setOutput($this->render());
	}
}
