<?php
class Catalog_Controller_Affiliate_Tracking extends Controller
{
	public function index()
	{
		$this->template->load('affiliate/tracking');

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/tracking');
	
			$this->url->redirect($this->url->link('affiliate/login'));
		}
	
		$this->language->load('affiliate/tracking');

		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('affiliate/tracking'));

		$this->_('text_description', $this->config->get('config_name'));
		$this->data['code'] = $this->affiliate->getCode();
		
		$this->data['continue'] = $this->url->link('affiliate/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
				
		$this->response->setOutput($this->render());
  	}
	
	public function autocomplete()
	{
		$json = array();
		
		if (isset($_GET['filter_name'])) {
			$data = array(
				'filter_name' => $_GET['filter_name'],
				'start'		=> 0,
				'limit'		=> 20
			);
			
			$results = $this->Model_Catalog_Product->getProducts($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'name' => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
					'link' => str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $result['product_id'] . '&tracking=' . $this->affiliate->getCode()))
				);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}