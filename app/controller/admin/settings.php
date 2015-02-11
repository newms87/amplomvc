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
		$this->Model_Settings->restoreDefaults();

		redirect('admin');
	}

	public function clear_cache()
	{
		$tables = _post('cache_tables');

		clear_cache($tables);

		message('success', _l("The cache %s was successfully cleared!", $tables ? 'for ' . implode(',', $tables) : ''));

		if (isset($_GET['redirect'])) {
			redirect(get_last_page());
		} else {
			redirect('admin/settings');
		}
	}
}
