<?php
class Catalog_Controller_Module_FeaturedFlashsale extends Controller
{
	protected function index($setting)
	{
		$this->language->load('module/featured_flashsale');

		empty($setting['limit']) ? $setting['limit'] = 3 : '';

		$filter     = 'date_start < NOW() AND date_end > NOW()';
		$sort       = 'date_end ASC';
		$flashsales = $this->Model_Catalog_Flashsale->getFlashsales($filter, $sort, $setting['limit']);

		$flashsales = is_array($flashsales) ? $flashsales : array();

		$items = array();
		if (count($flashsales) < $setting['limit']) {
			$dl = $setting['limit'] - count($flashsales);

			$featured_list = $this->config->get('featured_list');

			foreach ($featured_list as $id => $name) {
				if (substr($id, 0, 7) == "product") {
					$item = $this->Model_Catalog_Product->getProduct((int)substr($id, 7));
					if (!empty($item)) {
						$items[$id]         = $item;
						$items[$id]['href'] = $this->url->link('product/product', 'product_id=' . $items[$id]['product_id']);
					}
				} elseif (substr($id, 0, 8) == "designer") {
					$item = $this->Model_Catalog_Manufacturer->getManufacturerAndTeaser((int)substr($id, 8));
					if (!empty($item)) {
						$items[$id] = $item;
					}
				}
			}
			$items = array_slice($items, 0, $dl);
		}

		$blocks = array_merge($flashsales, $items);

		$size = $setting['size'];

		foreach ($blocks as &$block) {
			$block['image'] = $this->image->resize((isset($block['image']) ? $block['image'] : "no_image.png"), (int)(.754 * $size), (int)(.754 * $size));

			if (!isset($block['href'])) {
				$block['href'] = isset($block['keyword']) ? $this->url->site($block['keyword']) : '';
			}

			$block['teaser'] = isset($block['teaser']) ? $block['teaser'] : '';
		}

		$this->data['blocks'] = $blocks;

		$this->data['fs_tac'] = $this->image->resize('data/pink_tac.png', .138 * $size, .238 * $size);

		if ($setting['style'] == 'large') {
			$this->template->load('module/featured_flashsale_large');

			$this->data['fs_bg_image'] = $this->image->get('data/polaroids-blank.png');
		} else {
			$this->template->load('module/featured_flashsale');

			$this->data['polaroid'] = $this->image->resize('data/polaroid-1.png', $size, $size * 1.088);
		}

		$this->render();
	}
}