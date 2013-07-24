<?php
class Admin_Controller_Report_AffiliateCommission extends Controller
{
	public function index()
	{
		$this->template->load('report/affiliate_commission');

		$this->language->load('report/affiliate_commission');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['filter_date_start'])) {
			$filter_date_start = $_GET['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($_GET['filter_date_end'])) {
			$filter_date_end = $_GET['filter_date_end'];
		} else {
			$filter_date_end = '';
		}
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';
	
		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}
		
		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}
				
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
						
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/affiliate_commission', $url));

		$this->data['affiliates'] = array();
		
		$data = array(
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'				=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'				=> $this->config->get('config_admin_limit')
		);
		
		$affiliate_total = $this->Model_Report_Affiliate->getTotalCommission($data);
		
		$results = $this->Model_Report_Affiliate->getCommission($data);
		
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/affiliate/update', 'affiliate_id=' . $result['affiliate_id'] . $url)
			);
						
			$this->data['affiliates'][] = array(
				'affiliate'  => $result['affiliate'],
				'email'		=> $result['email'],
				'status'	=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'commission' => $this->currency->format($result['commission'], $this->config->get('config_currency')),
				'orders'	=> $result['orders'],
				'total'		=> $this->currency->format($result['total'], $this->config->get('config_currency')),
				'action'	=> $action
			);
		}
					
		$url = '';
						
		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}
		
		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}
		
		$this->pagination->init();
		$this->pagination->total = $affiliate_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
}