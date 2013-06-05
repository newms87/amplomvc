<?php
class Catalog_Controller_Block_Cart_Total extends Controller
{
	
	public function index($settings = null)
	{
		$this->template->load('block/cart/total');
		
		$this->language->load('block/cart/total');
		
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