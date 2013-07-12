<?php
class Admin_Controller_Report_ProductViewed extends Controller 
{
	public function index()
	{
		$this->template->load('report/product_viewed');

		$this->language->load('report/product_viewed');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/product_viewed'));
							
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$product_view_list = $this->Model_Report_Product->getProductViews();
		
		$product_views = array();
		foreach ($product_view_list as $pv) {
			if (isset($product_views[$pv['product_id']])) {
				$id = &$product_views[$pv['product_id']];
				$unique = false;
				if (($pv['user_id'] == 0 || !in_array($pv['user_id'],$id['users'])) && !in_array($pv['session_id'],$id['sessions'])) {
					if($pv['user_id'] != 0)
						$id['users'][] = $pv['user_id'];
					$id['sessions'][] = $pv['session_id'];
					$id['user_total'] += 1;
					$unique = true;
				}
				if (!in_array($pv['ip_address'],$id['ip_addr'])) {
					$id['ip_addr'][] = $pv['ip_address'];
					$id['ip_total'] += 1;
					if($unique)
						$id['ip_user_total'] += 1;
				}
			}
			else {
				$product_views[$pv['product_id']] = array('user_total'=>1,'users'=>array($pv['user_id']),'sessions'=>array($pv['session_id']),
																		'ip_total'=>1,'ip_addr'=>array($pv['ip_address']),
																		'ip_user_total'=>1
																		);
			}
		}
		
		$product_viewed_total = $this->Model_Report_Product->getTotalProductsViewed($data);
		
		$product_views_total = $this->Model_Report_Product->getTotalProductViews();
		
		$this->data['products'] = array();
		
		$results = $this->Model_Report_Product->getProductsViewed($data);
		
		foreach ($results as $result) {
			if ($result['views']) {
				$percent = round($result['views'] / $product_views_total * 100, 2);
			} else {
				$percent = 0;
			}
			
			$this->data['products'][] = array(
				'name'	=> $result['name'],
				'model'	=> $result['model'],
				'viewed'  => $result['views'],
				'ip_total'=> $product_views[$result['product_id']]['ip_total'],
				'user_total'=> $product_views[$result['product_id']]['user_total'],
				'ip_user_total'=> $product_views[$result['product_id']]['ip_user_total'],
				'percent' => $percent . '%'
			);
		}
 		
		$url = $this->get_url();
				
		$this->data['reset'] = $this->url->link('report/product_viewed/reset', $url);
						
		$this->pagination->init();
		$this->pagination->total = $product_viewed_total;
		$this->data['pagination'] = $this->pagination->render();
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function reset()
	{
		$this->language->load('report/product_viewed');
		
		$this->Model_Report_Product->reset();
		
		$this->message->add('success', $this->_('text_success'));
		
		$this->url->redirect($this->url->link('report/product_viewed'));
	}
	
	private function get_url($filters=null)
	{
		$url = '';
		$filters = $filters?$filters:array('page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}