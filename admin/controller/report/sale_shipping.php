<?php
class ControllerReportSaleShipping extends Controller {
	public function index() {     
$this->template->load('report/sale_shipping');

		$this->load->language('report/sale_shipping');

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
		
		if (isset($_GET['filter_group'])) {
			$filter_group = $_GET['filter_group'];
		} else {
			$filter_group = 'week';
		}
		
		if (isset($_GET['filter_order_status_id'])) {
			$filter_order_status_id = $_GET['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
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
		
		if (isset($_GET['filter_group'])) {
			$url .= '&filter_group=' . $_GET['filter_group'];
		}		

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
				
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
						
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/sale_shipping', $url));

		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
				
		$order_total = $this->model_report_sale->getTotalShipping($data); 
		
		$results = $this->model_report_sale->getShipping($data);
		
		foreach ($results as $result) {
			$this->data['orders'][] = array(
				'date_start' => $this->tool->format_datetime($result['date_start'], $this->language->getInfo('date_format_short')),
				'date_end'   => $this->tool->format_datetime($result['date_end'], $this->language->getInfo('date_format_short')),
				'title'      => $result['title'],
				'orders'     => $result['orders'],
				'total'      => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
		}
		 
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['groups'] = array();

		$this->data['groups'][] = array(
			'text'  => $this->_('text_year'),
			'value' => 'year',
		);

		$this->data['groups'][] = array(
			'text'  => $this->_('text_month'),
			'value' => 'month',
		);

		$this->data['groups'][] = array(
			'text'  => $this->_('text_week'),
			'value' => 'week',
		);

		$this->data['groups'][] = array(
			'text'  => $this->_('text_day'),
			'value' => 'day',
		);
		
		$url = '';
						
		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}
		
		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}
		
		if (isset($_GET['filter_group'])) {
			$url .= '&filter_group=' . $_GET['filter_group'];
		}		

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
								
		$this->pagination->init();
		$this->pagination->total = $order_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('report/sale_shipping', $url . '&page={page}');
			
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
				 
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
}