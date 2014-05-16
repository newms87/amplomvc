<?php
class Catalog_Controller_Block_Cart_Total extends Controller
{
	public function build()
	{
		$data['totals'] = $this->cart->getTotals();

		//Render
		$this->response->setOutput($this->render('block/cart/total', $data));
	}
}
