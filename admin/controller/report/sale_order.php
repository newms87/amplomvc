<?php
class ControllerReportSaleOrder extends Controller { 
	public function index() {
$this->template->load('report/sale_order');

	   $this->load->language('report/sale_order');

		$this->document->setTitle($this->_('heading_title'));
      
      $query_defaults = array(
            'filter_date_start'=>'',
            'filter_date_end'=>'',
            'filter_group'=>'week',
            'filter_order_status_id'=>5,
            'page'=>1,
           );
      foreach($query_defaults as $key=>$default){
         $$key = isset($_GET[$key])?$_GET[$key]:$default;
      }
      
		$url = $this->get_url();
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/sale_order'));
      
		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		$order_total = $this->model_report_sale->getTotalOrders($data);
		
		$results = $this->model_report_sale->getOrders($data);
		
		foreach ($results as $result) {
			$this->data['orders'][] = array(
				'date_start' => $this->tool->format_datetime($result['date_start'], $this->language->getInfo('date_format_short')),
				'date_end'   => $this->tool->format_datetime($result['date_end'], $this->language->getInfo('date_format_short')),
				'orders'     => $result['orders'],
				'products'   => $result['products'],
				'tax'        => $this->currency->format($result['tax'], $this->config->get('config_currency')),
				'total'      => $this->currency->format($result['total'], $this->config->get('config_currency')),
            'net'      => $this->currency->format($result['total']-$result['cost'], $this->config->get('config_currency'))
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

		$url = $this->get_url(array('filter_date_start','filter_date_end','filter_group','filter_order_status_id'));
				
		$this->pagination->init();
		$this->pagination->total = $order_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('report/sale_order', $url . '&page={page}');
			
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

   private function get_url($filters=null){
      $url = '';
      $filters = $filters?$filters:array('filter_date_start','filter_date_end','filter_group','filter_order_status_id','page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
}