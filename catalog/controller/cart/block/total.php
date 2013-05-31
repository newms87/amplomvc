<?php
class Catalog_Controller_Cart_Block_Total extends Controller
{
	
	public function index($settings = null)
	{
		$this->template->load('cart/block/total');
		
		$this->language->load('cart/block/total');
		
		if ($this->cart->hasProducts()) {
			$totals = $this->cart->getTotals();
			
			$this->data['totals'] = $totals['data'];
			
			$this->response->setOutput($this->render());
		}
		else {
			$this->response->setOutput('');
		}
	}
}