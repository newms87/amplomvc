<?php
class Admin_Controller_Extension_Plugin extends Controller
{

	public function index()
	{
		$this->language->load('extension/plugin');

		$this->document->setTitle(_l("Plugins"));

		$this->getList();
	}

	public function getList()
	{
		//The Template
		$this->template->load('extension/plugin');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Plugins"), $this->url->link('extension/plugin'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Plugin Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['version'] = array(
			'type'         => 'text',
			'display_name' => _l("Version"),
		);

		$columns['date'] = array(
			'type'         => 'date',
			'display_name' => _l("Date"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Title"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['author'] = array(
			'type'         => 'text',
			'display_name' => _l("Author"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['description'] = array(
			'type'         => 'text',
			'display_name' => _l("Description"),
			'filter'       => true,
		);

		$columns['link'] = array(
			'type'         => 'text',
			'display_name' => _l("Link"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['dependencies'] = array(
			'type'         => 'text',
			'display_name' => _l("Dependencies"),
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_status'),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
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
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/plugin/uninstall', 'name=' . $plugin['name']),
					);
				}
				else {
					$plugin['actions']['error'] = array(
						'text' => _l("<b>Uninstall Dependencies:</b><br />%s", implode(',', $this->Model_Setting_Plugin->getDependentsList($plugin['name']))),
					);
				}

			} else {
				if ($this->Model_Setting_Plugin->canInstall($plugin['name'])) {
					$plugin['actions'] = array(
						'install' => array(
							'text' => _l("Install"),
							'href' => $this->url->link('extension/plugin/install', 'name=' . $plugin['name']),
						)
					);
				} else {
					$plugin['actions'] = array(
						'error' => array(
							'text' => _l("<b>Install Dependencies:</b><br />%s", implode(', ', array_keys($plugin['dependencies'], false))),
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
		$this->data['limits'] = $this->sort->renderLimits();

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
			$this->message->add('warning', _l("Warning: There was no plugin found."));
			$this->url->redirect('extension/plugin');
		}
		$plugin_name = $_GET['name'];

		$this->document->setTitle(_l("Plugins"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Plugins"), $this->url->link('extension/plugin'));

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
			$this->message->add('warning', _l("Warning: There was no plugin found."));
			$this->url->redirect('extension/plugin');
		}

		$this->document->setTitle(_l("Plugins"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Setting_Plugin->updatePlugin($_GET['name'], $_POST['plugin_data']);

			$this->message->add('success', _l("You have successfully updated the plugins!"));

			$this->url->redirect('extension/plugin');
		}

		$this->getForm();
	}

	public function add_changes()
	{
		if (!empty($_GET['name'])) {
			$this->plugin->integrateChanges($_GET['name']);
		}

		$this->url->redirect('extension/plugin');
	}

	public function install()
	{
		if (isset($_GET['name'])) {
			$this->plugin->install($_GET['name']);
		}

		$this->url->redirect('extension/plugin');
	}

	public function uninstall()
	{
		if (isset($_GET['name'])) {
			$keep_data = isset($_GET['keep_data']) ? (int)$_GET['keep_data'] : true;

			$this->plugin->uninstall($_GET['name'], $keep_data);
		}

		$this->url->redirect('extension/plugin');
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'extension/plugin')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify plugins!");
		}

		$plugs = $_POST['plugin_data'];
		$name  = ucfirst($_GET['name']);

		foreach ($plugs as $p) {

		}

		return $this->error ? false : true;
	}
}
