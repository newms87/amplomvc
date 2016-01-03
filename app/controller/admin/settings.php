<?php

class App_Controller_Admin_Settings extends Controller
{
	public function index($data = array())
	{
		//Page Head
		set_page_info('title', _l("Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));

		//Settings Items
		$data['widgets'] = $this->Model_Settings->getWidgets();

		//Render
		output($this->render('settings/list', $data));
	}

	public function restore_defaults()
	{
		$first_install = !option('AMPLO_VERSION');

		if ($this->Model_Settings->restoreDefaults()) {
			if ($first_install) {
				message('success', _l("Welcome to Amplo MVC! Your installation has been successfully installed so you're ready to get started."));
			} else {
				message('success', _l("The Default Settings have been restored."));
			}
		} else {
			message('error', $this->Model_Settings->fetchError());
		}

		redirect('admin');
	}

	public function clear_cache()
	{
		$tables = _post('cache_tables');

		clear_cache($tables);

		message('success', _l("The cache %s was successfully cleared!", $tables ? 'for ' . implode(',', $tables) : ''));

		if (isset($_GET['redirect'])) {
			redirect($_GET['redirect'] ?: get_last_page());
		} else {
			redirect('admin/settings');
		}
	}

	public function refresh_sprite_sheets()
	{
		$this->theme->refreshAllSpriteSheets();

		message('success', _l("The sprite cache has been cleared and will be regenerated next run."));


		if (isset($_GET['redirect'])) {
			redirect($_GET['redirect'] ?: get_last_page());
		} else {
			redirect('admin/settings');
		}
	}
}
