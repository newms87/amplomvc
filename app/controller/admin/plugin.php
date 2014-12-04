<?php
class App_Controller_Admin_Plugin extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Plugins"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Plugins"), site_url('admin/plugin'));

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

		$plugin_total = $this->Model_Plugin->getTotalPlugins($filter);
		$plugins      = $this->Model_Plugin->getPlugins($sort + $filter);

		foreach ($plugins as &$plugin) {
			if ($plugin['installed']) {
				$plugin['actions'] = array();

				$version = $this->plugin->hasUpgrade($plugin['name']);

				if ($version) {
					$plugin['actions']['upgrade'] = array(
						'text' => _l("Upgrade to %s", $version),
						'href' => site_url('admin/plugin/upgrade', 'name=' . $plugin['name']),
					);
				} elseif ($this->plugin->hasChanges($plugin['name'])) {
					$plugin['actions']['add_changes'] = array(
						'text' => _l("Add Changes"),
						'href' => site_url('admin/plugin/upgrade', 'name=' . $plugin['name']),
					);
				}

				if ($this->Model_Plugin->canUninstall($plugin['name'])) {
					$plugin['actions']['uninstall'] = array(
						'text' => _l("Uninstall"),
						'href' => site_url('admin/plugin/uninstall', 'name=' . $plugin['name']),
					);
				} else {
					$plugin['actions']['error'] = array(
						'text' => _l("<b>Uninstall Dependencies:</b><br />%s", implode(',', $this->Model_Plugin->getDependentsList($plugin['name']))),
					);
				}

			} else {
				if ($this->Model_Plugin->canInstall($plugin['name'])) {
					$plugin['actions'] = array(
						'install' => array(
							'text' => _l("Install"),
							'href' => site_url('admin/plugin/install', 'name=' . $plugin['name']),
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
		output($this->render('plugin', $data));
	}

	public function form()
	{
		if (!isset($_GET['name'])) {
			message('warning', _l("Warning: There was no plugin found."));
			redirect('admin/plugin');
		}
		$plugin_name = $_GET['name'];

		$this->document->setTitle(_l("Plugins"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Plugins"), site_url('admin/plugin'));

		$data = $_POST;

		if (!IS_POST) {
			$data['plugin_data'] = $this->Model_Plugin->getPluginData($plugin_name);
		}

		$data['name'] = $plugin_name;

		$data['action'] = site_url('admin/plugin/save', 'name=' . $plugin_name);
		$data['cancel'] = site_url('admin/plugin');

		output($this->render('plugin_form', $data));
	}

	public function save()
	{
		$name = _get('name');

		if ($name && $this->Model_Plugin->save($name, $_POST['plugin_data'])) {
			message('success', _l("You have successfully updated the plugin %s!", $name));
			redirect('admin/plugin');
		} else {
			message('error', $this->Model_Plugin->getError());
		}

		post_redirect('admin/plugin/form', 'name=' . $name);
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

		redirect('admin/plugin');
	}

	public function uninstall()
	{
		if (isset($_GET['name'])) {
			$keep_data = isset($_GET['keep_data']) ? (int)$_GET['keep_data'] : true;

			$this->plugin->uninstall($_GET['name'], $keep_data);
		}

		redirect('admin/plugin');
	}

	public function upgrade()
	{
		if (!empty($_GET['name'])) {
			$version = $this->plugin->upgrade($_GET['name']);

			if ($version === true) {
				message('success', _l("The changes for plugin %s have been integrated.", $_GET['name']));
			} elseif ($version) {
				message('success', _l("The plugin %s was successfully upgraded to version %s!", $_GET['name'], $version));
			} else {
				message('error', $this->plugin->getError());
			}
		}

		redirect('admin/plugin');
	}
}