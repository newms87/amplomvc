<?php

class App_Controller_Admin_History extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("DB History"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("History"), site_url('admin/history'));

		//Listing
		$data['listing'] = $this->listing();

		//Response
		output($this->render('history', $data));
	}

	public function listing()
	{
		$sort    = (array)_get('sort', array('history_id' => 'DESC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'history_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_History->getColumns((array)_request('columns')),
		);


		list($entries, $total) = $this->Model_History->getRecords($sort, $filter, $options, true);

		$listing = array(
			'extra_cols'     => $this->Model_History->getColumns(false),
			'records'        => $entries,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
			'listing_path'   => 'admin/history/listing',
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}
}
