<?php

class Admin_Controller_Page_Page extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['page_id'])) {
				$page_id = $this->Model_Page_Page->addPage($_POST);

				if ($this->request->isAjax()) {
					$this->response->setOutput(json_encode(array('redirect' => $this->url->link('page/page', 'page_id=' . $page_id))));
					return;
				}
			} //Update
			else {
				$this->Model_Page_Page->editPage($_GET['page_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Page was updated successfully!"));

				if (!$this->request->isAjax()) {
					$this->url->redirect('page/page');
				}
			}
		}

		if ($this->request->isAjax()) {
			$json = $this->error ? array('error' => $this->error) : $this->message->fetch();
			$this->response->setOutput(json_encode($json));
		} else {
			$this->getForm();
		}
	}

	public function delete()
	{
		if (isset($_GET['page_id']) && $this->validateDelete()) {
			$this->Model_Page_Page->deletePage($_GET['page_id']);

			if (!$this->message->hasError()) {
				$this->message->add('notify', _l("Page was deleted!"));
			}
		}

		$this->url->redirect('page/page');
	}

	public function batch_update()
	{
		if (isset($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $page_id) {
				switch ($_GET['action']) {
					case 'enable':
						$this->Model_Page_Page->update_field($page_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Page_Page->update_field($page_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Page_Page->deletePage($page_id);
						break;
					case 'copy':
						$this->Model_Page_Page->copyPage($page_id);
						break;
					default:
						break 2; //Exit the For Loop
				}
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified Pages!"));
			}
		}

		$this->url->redirect('page/page');
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//The Template
		$this->view->load('page/page_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page"), $this->url->link('page/page'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Page Title"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['stores'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Stores"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $this->Model_Setting_Store->getStores(),
			'sortable'     => false,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('title', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$page_total = $this->Model_Page_Page->getTotalPages($filter);
		$pages      = $this->Model_Page_Page->getPages($sort + $filter);

		$url_query = $this->url->getQueryExclude('page_id');

		foreach ($pages as &$page) {
			$page['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('page/page/update', 'page_id=' . $page['page_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('page/page/delete', 'page_id=' . $page['page_id'] . '&' . $url_query)
				)
			);

			$page['stores'] = $this->Model_Page_Page->getPageStores($page['page_id']);
		}
		unset($page);

		//Build The Table
		$tt_data = array(
			'row_id' => 'page_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($pages);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
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

		$this->data['batch_update'] = 'page/page/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $page_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('page/page/update');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//The Template
		$this->view->load('page/page_form');

		//Insert or Update
		$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page"), $this->url->link('page/page'));

		if ($page_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('page/page/update', 'page_id=' . $page_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('page/page/update'));
		}

		//Load Information from POST or DB
		if ($this->request->isPost()) {
			$page_info = $_POST;
		} elseif ($page_id) {
			$page_info = $this->Model_Page_Page->getPage($page_id);

			$page_info['stores'] = $this->Model_Page_Page->getPageStores($page_id);
		} else {
			$page_info = array();
		}

		//Set Values or Defaults
		$defaults = array(
			'title'            => 'New Page',
			'alias'            => '',
			'content'          => '',
			'css'              => '',
			'meta_keywords'    => '',
			'meta_description' => '',
			'display_title'    => 1,
			'layout_id'        => 0,
			'stores'           => array(0),
			'blocks'           => array(),
			'status'           => 1,
			'translations'     => array(),
		);

		$this->data += $page_info + $defaults;

		$_POST = $this->data;
		//$this->loadBlocks();

		//Template Data
		$this->data['data_stores']  = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts();

		$this->data['url_blocks']        = $this->url->link('block/block');
		$this->data['url_create_layout'] = $this->url->link('page/page/create_layout');
		$this->data['url_load_blocks']   = $this->url->link('page/page/loadBlocks');

		$store_front                = current($this->data['stores']);
		$this->data['page_preview'] = $this->url->store($store_front['store_id'], 'page/page/preview', 'page_id=' . $page_id);

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$this->data['save']   = $this->url->link('page/page/update', 'page_id=' . $page_id);
		$this->data['cancel'] = $this->url->link('page/page');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function create_layout()
	{
		if (!empty($_POST['name'])) {
			$layout = array(
				'name' => $_POST['name'],
			);

			$result = $this->Model_Design_Layout->getLayouts($layout);

			if (empty($result)) {
				$layout_id = $this->Model_Design_Layout->addLayout($layout);
			} else {
				$result    = current($result);
				$layout_id = $result['layout_id'];
			}
		}

		$sort = array(
			'sort'  => 'name',
			'order' => "ASC",
		);

		$layouts = $this->Model_Design_Layout->getLayouts($sort);

		$this->builder->setConfig('layout_id', 'name');

		$this->response->setOutput($this->builder->build('select', $layouts, 'layout_id', $layout_id));
	}

	public function loadBlocks()
	{
		$blocks = array();

		if (!empty($_POST['layout_id']) && !empty($_POST['stores'])) {
			$filter = array(
				'layouts' => array($_POST['layout_id']),
				'stores'  => $_POST['stores'],
				'status'  => 1,
			);

			$block_list = $this->block->getBlocks($filter);

			$blocks = array();

			$data_positions = $this->theme->getSetting('data_positions');

			foreach ($block_list as $block) {
				foreach ($block['profiles'] as $profile) {
					foreach ($profile['store_ids'] as $store_id) {
						$blocks[] = array(
							'path'       => $block['path'],
							'name'       => $block['name'],
							'position'   => $data_positions[$profile['position']],
							'store_id'   => $store_id,
							'store_name' => $this->Model_Setting_Store->getStoreName($store_id),
						);
					}
				}
			}
		}

		$this->response->setOutput(json_encode($blocks));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'page/page')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Pages!");
		}

		if (!$this->validation->text($_POST['title'], 3, 64)) {
			$this->error['title'] = _l("Page Title must be between 3 and 64 characters!");
		}

		if (empty($_POST['display_title'])) {
			$_POST['display_title'] = 0;
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'page/page')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Pages!");
		}

		return $this->error ? false : true;
	}
}
