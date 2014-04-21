<?php
class Catalog_Controller_Block_Product_Related extends Controller
{

	public function index($settings)
	{
		$product_id = !empty($settings['product_id']) ? $settings['product_id'] : null;

		if (!$product_id) {
			return;
		}

		//Find the related products
		$related_products = $this->Model_Catalog_Product->getProductRelated($product_id);

		foreach ($related_products as &$product) {
			if ($product['image']) {
				$product['image'] = $this->image->resize($product['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
			} else {
				$product['image'] = false;
			}

			if (($this->config->get('config_customer_hide_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_hide_price')) {
				if ($this->config->get('config_show_price_with_tax')) {
					$product['price'] = $this->tax->calculate($product['price'], $product['tax_class_id']);
				}
				$product['price'] = $this->currency->format($product['price']);
			} else {
				$product['price'] = false;
			}

			if ((float)$product['special']) {
				$product['special'] = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id']));
			} else {
				$product['special'] = false;
			}

			if ($this->config->get('config_review_status')) {
				$product['rating'] = (int)$product['rating'];
			} else {
				$product['rating'] = false;
			}

			$product['reviews'] = _l("There are %d review for this product.", (int)$product['reviews']);

			$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
		}

		$data['products'] = $related_products;

		$this->response->setOutput($this->render('block/product/related', $data));
	}
}
