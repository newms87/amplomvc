<?php
class Admin_Controller_Extension_Payment extends Controller
{
	private $extension_controller;

	public function index()
	{
		$this->language->load('extension/payment');

		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Payment->has($_GET['code'])) {
				$this->message->add('warning', $this->_('error_unknown_extension', $_GET['code']));

				$this->url->redirect($this->url->link('extension/payment'));
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	//TODO: Implement the Add / Delete functionality for Extensions
	public function delete()
	{
		$this->language->load('extension/payment');

		if (!empty($_GET['code']) && $this->validateDelete()) {
			$this->System_Extension_Payment->deleteExtension($_GET['code']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success_delete'));
			}

			$this->url->redirect($this->url->link('extension/payment', $this->url->getQueryExclude('name')));
		}

		$this->index();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Template
		$this->template->load('extension/payment_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('extension/payment'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_title'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_code'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => $this->_('column_sort_order'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_status'),
			'filter'       => true,
			'build_data'   => $this->_('data_statuses'),
			'sortable'     => true,
		);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('sort_order', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Table Row Data
		$extension_total = $this->System_Extension_Payment->getTotal($filter);
		$extensions      = $this->System_Extension_Payment->getFiltered($sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('extension/payment/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => $this->_('text_settings'),
						'href' => $this->url->link('extension/payment', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/payment/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/payment/install', 'code=' . $extension['code'])
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
		$this->data['limits'] = $this->sort->render_limit();

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

			$this->System_Extension_Payment->updateExtension($code, $_POST);

			$this->message->add('success', $this->_('text_settings_success', $code));

			$this->url->redirect($this->url->link('extension/payment'));
		}

		$extension = $this->System_Extension_Payment->get($code);

		//Page Head
		$this->document->setTitle($extension->getInfo('title'));
		$this->language->set('head_title', $extension->getInfo('title'));

		//Template and Language
		$this->template->load('extension/payment');
		$this->language->load('extension/payment');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_extension_list'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($extension->getInfo('title'), $this->url->link('extension/payment', 'code=' . $code));

		//Load Information
		if (!$this->request->isPost()) {
			$extension = $this->System_Extension_Payment->get($code)->getInfo();
		} else {
			$extension = $_POST;
		}

		$settings_defaults = array(
			'min_total'                => 0,
			'complete_order_status_id' => $this->config->get('config_order_complete_status_id'),
			'geo_zone_id'              => 0,
		);

		//Note: these in theory should be worthless, all data is set in System_Extension_Extension::getExtensions() method
		$defaults = array(
			'settings'   => array(),
			'sort_order' => 0,
			'status'     => 1,
		);

		$this->data += $extension + $defaults;
		$this->data['settings'] += $settings_defaults;

		//Get additional extension settings and profile data (this is the plugin part)

		if (method_exists($this->extension_controller, 'settings')) {
			$this->extension_controller->settings($this->data['settings']);
			$this->data['extend_settings'] = $this->extension_controller->output;
		}

		//Additional Data
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();
		$this->data['data_geo_zones']      = array(0 => $this->_('text_all_zones')) + $this->Model_Localisation_GeoZone->getGeoZones();

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/payment', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/payment');

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
		$this->language->load('extension/payment');

		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/payment/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', $this->_('error_extension_file', $file));
			$this->url->redirect($this->url->link('extension/payment'));
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', $this->_('text_edit_success', $file));
			} else {
				$this->message->add('warning', $this->_('error_edit_fail', $file));
			}

			$this->url->redirect($this->url->link('extension/payment'));
		}

		//Load extension
		$extension = $this->System_Extension_Payment->get($code)->getInfo();

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_extension_list'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($extension['title'], $this->url->link('extension/payment/edit', 'code=' . $code));

		//Load Contents
		$this->data['contents'] = file_get_contents($file);

		//Additional Data
		$this->language->set('head_title', $this->_('text_editing', $extension['title'], $file));

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/payment/edit', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/payment');

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
			$action = new Action($this->registry, 'extension/payment/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->error['warning'] = $this->_('error_permission');
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
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}

	public function install()
	{
		$this->language->load('extension/payment');

		if ($this->System_Extension_Payment->install($_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array(
				'min_total'                => 0,
				'complete_order_status_id' => $this->config->get('config_order_complete_status_id'),
				'geo_zone_id'              => 0,
			);

			//Save Default Settings From Extension
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Payment->updateExtension($_GET['code'], array('settings' => $settings));

			$this->message->add('success', $this->_('text_install_success', $_GET['code']));
		}

		$this->url->redirect($this->url->link('extension/payment'));
	}

	public function uninstall()
	{
		$this->language->load('extension/payment');

		if ($this->System_Extension_Payment->uninstall($_GET['code'])) {
			$this->message->add('notify', $this->_('text_uninstall_success', $_GET['code']));
		}


		$this->url->redirect($this->url->link('extension/payment'));
	}
}
