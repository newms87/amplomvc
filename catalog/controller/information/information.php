<?php
class Catalog_Controller_Information_Information extends Controller
{
	public function index()
	{
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));

		$information_id = isset($_GET['information_id']) ? $_GET['information_id'] : 0;

		$information_info = $this->Model_Catalog_Information->getInformation($information_id);

		//Page Not Found
		if (!$information_info) {
			redirect('common/error');
		}

		//Layout override (only if set)
		$layout_id = $this->Model_Catalog_Information->getInformationLayoutId($information_id);

		if ($layout_id) {
			$this->config->set('config_layout_id', $layout_id);
		}

		$this->document->setTitle($information_info['title']);

		$this->breadcrumb->add($information_info['title'], site_url('information/information', 'information_id=' . $information_id));

		//Page Title
		$data['page_title'] = $information_info['title'];

		$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

		$data['continue'] = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : site_url('common/home');

		$this->response->setOutput($this->render('information/information', $data));
	}

	public function info()
	{
		$information_id = isset($_GET['information_id']) ? $_GET['information_id'] : 0;

		$information_info = $this->Model_Catalog_Information->getInformation($information_id);

		if ($information_info) {
			$information_info['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data = $information_info;
		}

		$this->response->setOutput($this->render('information/information_only', $data));
	}

	public function shipping_return_policy()
	{
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

		if ($product_id) {
			$data['shipping_policy'] = $this->cart->getProductShippingPolicy($product_id);
			$data['return_policy']   = $this->cart->getProductReturnPolicy($product_id);
		}

		$this->response->setOutput($this->render('information/shipping_return_policy', $data));
	}
}
