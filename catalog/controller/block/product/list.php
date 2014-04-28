<?php
class Catalog_Controller_Block_Product_List extends Controller
{
	public function build($settings)
	{
		$process_data = isset($settings['process_data']) ? $settings['process_data'] : true;

		//TODO: need to implement these options in admin panel!
		$data = array(
			'wishlist_status' => $this->config->get('config_wishlist_status'),
			'compare_status'  => $this->config->get('config_compare_status'),
			'list_show_add_to_cart' => $this->config->get('config_list_show_add_to_cart'),
		   'show_price_tax' => $this->config->get('config_show_price_with_tax'),
		   'review_status' => $this->config->get('config_review_status'),
		);

		$image_width  = $this->config->get('config_image_product_width');
		$image_height = $this->config->get('config_image_product_height');

		if ($process_data) {
			foreach ($settings['data'] as &$item) {
				$item['thumb'] = $this->image->resize($item['image'], $image_width, $image_height);

				if (!$item['thumb']) {
					$item['thumb'] = $this->image->resize('no_image.png', $image_width, $image_height);
				}

				if ($this->config->get('config_show_product_list_hover_image')) {
					if (!empty($item['images'])) {
						reset($item['images']);
						$item['backup_thumb'] = $this->image->resize(current($item['images']), $image_width, $image_height);
					}
				}

				if (($this->config->get('config_customer_hide_price') ? $this->customer->isLogged() : true)) {
					if (!empty($item['price'])) {
						$item['price'] = $this->currency->format($this->tax->calculate($item['price'], $item['tax_class_id']));
					}

					if (!empty($item['special'])) {
						$item['special'] = $this->currency->format($this->tax->calculate($item['special'], $item['tax_class_id']));
					}
				} else {
					$item['price'] = false;
				}

				if ($data['show_price_tax']) {
					$item['tax'] = $this->currency->format((float)$item['special'] ? $item['special'] : $item['price']);
				}

				if ($data['review_status']) {
					$item['rating']  = (int)$item['rating'];
					$item['reviews'] = _l("Based on %s reviews.", (int)$item['reviews']);
				}

				if (!empty($item['teaser'])) {
					$item['teaser'] = $this->tool->limit_characters(html_entity_decode($item['teaser'], ENT_QUOTES, 'UTF-8'), 100);
				}

				if (empty($item['href'])) {
					$item['href'] = site_url('product/product', 'product_id=' . $item['product_id']);
				}
			}
			unset($item);
		}

		$data += array(
			'products'        => $settings['data'],
			'sort_url'        => $this->sort->get_sort_url(),
		);

		if ($data['compare_status']) {
			$data['compare_count'] = $this->cart->get_compare_count();
			$data['compare']       = site_url('product/compare');
		}

		$template = !empty($settings['template']) ? $settings['template'] : 'block/product/product_list';

		$this->render($template, $data);
	}
}
