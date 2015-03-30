<?php

class App_Controller_Admin_Page extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Page"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page"), site_url('admin/page'));

		//Batch Actions
		if (user_can('w', 'admin/page/batch_action')) {
			$actions = array(
				'enable'  => array(
					'label' => _l("Enable")
				),
				'disable' => array(
					'label' => _l("Disable"),
				),
				'delete'  => array(
					'label' => _l("Delete"),
				),
			);

			$data['batch_action'] = array(
				'actions' => $actions,
				'path'    => site_url('admin/page/batch-action'),
			);
		}

		$data['view_listing_id'] = $this->Model_Page->getViewListingId();

		//Render
		output($this->render('page/list', $data));
	}

	public function listing($listing = array())
	{
		//The Table Columns
		$columns = $this->Model_Page->getColumns(_request('columns'));

		//Disallow content and style columns
		$disallow = array(
			'content' => 1,
			'style'   => 1,
		);

		$columns = array_diff_key($columns, $disallow);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('title', 'ASC');
		$filter = _get('filter', array());

		list($pages, $page_total) = $this->Model_Page->getRecords($sort, $filter, $columns, true, 'page_id');

		foreach ($pages as $page_id => &$page) {
			$actions = array();

			if (user_can('r', 'admin/page/form')) {
				$actions['edit'] = array(
					'text' => _l("Edit"),
					'href' => site_url('admin/page/form', 'page_id=' . $page_id)
				);
			}

			if (user_can('w', 'admin/page/delete')) {
				$actions['delete'] = array(
					'text' => _l("Delete"),
					'href' => site_url('admin/page/delete', 'page_id=' . $page_id)
				);
			}

			$page['actions'] = $actions;
		}
		unset($page);

		$listing += array(
			'row_id'         => 'page_id',
			'columns'        => $columns,
			'extra_cols'     => array_diff_key($this->Model_Page->getColumns(), $disallow),
			'rows'           => $pages,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $page_total,
			'listing_path'   => 'admin/page/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Page"));

		//Insert or Update
		$page_id = _get('page_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page"), site_url('admin/page'));
		breadcrumb($page_id ? _l("Edit") : _l("Add"), site_url('admin/page/form', 'page_id=' . $page_id));

		//Load Information from POST or DB
		$page = $_POST;

		if ($page_id && !IS_POST) {
			$page = $this->Model_Page->getRecord($page_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'theme'            => option('config_default_theme', AMPLO_DEFAULT_THEME),
			'name'             => '',
			'title'            => 'New Page',
			'alias'            => '',
			'content'          => '',
			'style'            => '',
			'meta_keywords'    => '',
			'meta_description' => '',
			'options'          => array(),
			'template'         => '',
			'layout_id'        => 0,
			'blocks'           => array(),
			'status'           => 1,
			'translations'     => array(),
		);

		$page += $defaults;

		$page['options'] += array(
			'show_title'       => 1,
			'show_breadcrumbs' => 1,
		);

		//Template Data
		$page['data_templates'] = $this->Model_Page->getTemplates();
		$page['data_layouts']   = $this->Model_Layout->getRecords(array('cache' => true));
		$page['data_themes']    = $this->theme->getThemes();

		$page['url_create_layout'] = site_url('admin/page/create-layout');

		//Must set query inline, to bypass URL Aliasing.
		$page['page_preview'] = site_url('page/preview_content?page_id=' . $page_id);

		$page['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$page['save'] = site_url('admin/page/save', 'page_id=' . $page_id);

		//Render
		output($this->render('page/form', $page));
	}

	public function save()
	{
		$post = $_POST;
		$post['t'] = $post['template'];

		if ($page_id = $this->Model_Page->save(_request('page_id'), $post)) {
			message('success', _l("The Page has been saved!"));
			message('data', array('page_id' => $page_id));

			if ($this->Model_Page->hasError()) {
				message('notify', $this->Model_Page->getError());
			}
		} else {
			message('error', $this->Model_Page->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/page/form', 'page_id=' . _request('page_id'));
		} else {
			redirect('admin/page');
		}
	}

	public function delete()
	{
		if ($this->Model_Page->deletePage(_get('page_id'))) {
			message('success', _l("The Page was deleted!"));
		} else {
			message('error', $this->Model_Page->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/page');
		}
	}

	public function batch_action()
	{
		$batch = (array)_request('batch');
		$action = _request('action');
		$value = _request('value');

		foreach ($batch as $page_id) {
			switch ($action) {
				case 'enable':
					$this->Model_Page->update_field($page_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Page->update_field($page_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Page->deletePage($page_id);
					break;

				case 'copy':
					$this->Model_Page->copyPage($page_id);
					break;
			}
		}

		if ($this->Model_Page->hasError()) {
			message('error', $this->Model_Page->getError());
		} else {
			message('success', _l("The pages have been updated!"));
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect('admin/page');
		}
	}

	public function create_layout()
	{
		if (!empty($_POST['name'])) {
			$layout = array(
				'name' => $_POST['name'],
			);

			$result = $this->Model_Layout->getRecords(null, $layout);

			if (empty($result)) {
				$layout_id = $this->Model_Layout->save(null, $layout);
			} else {
				$result    = current($result);
				$layout_id = $result['layout_id'];
			}
		}

		$sort = array(
			'sort'  => 'name',
			'order' => "ASC",
		);

		$layouts = $this->Model_Layout->getRecords($sort);

		$output = build(array(
			'type'   => 'select',
			'name'   => 'layout_id',
			'data'   => $layouts,
			'select' => $layout_id,
			'value'  => 'layout_id',
			'label'  => 'name',
		));

		output($output);
	}

	public function loadBlocks()
	{
		$filter = array(
			'layouts' => _post('layout_id'),
			'status'  => 1,
		);

		$block_list = $this->block->getBlocks($filter);

		$blocks = array();

		$data_positions = $this->theme->getPositions();

		foreach ($block_list as $block) {
			foreach ($block['profiles'] as $profile) {
				$blocks[] = array(
					'path'     => $block['path'],
					'name'     => $block['name'],
					'position' => $data_positions[$profile['position']],
				);
			}
		}

		output(json_encode($blocks));
	}
}
