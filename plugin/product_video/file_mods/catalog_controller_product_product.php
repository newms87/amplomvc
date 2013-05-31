//=====
<?php
class ControllerProductProduct extends Controller 
{
	
	public function index()
	{
//-----
//>>>>> {before} {php}
		if ($product_info['template'] == 'product_video') {
			$this->language->plugin('product_video', 'product_video');
			
			$this->data['product_video'] = html_entity_decode($product_info['blurb']);
			
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturer_url'] = $this->url->link('designers/designers', 'designer_id=' . $product_info['manufacturer_id']);
			
			if ($this->config->get('config_share_status')) {
				$this->data['block_sharing'] = $this->getBlock('extras', 'sharing');
			}
		}
		else {
//-----
//=====
		//Product Flashsale
		if (isset($flashsale_info) && $flashsale_info) {
			$this->data['block_product_flashsale_countdown'] = $this->getBlock('product', 'flashsale_countdown', array($flashsale_info));
		}
//-----
//>>>>> {before} {php}
		}
//-----
//=====
		//The Tags associated with this product
		$tags = $this->Model_Catalog_Product->getProductTags($product_info['product_id']);
//-----
//=====
	}
//.....
}
//-----