<?php
class App_Controller_Block_Widget_Specials extends Controller
{
	public function build($settings)
	{
		$sort_filter = array(
			'special' => array(
				'low' => 0,
			),
		);

		$sort_filter += $this->sort->getQueryDefaults('price', 'ASC');

		$product_total = $this->Model_Catalog_Product->getTotalActiveProducts($sort_filter);
		$products      = $this->Model_Catalog_Product->getActiveProducts($sort_filter);

		if (!empty($products)) {
			$params = array(
				'data'     => $products,
				'template' => 'block/product/product_list',
			);

			$data['block_product_list'] = $this->block->render('product/list', null, $params);

			//Sort
			$sorts = array(
				'sort=p.name&order=ASC'  => _l("Name (A - Z)"),
				'sort=p.name&order=DESC' => _l("Name (Z - A)"),
				'sort=price&order=ASC'   => _l("Price (Low &gt; High)"),
				'sort=price&order=DESC'  => _l("Price (High &gt; Low)"),
			);

			$data['sorts'] = $this->sort->render_sort($sorts);

			$data['limits'] = $this->sort->renderLimits();

			$this->pagination->init();
			$this->pagination->total = $product_total;

			$data['pagination'] = $this->pagination->render();
		} else {
			$data['continue'] = site_url('common/home');
		}

		$this->render('block/widget/specials', $data);
	}
}
