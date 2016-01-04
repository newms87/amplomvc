<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Plugin extends Controller
{
	public function index()
	{
		set_page_info('title', _l("Plugins"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Plugins"), site_url('admin/plugin'));

		//Render
		output($this->render('plugin/list'));
	}

	public function listing()
	{
		$sort    = (array)_request('sort', array('name' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_Plugin->getColumns((array)_request('columns')),
		);

		list($plugins, $total) = $this->Model_Plugin->getPlugins($sort, $filter, $options, true);

		foreach ($plugins as &$plugin) {
			if ($plugin['installed']) {
				$plugin['actions'] = array();

				$version = $this->plugin->hasUpgrade($plugin['name']);

				if ($version) {
					$plugin['actions']['upgrade'] = array(
						'text' => _l("Upgrade to %s", $version),
						'href' => site_url('admin/plugin/upgrade', 'name=' . $plugin['name']),
					);
				} else {
					$plugin['actions']['update'] = array(
						'text' => _l("Update"),
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

		$listing = array(
			'extra_cols'     => $this->Model_Plugin->getColumns(),
			'records'        => $plugins,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total' => $total,
			'listing_path'   => 'admin/plugin/listing',
		);

		$output = block('widget/listing', null, $listing + $options);

		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		if (!isset($_GET['name'])) {
			message('warning', _l("Warning: There was no plugin found."));
			redirect('admin/plugin');
		}
		$plugin_name = $_GET['name'];

		set_page_info('title', _l("Plugins"));

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
			message('error', $this->Model_Plugin->fetchError());
		}

		post_redirect('admin/plugin/form', 'name=' . $name);
	}

	public function install()
	{
		if ($this->plugin->install(_get('name'))) {
			message('success', _l("Plugin %s has been installed!", _get('name')));
		} else {
			message('error', $this->plugin->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/plugin');
		}
	}

	public function uninstall()
	{
		if ($this->plugin->uninstall(_get('name'), _get('keep_data', true))) {
			message('success', _l("Plugin %s has been uninstalled.", _get('name')));
		} else {
			message('error', $this->plugin->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/plugin');
		}
	}

	public function upgrade()
	{
		$name = _get('name');

		$version = $this->plugin->upgrade($name);

		$errors = $this->plugin->fetchError();

		if ($version === true) {
			message('success', _l("The changes for plugin %s have been integrated.", $name));
		} elseif ($version) {
			message('success', _l("The plugin %s was successfully upgraded to version %s!", $name, $version));
		} else {
			message('error', $errors);
		}

		if ($this->plugin->changes) {
			foreach ($this->plugin->changes as $file) {
				message('success', _l("Added file %s", str_replace(DIR_SITE, '', $file)));
			}
		}

		if ($version && $errors) {
			message('error', _l("There were some problems during the upgrade. Only a partial update has been applied"));
			message('error', $errors);
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/plugin');
		}
	}

	public function find()
	{
		$data = $_POST;

		$defaults = array(
			'search' => '',
			'team'   => 'amplomvc',
		);

		$data += $defaults;

		$plugins = $this->Model_Plugin->searchPlugins($data['search'], $data['team']);

		$data['plugins'] = $plugins;

		output($this->render('plugin/find', $data));
	}

	public function download()
	{
		$name = $this->Model_Plugin->downloadPlugin(_request('name'));

		if ($name) {
			if ($this->plugin->install($name)) {
				message('success', _l("The %s plugin has been installed!", $name));
			} else {
				message('error', _l("The %s plugin has been downloaded, but failed to install.", $name));
				message('error', $this->plugin->fetchError());
			}
		} else {
			message('error', $this->Model_Plugin->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/plugin');
		}
	}
}
