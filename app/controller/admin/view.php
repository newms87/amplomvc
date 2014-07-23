<?php

class App_Controller_Admin_View extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Views"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Views"), site_url('admin/view'));

		//Batch Actions
		$actions = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'path'    => site_url('admin/view/batch_action'),
		);

		//Action Buttons
		$data['insert'] = site_url('admin/view/form');

		//Render
		output($this->render('view/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$requested_cols = _request('columns');

		$columns = $this->Model_View->getColumns($requested_cols);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _request('filter', array());

		$view_listing_total = $this->Model_View->getTotalViewListings($filter);
		$view_listings      = $this->Model_View->getViewListings($sort + $filter);

		foreach ($view_listings as &$view_listing) {
			$actions = array();

			if (user_can('modify', 'view')) {
				$actions['edit'] = array(
					'text' => _l("Edit"),
					'href' => site_url('admin/view/form', 'view_listing_id=' . $view_listing['view_listing_id'])
				);

				$actions['delete'] = array(
					'text' => _l("Remove"),
					'href' => site_url('admin/view/delete', 'view_listing_id=' . $view_listing['view_listing_id']),
				);
			}

			$view_listing['actions'] = $actions;
		}
		unset($view_listing);

		$listing = array(
			'row_id'         => 'view_listing_id',
			'extra_cols'     => $this->Model_View->getColumns(false),
			'columns'        => $columns,
			'rows'           => $view_listings,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $view_listing_total,
			'listing_path'   => 'admin/view/listing',
			'save_path'      => 'admin/view/save',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if (IS_AJAX) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("View Form"));

		//Insert or Update
		$view_listing_id = _get('view_listing_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Views"), site_url('admin/view'));
		breadcrumb($view_listing_id ? _l("Edit") : _l("Add"), site_url('admin/view/form', 'view_listing_id=' . $view_listing_id));

		//Load Information from POST or DB
		$view_listing = $_POST;

		if ($view_listing_id) {
			$view_listing += $this->Model_View->getViewListing($view_listing_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'name'            => '',
			'slug'            => '',
			'path'            => '',
			'query'           => '',
			'sql'             => '',
		);

		$view_listing += $defaults;

		//Action Buttons
		$view_listing['save'] = site_url('admin/view/save', 'view_listing_id=' . $view_listing_id);

		//Render
		output($this->render('view/form', $view_listing));
	}

	public function save()
	{
		$view_listing_id = _get('view_listing_id');

		if ($this->Model_View->saveViewListing($view_listing_id, $_POST)) {
			message('success', _l("The View has been saved"));
		} else {
			message('error', $this->Model_View->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/view');
		}
	}

	public function delete()
	{
		if ($this->Model_View->removeViewListing(_get('view_listing_id'))) {
			message('notify', _l("The View was removed."));
		} else {
			message('error', $this->Model_View->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/view');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $view_listing_id) {
			switch ($_POST['action']) {
				case 'delete':
					$this->Model_View->removeViewListing($view_listing_id);
					break;
			}
		}

		if ($this->Model_View->hasError()) {
			message('error', $this->Model_View->getError());
		} else {
			message('success', _l("The Views have been updated!"));
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/view');
		}
	}
}