<?php
class Admin_Controller_Extension_Total extends Controller
{
	private $extension_controller;

	public function index()
	{
		$this->language->load('extension/total');

		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Total->has($_GET['code'])) {
				$this->message->add('warning', _l("The extension %s does not exist!", $_GET['code']));

				$this->url->redirect('extension/total');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	//TODO: Implement the Add / Delete functionality for Extensions
	public function delete()
	{
		$this->language->load('extension/total');

		if (!empty($_GET['code']) && $this->validateDelete()) {
			$this->System_Extension_Total->deleteExtension($_GET['code']);

			if (!$this->message->hasError()) {
				$this->message->add('success', $this->_('text_success_delete'));
			}

			$this->url->redirect('extension/total', $this->url->getQueryExclude('name'));
		}

		$this->index();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Order Totals"));

		//Template
		$this->template->load('extension/total_list');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals"), $this->url->link('extension/total'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Label"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => _l("Extension Code"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => _l("Sort Order"),
			'filter'       => true,
			'sortable'     => true,
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

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('sort_order', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Table Row Data
		$extension_total = $this->System_Extension_Total->getTotal($filter);
		$extensions      = $this->System_Extension_Total->getFiltered($sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('extension/total/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => _l("Settings"),
						'href' => $this->url->link('extension/total', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/total/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => _l("Install"),
						'href' => $this->url->link('extension/total/install', 'code=' . $extension['code'])
					),
				);
			}

			$extension['actions'] = $actions;
		}
		unset($extension);

		//Build The Table

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($extensions);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('extension/add');

		//Render limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $extension_total;

		$this->data['pagination'] = $this->pagination->render();

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
		$code = $_GET['code'];

		$this->loadExtensionController($code);

		if ($this->request->isPost() && $this->validate()) {
			//If Extension needs to customize the way data is stored
			if (method_exists($this->extension_controller, 'saveSettings')) {
				$this->extension_controller->saveSettings($_POST['settings']);
			}

			$this->System_Extension_Total->updateExtension($code, $_POST);

			$this->message->add('success', _l("Successfully saved settings for %s!", $code));

			$this->url->redirect('extension/total');
		}

		if (!$this->request->isPost()) {
			$extension = $this->System_Extension_Total->get($code)->getInfo();
		} else {
			$extension = $_POST;

		}

		//Page Head
		$this->document->setTitle($extension['title']);
		$this->data['page_title'] = $extension['title'];

		//Template and Language
		$this->template->load('extension/total');
		$this->language->load('extension/total');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals Extensions"), $this->url->link('extension/total'));
		$this->breadcrumb->add($extension['title'], $this->url->link('extension/total', 'code=' . $code));

		$defaults = array(
			'title'      => '',
			'settings'   => array(),
			'sort_order' => 0,
			'status'     => 1,
		);

		$this->data += $extension + $defaults;

		//Get additional extension settings and profile data (this is the plugin part)
		if (method_exists($this->extension_controller, 'settings')) {
			$this->extension_controller->settings($this->data['settings']);
			$this->data['extend_settings'] = $this->extension_controller->output;
		}

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/total', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/total');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function edit()
	{
		//Language
		$this->language->load('extension/total');

		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/total/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', _l("The extension file %s does not exist!", $file));
			$this->url->redirect('extension/total');
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', _l("Saved the extension file %s!", $file));
			} else {
				$this->message->add('warning', _l("There was a problem while saving the file %s!", $file));
			}

			$this->url->redirect('extension/total');
		}

		//Load extension
		$extension = $this->System_Extension_Total->get($code)->getInfo();

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals Extensions"), $this->url->link('extension/total'));
		$this->breadcrumb->add($extension['title'], $this->url->link('extension/total/edit', 'code=' . $code));

		//Load Contents
		$this->data['contents'] = file_get_contents($file);

		//Additional Data
		$this->data['page_title'] = $extension['title'];
		$this->data['edit_file']  = $file;

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/total/edit', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/total');

		//Template
		$this->template->load('extension/edit');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function loadExtensionController($code)
	{
		if (!$this->extension_controller && !empty($code)) {
			$action = new Action($this->registry, 'extension/total/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'extension/total')) {
			$this->error['warning'] = _l("You do not have permission to modify the Totals system extension!");
		}

		if (method_exists($this->extension_controller, 'validate')) {
			if (!$this->extension_controller->validate()) {
				return false;
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'extension/total')) {
			$this->error['warning'] = _l("You do not have permission to modify the Totals system extension!");
		}

		return $this->error ? false : true;
	}

	public function install()
	{
		$this->language->load('extension/total');

		if ($this->System_Extension_Total->install($_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array();

			//Save Default Settings
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Total->updateExtension($_GET['code'], array('settings' => $settings));

			$this->message->add('success', _l("Successfully installed the %s extension for Order Totals", $_GET['code']));
		} elseif ($this->System_Extension_Total->hasError()) {
			$this->message->add('warning', $this->System_Extension_Total->getError());
		}

		$this->url->redirect('extension/total');
	}

	public function uninstall()
	{
		$this->language->load('extension/total');

		if ($this->System_Extension_Total->uninstall($_GET['code'])) {
			$this->message->add('notify', _l("Uninstalled the %s extension for Order Totals.", $_GET['code']));
		}


		$this->url->redirect('extension/total');
	}
}
