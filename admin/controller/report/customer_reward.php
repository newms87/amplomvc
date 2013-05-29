<?php
class ControllerReportCustomerReward extends Controller 
{
	public function index()
	{
		$this->template->load('report/customer_reward');

		$this->load->language('report/customer_reward');

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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/customer_reward', $url));

		$this->data['customers'] = array();
		
		$data = array(
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'				=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'				=> $this->config->get('config_admin_limit')
		);
				
		$customer_total = $this->model_report_customer->getTotalRewardPoints($data);
		
		$results = $this->model_report_customer->getRewardPoints($data);
		
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/customer/update', 'customer_id=' . $result['customer_id'] . $url)
			);
						
			$this->data['customers'][] = array(
				'customer'		=> $result['customer'],
				'email'			=> $result['email'],
				'customer_group' => $result['customer_group'],
				'status'			=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'points'			=> $result['points'],
				'orders'			=> $result['orders'],
				'total'			=> $this->currency->format($result['total'], $this->config->get('config_currency')),
				'action'			=> $action
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
		$this->pagination->total = $customer_total;
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