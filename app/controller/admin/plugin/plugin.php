<?php
class App_Controller_Admin_Plugin_Plugin extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Plugins"));

		$this->getList();
	}

	public function getList()
	{
		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Plugins"), site_url('admin/plugin/plugin'));

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
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		$plugin_total = $this->Model_Setting_Plugin->getTotalPlugins($filter);
		$plugins      = $this->Model_Setting_Plugin->getPlugins($sort + $filter);

		$all_plugins = $this->Model_Setting_Plugin->getPlugins();

		foreach ($plugins as &$plugin) {
			if ($plugin['installed']) {
				$plugin['actions'] = array( /*'edit' => array(
						'text' => _l("Edit"),
						'href' => site_url('admin/plugin/plugin/update', 'name=' . $plugin['name']),
					),*/
				);

				if ($this->plugin->hasChanges($plugin['name'])) {
					$plugin['actions']['add_changes'] = array(
						'text' => _l("Add Changes"),
						'href' => site_url('admin/plugin/plugin/add_changes', 'name=' . $plugin['name']),
					);
				}

				if ($this->Model_Setting_Plugin->canUninstall($plugin['name'])) {
					$plugin['actions']['uninstall'] = array(
						'text' => _l("Uninstall"),
						'href' => site_url('admin/plugin/plugin/uninstall', 'name=' . $plugin['name']),
					);
				} else {
					$plugin['actions']['error'] = array(
						'text' => _l("<b>Uninstall Dependencies:</b><br />%s", implode(',', $this->Model_Setting_Plugin->getDependentsList($plugin['name']))),
					);
				}

			} else {
				if ($this->Model_Setting_Plugin->canInstall($plugin['name'])) {
					$plugin['actions'] = array(
						'install' => array(
							'text' => _l("Install"),
							'href' => site_url('admin/plugin/plugin/install', 'name=' . $plugin['name']),
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
				}
				unset($installed);

				$plugin['dependencies'] = implode('<br />', $plugin['dependencies']);
			}

			$plugin['link'] = "<a target=\"_blank\" href=\"$plugin[link]\">$plugin[link]</a>";

		}
		unset($plugin);

		//Build The Table
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($plugins);
		$this->table->mapAttribute('filter_value', $filter);

		$data['list_view'] = $this->table->render();

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $plugin_total;

		$data['pagination'] = $this->pagination->render();

		//Render
		output($this->render('plugin/plugin', $data));
	}

	public function getForm()
	{
		if (!isset($_GET['name'])) {
			message('warning', _l("Warning: There was no plugin found."));
			redirect('admin/plugin/plugin');
		}
		$plugin_name = $_GET['name'];

		$this->document->setTitle(_l("Plugins"));

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Plugins"), site_url('admin/plugin/plugin'));

		if (isset($_POST['plugin_data'])) {
			$data['plugin_data'] = $_POST['plugin_data'];
		} else {
			$data['plugin_data'] = $this->Model_Setting_Plugin->getPluginData($plugin_name);
		}

		$data['name'] = $plugin_name;

		$data['action'] = site_url('admin/plugin/plugin/update', 'name=' . $plugin_name);
		$data['cancel'] = site_url('admin/plugin/plugin');

		output($this->render('plugin/plugin_form', $data));
	}

	public function update()
	{
		$this->cache->delete('model');

		if (!isset($_GET['name'])) {
			message('warning', _l("Warning: There was no plugin found."));
			redirect('admin/plugin/plugin');
		}

		$this->document->setTitle(_l("Plugins"));

		if (IS_POST && $this->validateForm()) {
			$this->Model_Setting_Plugin->updatePlugin($_GET['name'], $_POST['plugin_data']);

			message('success', _l("You have successfully updated the plugins!"));

			redirect('admin/plugin/plugin');
		}

		$this->getForm();
	}

	public function add_changes()
	{
		if (!empty($_GET['name'])) {
			$this->plugin->integrateChanges($_GET['name']);
		}

		redirect('admin/plugin/plugin');
	}

	public function install()
	{
		if (!empty($_GET['name'])) {
			if (!$this->plugin->install($_GET['name'])) {
				message('error', $this->plugin->getError());
			} else {
				message('success', _l("%s was successfully installed!", $_GET['name']));
			}
		}

		redirect('admin/plugin/plugin');
	}

	public function uninstall()
	{
		if (isset($_GET['name'])) {
			$keep_data = isset($_GET['keep_data']) ? (int)$_GET['keep_data'] : true;

			$this->plugin->uninstall($_GET['name'], $keep_data);
		}

		redirect('admin/plugin/plugin');
	}

	private function validateForm()
	{
		if (!user_can('modify', 'plugin/plugin')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify plugins!");
		}

		$plugs = $_POST['plugin_data'];
		$name  = ucfirst($_GET['name']);

		foreach ($plugs as $p) {

		}

		return empty($this->error);
	}
}
