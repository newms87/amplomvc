<?php
class Catalog_Controller_Module_MagazineAd extends Controller
{

	public function index($setting = null)
	{
		$this->language->load('module/magazine_ad');

		$this->template->load('module/magazine_ad');

		if (!$setting) {
			$setting  = $this->System_Model_Setting->getSetting('featured_carousel');
			$products = $setting['featured_product_list'];
		} else {
			$setting  = array();
			$products = array();
		}

		empty($setting['limit']) ? $setting['limit'] = 12 : '';

		$this->data['shop_url'] = str_replace($this->config->get('config_url'), "http://shop.bettyconfidential.com/", $this->url->link('common/home'));

		$this->data['shop_logo'] = $this->image->resize('data/BC_Shop_Logo_w-tagline_trans.png', 370, 100);

		$this->_('text_become_designer', str_replace($this->config->get('config_url'), "http://shop.bettyconfidential.com/", $this->url->link('information/are_you_a_designer')));

		$this->data['products'] = array();

		foreach ($products as $product_id => $item) {
			$product = $this->Model_Catalog_Product->getProduct($product_id);

			if ($product) {
				$product['title'] = $item['name'];

				if ($product['special'] && (int)$product['special'] < (int)$product['price']) {
					$product['retail'] = $this->currency->format($product['price'], '', '', true, 0);
					$product['price']  = $this->currency->format($product['special'], '', '', true, 0);
				} else {
					$product['price'] = $this->currency->format($product['price'], '', '', true, 0);
				}

				$product['thumb'] = $this->image->resize($item['image'], 160, 160);
				$product['href']  = str_replace($this->config->get('config_url'), "http://shop.bettyconfidential.com/", $this->url->link('product/product', 'product_id=' . $product_id));

				$this->data['products'][] = $product;
			}

			if (count($this->data['products']) >= $setting['limit']) {
				break;
			}
		}

		$this->response->setOutput($this->render());
	}
}
