<?php
class Catalog_Controller_Information_Sitemap extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Site Map"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Site Map"), $this->url->link('information/sitemap'));

		$data['categories'] = array();

		$categories_1 = $this->Model_Catalog_Category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->Model_Catalog_Category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->Model_Catalog_Category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'name' => $category_3['name'],
						'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}

				$level_2_data[] = array(
					'name'     => $category_2['name'],
					'children' => $level_3_data,
					'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
				);
			}

			$data['categories'][] = array(
				'name'     => $category_1['name'],
				'children' => $level_2_data,
				'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
			);
		}

		$data['special']  = $this->url->link('product/special');
		$data['account']  = $this->url->link('account/account');
		$data['edit']     = $this->url->link('account/edit');
		$data['password'] = $this->url->link('account/password');
		$data['address']  = $this->url->link('account/address');
		$data['history']  = $this->url->link('account/order');
		$data['download'] = $this->url->link('account/download');
		$data['cart']     = $this->url->link('cart/cart');
		$data['checkout'] = $this->url->link('checkout/checkout');
		$data['search']   = $this->url->link('product/search');
		$data['contact']  = $this->url->link('information/contact');

		$data['informations'] = array();

		foreach ($this->Model_Catalog_Information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
			);
		}

		$this->response->setOutput($this->render('information/sitemap', $data));
	}
}
