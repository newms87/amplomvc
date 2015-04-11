<?php

class App_Controller_Admin_View extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Views"));

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
			'path'    => site_url('admin/view/batch-action'),
		);

		//Action Buttons
		$data['insert'] = site_url('admin/view/form');

		//Render
		output($this->render('view/list', $data));
	}

	public function listing()
	{
		$sort    = (array)_get('sort', array('name' => 'ASC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'view_listing_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_ViewListing->getColumns((array)_request('columns')),
		);

		list($view_listings, $view_listing_total) = $this->Model_ViewListing->getRecords($sort, $filter, $options, true);

		foreach ($view_listings as $view_listing_id => &$view_listing) {
			$actions = array();

			if (user_can('w', 'admin/view')) {
				$actions['edit'] = array(
					'text' => _l("Edit"),
					'href' => site_url('admin/view/form', 'view_listing_id=' . $view_listing_id)
				);

				$actions['delete'] = array(
					'text' => _l("Remove"),
					'href' => site_url('admin/view/delete', 'view_listing_id=' . $view_listing_id),
				);
			}

			$view_listing['actions'] = $actions;
		}
		unset($view_listing);

		$listing = array(
			'extra_cols'     => $this->Model_ViewListing->getColumns(false),
			'records'        => $view_listings,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $view_listing_total,
			'listing_path'   => 'admin/view/listing',
			'save_path'      => 'admin/view/save',
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("View Form"));

		//Insert or Update
		$view_listing_id = _get('view_listing_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Views"), site_url('admin/view'));
		breadcrumb($view_listing_id ? _l("Edit") : _l("Add"), site_url('admin/view/form', 'view_listing_id=' . $view_listing_id));

		//Load Information from POST or DB
		$view_listing = $_POST;

		if ($view_listing_id) {
			$view_listing += $this->Model_ViewListing->getRecord($view_listing_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'view_listing_id' => $view_listing_id,
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
		if ($view_listing_id = $this->Model_ViewListing->save(_request('view_listing_id'), $_POST)) {
			message('success', _l("The View has been saved"));
			message('data', array('view_listing_id' => $view_listing_id));
		} else {
			message('error', $this->Model_ViewListing->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/view/form', 'view_listing_id=' . _request('view_listing_id'));
		} else {
			redirect('admin/view');
		}
	}

	public function delete()
	{
		if ($this->Model_ViewListing->remove(_get('view_listing_id'))) {
			message('notify', _l("The View Listing was removed."));
		} else {
			message('error', $this->Model_ViewListing->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/view');
		}
	}

	public function save_data()
	{
		$view_id = _post('view_id');

		if (!$view_id) {
			message('error', _l("There was no view to save this image for"));
		} else {
			$name = slug(_post('name', date('Y-m-d')));

			$name = slug($name);

			$img = str_replace('data:image/png;base64,', '', _post('img'));

			if ($img) {
				$dir = DIR_IMAGE . 'charts/';

				$img  = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				$file = $dir . $name . '.png';

				_is_writable(dirname($file));

				$count = 1;

				while (file_exists($file)) {
					$file = $dir . $name . '-' . $count++ . '.png';
				}

				if (!file_put_contents($file, $data)) {
					message('error', _l("Failed to save file. Try another name."));
				} else {

					$img_url = str_replace(DIR_IMAGE, URL_IMAGE, $file);

					if (!$this->Model_View->saveViewMeta($view_id, 'chart_image', $img_url)) {
						message('error', $this->Model_View->fetchError());
					} else {
						message('success', _l("The file was saved as <a target=\"_blank\" href=\"%s\">%s</a>", $img_url, basename($file)));
					}
				}

			} else {
				message('error', _l("There was no image data"));
			}
		}

		output_message();
	}

	public function batch_action()
	{
		$batch  = (array)_post('batch');
		$action = _post('action');
		$value  = _request('value');

		foreach ($batch as $view_listing_id) {
			switch ($action) {
				case 'delete':
					$this->Model_ViewListing->remove($view_listing_id);
					break;
			}
		}

		if ($this->Model_ViewListing->hasError()) {
			message('error', $this->Model_ViewListing->fetchError());
		} else {
			message('success', _l("The View Listings have been updated!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/view');
		}
	}
}
