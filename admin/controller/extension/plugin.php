<?php
class Admin_Controller_Extension_Plugin extends Controller
{

	public function index()
	{
		$this->language->load('extension/plugin');

		$this->document->setTitle($this->_('head_title'));

		$this->getList();
	}

	public function getList()
	{
		//The Template
		$this->template->load('extension/plugin');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('extension/plugin'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_name'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['version'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_version'),
		);

		$columns['date'] = array(
			'type'         => 'date',
			'display_name' => $this->_('column_date'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_title'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['author'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_author'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['description'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_description'),
			'filter'       => true,
		);

		$columns['link'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_link'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['dependencies'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_dependencies'),
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_status'),
			'filter'       => true,
			'build_data'   => $this->_('data_statuses'),
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$plugin_total = $this->Model_Setting_Plugin->getTotalPlugins($filter);
		$plugins = $this->Model_Setting_Plugin->getPlugins($sort + $filter);

		$all_plugins = $this->Model_Setting_Plugin->getPlugins();

		foreach ($plugins as &$plugin) {
			if ($plugin['installed']) {
				$plugin['actions'] = array(
					/*'edit' => array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('extension/plugin/update', 'name=' . $plugin['name']),
					),*/
				);

				if ($this->plugin->hasChanges($plugin['name'])) {
					$plugin['actions']['add_changes'] = array(
						'text' => $this->_('text_add_changes'),
						'href' => $this->url->link('extension/plugin/add_changes', 'name=' . $plugin['name']),
					);
				}

				if ($this->Model_Setting_Plugin->canUninstall($plugin['name'])) {
					$plugin['actions']['uninstall'] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/plugin/uninstall', 'name=' . $plugin['name']),
					);
				}
				else {
					$plugin['actions']['error'] = array(
						'text' => $this->_('error_uninstall_dependent', implode(',', $this->Model_Setting_Plugin->getDependentsList($plugin['name']))),
					);
				}

			} else {
				if ($this->Model_Setting_Plugin->canInstall($plugin['name'])) {
					$plugin['actions'] = array(
						'install' => array(
							'text' => $this->_('text_install'),
							'href' => $this->url->link('extension/plugin/install', 'name=' . $plugin['name']),
						)
					);
				} else {
					$plugin['actions'] = array(
						'error' => array(
							'text' => $this->_('error_install_dependent', implode(', ', array_keys($plugin['dependencies'], false))),
						),
					);
				}
			}

			if (!empty($plugin['dependencies'])) {
				foreach ($plugin['dependencies'] as $depend => &$installed) {
					if ($installed) {
						$installed = "<span class=\"dependency_active\">$depend</span>";
					} else {
						$installed = "<span class=\"dependency_inactive\">$depend</span>";
					}
				} unset($installed);

				$plugin['dependencies'] = implode('<br />', $plugin['dependencies']);
			}

			$plugin['link'] = "<a target=\"_blank\" href=\"$plugin[link]\">$plugin[link]</a>";

		} unset($plugin);

		//Build The Table
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($plugins);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $plugin_total;

		$this->data['pagination'] = $this->pagination->render();

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function getForm()
	{
		$this->template->load('extension/plugin_form');

		if (!isset($_GET['name'])) {
			$this->message->add('warning', $this->_('error_no_plugin'));
			$this->url->redirect($this->url->link('extension/plugin'));
		}
		$plugin_name = $_GET['name'];

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('extension/plugin'));

		if (isset($_POST['plugin_data'])) {
			$this->data['plugin_data'] = $_POST['plugin_data'];
		} else {
			$this->data['plugin_data'] = $this->Model_Setting_Plugin->getPluginData($plugin_name);
		}

		$this->data['name'] = $plugin_name;

		$this->data['action'] = $this->url->link('extension/plugin/update', 'name=' . $plugin_name);
		$this->data['cancel'] = $this->url->link('extension/plugin');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function update()
	{
		$this->cache->delete('model');

		$this->language->load('extension/plugin');

		if (!isset($_GET['name'])) {
			$this->message->add('warning', $this->_('error_no_plugin'));
			$this->url->redirect($this->url->link('extension/plugin'));
		}

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Setting_Plugin->updatePlugin($_GET['name'], $_POST['plugin_data']);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/plugin'));
		}

		$this->getForm();
	}

	public function add_changes()
	{
		if (!empty($_GET['name'])) {
			$this->plugin->integrateChanges($_GET['name']);
		}

		$this->url->redirect($this->url->link('extension/plugin'));
	}

	public function install()
	{
		if (isset($_GET['name'])) {
			$this->plugin->install($_GET['name']);
		}

		$this->url->redirect($this->url->link('extension/plugin'));
	}

	public function uninstall()
	{
		if (isset($_GET['name'])) {
			$keep_data = isset($_GET['keep_data']) ? (int)$_GET['keep_data'] : true;

			$this->plugin->uninstall($_GET['name'], $keep_data);
		}

		$this->url->redirect($this->url->link('extension/plugin'));
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/plugin')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		$plugs = $_POST['plugin_data'];
		$name  = ucfirst($_GET['name']);

		foreach ($plugs as $p) {

		}

		return $this->error ? false : true;
	}
}
