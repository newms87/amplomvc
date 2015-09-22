<?php

class App_Controller_Admin_Page extends App_Controller_Table
{
	protected $model = array(
		'title' => 'Page',
		'class' => 'App_Model_Page',
		'path'  => 'admin/page',
		'label' => 'name',
		'value' => 'page_id',
	);

	public function index()
	{
		//Page Head
		set_page_info('title', _l("Page"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page"), site_url('admin/page'));

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable"),
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

		//Render
		output($this->render('page/list', $data));
	}

	public function listing($options = array())
	{
		$disallow = array(
			'content' => false,
			'style'   => false,
			'options' => false,
		);

		$options += array(
			'sort_default' => array('title' => 'ASC'),
			'columns'      => $disallow,
			'extra_cols'   => $disallow + $this->instance->getColumns(),
			'actions'      => array(
				'view' => array(
					'text'    => _l("View"),
					'path'    => 'page',
					'#target' => "_blank",
				),
			),
			'filter'       => array('type' => 'page'),
		);

		return parent::listing($options);
	}

	public function form($defaults = array())
	{
		//Page Head
		set_page_info('title', _l($this->model['title']));

		//Insert or Update
		$page_id = _get('page_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l($this->model['title']), site_url($this->model['path']));
		breadcrumb($page_id ? _l("Edit") : _l("New"), site_url($this->model['path'] . '/form', 'page_id=' . $page_id));

		//Load Information from POST or DB
		$page = $_POST;

		if ($page_id && !IS_POST) {
			$page = $this->Model_Page->getPage($page_id, false);

			$page['categories'] = $this->Model_Page->getCategories($page_id);
		}

		//Set Values or Defaults
		$defaults += array(
			'page_id'          => 0,
			'type'             => 'page',
			'name'             => 'new-page',
			'title'            => 'New ' . $this->model['title'],
			'author_id'        => user_info('user_id'),
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
			'date_published'   => '',
			'categories'       => array(),
		);

		$page += $defaults;

		$page['options'] += array(
			'show_title'       => 1,
			'show_breadcrumbs' => 1,
		);

		//Template Data
		$page['data_templates']  = $this->Model_Page->getTemplates();
		$page['data_layouts']    = $this->Model_Layout->getRecords(null, null, array('cache' => true));
		$page['data_authors']    = array('' => 'Anonymous') + $this->Model_Page->getAuthors();
		$page['data_categories'] = $this->Model_Category->getRecords(array('name' => 'ASC'), array('type' => 'page'), array('cache' => true));

		$page['model'] = $this->model;

		//Render
		output($this->render('page/form', $page));
	}

	public function batch_action()
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $page_id) {
			switch ($action) {
				case 'enable':
					$this->Model_Page->save($page_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Page->save($page_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Page->remove($page_id);
					break;

				case 'copy':
					$this->Model_Page->copyPage($page_id);
					break;
			}
		}

		if ($this->Model_Page->hasError()) {
			message('error', $this->Model_Page->fetchError());
		} else {
			message('success', _l("The pages have been updated!"));
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect($this->model['path']);
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

		$layouts = $this->Model_Layout->getRecords(array('name' => 'ASC'));

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
