<?php
class Catalog_Controller_Product_Block_FlashsaleCountdown extends Controller 
{

	public function index($settings, $flashsale)
	{
		
		$this->language->load('product/block/flashsale_countdown');
		
		$this->template->load('product/block/flashsale_countdown');
		
		//This products flashsale
		if (!is_array($flashsale)) {
			$flashsale = $this->Model_Catalog_Flashsale->getFlashsale((int)$flashsale);
		}
		
		$this->data['flashsale_id'] = $flashsale['flashsale_id'];
		
		$this->data['flashsale_image'] = $this->image->get('data/clock.png');
		
		$this->data['flashsale_link'] = $this->url->link('sales/flashsale','flashsale_id='.$this->data['flashsale_id']);
		
		$this->response->setOutput($this->render());
	}
	
}