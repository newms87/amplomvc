<?php
class Catalog_Controller_Information_Sitemap extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Site Map"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Site Map"), site_url('information/sitemap'));

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
						'href' => site_url('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}

				$level_2_data[] = array(
					'name'     => $category_2['name'],
					'children' => $level_3_data,
					'href'     => site_url('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
				);
			}

			$data['categories'][] = array(
				'name'     => $category_1['name'],
				'children' => $level_2_data,
				'href'     => site_url('product/category', 'path=' . $category_1['category_id'])
			);
		}

		$data['special']  = site_url('product/special');
		$data['account']  = site_url('account');
		$data['edit']     = site_url('account/edit');
		$data['password'] = site_url('account/password');
		$data['address']  = site_url('account/address');
		$data['history']  = site_url('account/order');
		$data['download'] = site_url('account/download');
		$data['cart']     = site_url('cart/cart');
		$data['checkout'] = site_url('checkout/checkout');
		$data['search']   = site_url('product/search');
		$data['contact']  = site_url('information/contact');

		$data['informations'] = array();

		foreach ($this->Model_Catalog_Information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => site_url('information/information', 'information_id=' . $result['information_id'])
			);
		}

		$this->response->setOutput($this->render('information/sitemap', $data));
	}
}
