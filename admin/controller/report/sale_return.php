<?php
class Admin_Controller_Report_SaleReturn extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Returns Report"));

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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Returns Report"), $this->url->link('report/sale_return', $url));

		$data['returns'] = array();

		$data = array(
			'filter_date_start'       => $filter_date_start,
			'filter_date_end'         => $filter_date_end,
			'filter_group'            => $filter_group,
			'filter_return_status_id' => $filter_return_status_id,
			'start'                   => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                   => $this->config->get('config_admin_limit')
		);

		$return_total = $this->Model_Report_Return->getTotalReturns($data);

		$results = $this->Model_Report_Return->getReturns($data);

		foreach ($results as $result) {
			$data['returns'][] = array(
				'date_start' => $this->date->format($result['date_start'], 'short'),
				'date_end'   => $this->date->format($result['date_end'], 'short'),
				'returns'    => $result['returns']
			);
		}

		$data['return_statuses'] = $this->Model_Localisation_ReturnStatus->getReturnStatuses();

		$data['groups'] = array();

		$data['groups'][] = array(
			'text'  => _l("Years"),
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => _l("Months"),
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => _l("Weeks"),
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => _l("Days"),
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
		$this->pagination->total  = $return_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start']       = $filter_date_start;
		$data['filter_date_end']         = $filter_date_end;
		$data['filter_group']            = $filter_group;
		$data['filter_return_status_id'] = $filter_return_status_id;

		$this->response->setOutput($this->render('report/sale_return', $data));
	}
}
