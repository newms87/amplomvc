<?php
class Admin_Controller_Extension_Shipping extends Controller
{
	private $extension_controller;

	public function index()
	{
		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Shipping->has($_GET['code'])) {
				$this->message->add('warning', _l("The extension %s does not exist!", $_GET['code']));

				$this->url->redirect('extension/shipping');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	//TODO: Implement the Add / Delete functionality for Extensions
	public function delete()
	{
		if ($this->user->can('modify', 'extension/shipping')) {
			if (!empty($_GET['code'])) {
				$this->System_Extension_Shipping->deleteExtension($_GET['code']);

				if (!$this->message->hasError()) {
					$this->message->add('success', $this->_('text_success_delete'));
				}

				$this->url->redirect('extension/shipping', $this->url->getQueryExclude('name'));
			}
		} else {
			$this->error['permission'] = _l("You do not have permission to modify the Shipping extensions");
		}

		$this->index();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Shipping Extensions"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Shipping Title"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => _l("Code"),
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
		$extension_total = $this->System_Extension_Shipping->getTotal($filter);
		$extensions      = $this->System_Extension_Shipping->getFiltered($sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => _l("Edit"),
						'href' => $this->url->link('extension/shipping/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => _l("Settings"),
						'href' => $this->url->link('extension/shipping', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/shipping/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/shipping/install', 'code=' . $extension['code'])
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

		//The Template
		$this->template->load('extension/shipping_list');

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

			$this->System_Extension_Shipping->updateExtension($code, $_POST);

			$this->message->add('success', _l("You have successfully modified the %s extension!", $code));

			$this->url->redirect('extension/shipping');
		}

		$extension = $this->System_Extension_Shipping->get($code);

		$title = $extension->getInfo('title');

		//Page Head
		$this->document->setTitle($title);
		$this->data['head_title'] = $title;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($title, $this->url->link('extension/shipping', 'code=' . $code));

		//Load Information
		if (!$this->request->isPost()) {
			$extension = $this->System_Extension_Shipping->get($code)->getInfo();
		} else {
			$extension = $_POST;
		}

		//Note: these in theory should be worthless, all data is set in System_Extension_Extension::getExtensions() method
		$defaults = array(
			'settings'   => array(),
			'sort_order' => 0,
			'status'     => 1,
		);

		$this->data += $extension + $defaults;

		$settings_defaults = array(
			'min_total'                => 0,
			'complete_order_status_id' => $this->config->get('config_order_complete_status_id'),
			'geo_zone_id'              => 0,
		);

		$this->data['settings'] += $settings_defaults;

		//Get additional extension settings and profile data (this is the plugin part)
		if (method_exists($this->extension_controller, 'settings')) {
			$this->extension_controller->settings($this->data['settings']);
			$this->data['extend_settings'] = $this->extension_controller->output;
		}

		//Additional Data
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();
		$this->data['data_geo_zones']      = array(0 => _l("All Zones")) + $this->Model_Localisation_GeoZone->getGeoZones();

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/shipping', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/shipping');

		//The Template
		$this->template->load('extension/shipping');

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
		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/shipping/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', $this->_('error_extension_file', $file));
			$this->url->redirect('extension/shipping');
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', _l("Successfully modified %s", $file));
			} else {
				$this->message->add('warning', _l("Failed to save changes to %s", $file));
			}

			$this->url->redirect('extension/shipping');
		}

		//Load extension
		$extension = $this->System_Extension_Shipping->get($code)->getInfo();

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($extension['title'], $this->url->link('extension/shipping/edit', 'code=' . $code));

		//Load Contents
		$this->data['contents'] = file_get_contents($file);

		//Additional Data
		$this->data['head_title'] = _l("Editing %s: %s", $extension['title'], $file);

		//Action Buttons
		$this->data['save']   = $this->url->link('extension/shipping/edit', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('extension/shipping');

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
			$action = new Action($this->registry, 'extension/shipping/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'extension/shipping')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (method_exists($this->extension_controller, 'validate')) {
			if (!$this->extension_controller->validate()) {
				return false;
			}
		}

		return $this->error ? false : true;
	}

	public function install()
	{
		if ($this->System_Extension_Shipping->install($_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array(
				'min_total'   => 0,
				'geo_zone_id' => 0,
			);

			//Save Default Settings From Extension
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Shipping->updateExtension($_GET['code'], array('settings' => $settings));

			$this->message->add('success', _l("You have successfully installed %s!", $_GET['code']));
		} elseif ($this->System_Extension_Shipping->hasError()) {
			$this->message->add('error', $this->System_Extension_Shipping->getError());
		}

		$this->url->redirect('extension/shipping');
	}

	public function uninstall()
	{
		if ($this->System_Extension_Shipping->uninstall($_GET['code'])) {
			$this->message->add('notify', _l("%s has been uninstalled.", $_GET['code']));
		} elseif ($this->System_Extension_Shipping->hasError()) {
			$this->message->add('error', $this->System_Extension_Shipping->getError());
		}

		$this->url->redirect('extension/shipping');
	}
}
