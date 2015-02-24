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
		$sort    = $this->sort->getQueryDefaults('history_id', 'desc');
		$filter  = (array)_get('filter');
		$columns = $this->Model_History->getColumns((array)_request('columns'));

		list($entries, $total) = $this->Model_History->getRecords($sort, $filter, $columns, true);

		$listing = array(
			'row_id'         => 'history_id',
			'extra_cols'     => $this->Model_History->getColumns(false),
			'columns'        => $columns,
			'rows'           => $entries,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
			'listing_path'   => 'admin/history/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}
}
