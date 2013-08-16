<?php
class Catalog_Controller_Block_Product_Suggestions extends Controller
{
	public function index($settings)
	{
		$product_info = !empty($settings['product_info']) ? $settings['product_info'] : null;

		if (!$product_info) {
			return;
		}

		$limit = !empty($settings['limit']) ? $settings['limit'] : null;

		//Template and Language
		$this->language->load('block/product/suggestions');
		$this->template->load('block/product/suggestions');

		$image_width  = $this->config->get('config_image_related_width');
		$image_height = $this->config->get('config_image_related_height');

		$suggestions = $this->Model_Catalog_Product->getProductSuggestions($product_info, $limit);

		foreach ($suggestions as &$product) {
			if ($product['image']) {
				$product['thumb'] = $this->image->resize($product['image'], $image_width, $image_height);
			}

			if ($this->config->get('config_show_product_list_hover_image')) {
				$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);

				if (!empty($product['images'])) {
					reset($product['images']);
					$product['backup_thumb'] = $this->image->resize(current($product['images']), $image_width, $image_height);
				}
			}

			if ($this->config->get('config_customer_hide_price') && !$this->customer->isLogged()) {
				$product['price'] = false;
			} else {
				$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']));
			}

			if ($product['special']) {
				$product['special'] = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id']));
			}

			$product['href'] = $this->url->link('product/product', 'product_id=' . (int)$product['product_id']);
		}
		unset($product);

		$this->data['products'] = $suggestions;

		$this->data['show_price_tax'] = $this->config->get('config_show_price_with_tax');

		$this->render();
	}
}
