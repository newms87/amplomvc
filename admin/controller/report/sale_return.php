<?php
class ControllerReportSaleReturn extends Controller {
	public function index() {     
		$this->template->load('report/sale_return');

		$this->load->language('report/sale_return');

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
		
		if (isset($_GET['filter_return_status_id'])) {
			$filter_return_status_id = $_GET['filter_return_status_id'];
		} else {
			$filter_return_status_id = 0;
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

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
				
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
						
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/sale_return', $url));

		$this->data['returns'] = array();
		
		$data = array(
			'filter_date_start'	      => $filter_date_start, 
			'filter_date_end'	      => $filter_date_end, 
			'filter_group'            => $filter_group,
			'filter_return_status_id' => $filter_return_status_id,
			'start'                   => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                   => $this->config->get('config_admin_limit')
		);
		
		$return_total = $this->model_report_return->getTotalReturns($data);
		
		$results = $this->model_report_return->getReturns($data);
		
		foreach ($results as $result) {
			$this->data['returns'][] = array(
				'date_start' => $this->tool->format_datetime($result['date_start'], $this->language->getInfo('date_format_short')),
				'date_end'   => $this->tool->format_datetime($result['date_end'], $this->language->getInfo('date_format_short')),
				'returns'    => $result['returns']
			);
		}
				 
		$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();

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

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
				
		$this->pagination->init();
		$this->pagination->total = $return_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('report/sale_return', $url . '&page={page}');
			
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
		$this->data['filter_return_status_id'] = $filter_return_status_id;
				 
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
}