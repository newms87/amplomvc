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

		$this->data['totals'] = $totals;

		//The Template
		$this->view->load('block/cart/total');

		//Render
		$this->response->setOutput($this->render());
	}
}
